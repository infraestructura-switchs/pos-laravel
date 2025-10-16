<?php

namespace App\Http\Livewire\Admin\Bills;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Rules\Date;
use App\Services\BillService;
use App\Services\DetailBillService;
use App\Services\DocumentTaxService;
use App\Services\FactusConfigurationService;
use App\Services\FactroConfigurationService;
use App\Services\TerminalService;
use App\Services\CloudinaryService;
use App\Traits\UtilityTrait;
use App\Utilities\CalcItemValues;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Create extends Component
{
    use UtilityTrait;

    public $paymentMethods;

    public $openChange = false;

    public $cash;

    public $products;

    public $customer = [];

    public $finance = 0;

    public $customerDefault;

    public $payment_method_id = '';

    public $due_date;

    public $observation;

    protected $validationAttributes = [
        'customer.id' => 'cliente',
        'products' => 'productos',
        'finance' => 'método de pago',
        'payment_method_id' => 'medio de pago',
    ];

    protected $messages = [
        'products.required' => 'Seleccione uno o más productos',
    ];

    protected function rules()
    {
        return [
            'customer.id' => 'required|integer|exists:customers,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.discount' => 'nullable|integer|min:0|max:999999999',
            'finance' => 'required|integer|min:0|max:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'due_date' => ['nullable', 'date', new Date],
            'observation' => 'nullable|string|max:1000',
        ];
    }

    public function mount()
    {
        $paymentMethods = PaymentMethod::where('status', PaymentMethod::ACTIVE)->get();
        $this->paymentMethods = $paymentMethods->pluck('name', 'id');
        $this->payment_method_id = $paymentMethods->where('default', '1')->count() ? $paymentMethods->where('default', '1')->first()->id : '';
        $this->products = collect();
        $this->customerDefault = Customer::find(1)->toArray();
    }

    public function render()
    {
        return view('livewire.admin.bills.create')->layoutData(['title' => 'Crear factura']);
    }

    public function openChange()
    {
        // $this->dispatchBrowserEvent('print-ticket', 100);
        // return;

        $this->due_date = $this->finance == 1 ? $this->due_date : null;
        $this->due_date = empty($this->due_date) ? null : $this->due_date;

        $this->resetValidation();
        $this->validate();

        $total = collect($this->products)->sum('total');

        if (intval($this->payment_method_id) === PaymentMethod::CASH && intval($this->finance) === 0) {
            return $this->dispatchBrowserEvent('open-change', $total);
        }

        $this->cash = $total;
        $this->store();
    }

    public function store()
    {
        file_put_contents(storage_path('logs/debug.log'), '=== STORE METHOD CALLED ===' . PHP_EOL, FILE_APPEND);
        $products = collect($this->products);
        $productsDB = BillService::getUniqueProductsDB(clone $products);
        BillService::addCostToItems($products, $productsDB);

        try {
            file_put_contents(storage_path('logs/debug.log'), 'Validating terminal and inventory...' . PHP_EOL, FILE_APPEND);
            TerminalService::verifyTerminal();
            BillService::validateInventory($products, $productsDB);
            BillService::calcTotales($products, $productsDB);
            $products = CalcItemValues::taxes($products);
            file_put_contents(storage_path('logs/debug.log'), 'Validation passed, starting transaction...' . PHP_EOL, FILE_APPEND);
        } catch (CustomException $ce) {
            file_put_contents(storage_path('logs/debug.log'), 'Validation error: ' . $ce->getMessage() . PHP_EOL, FILE_APPEND);
            return $this->emit('error', $ce->getMessage());
        }

        $lock = Cache::lock('createBill', 30);

        try {
            file_put_contents(storage_path('logs/debug.log'), 'Acquiring lock and starting transaction...' . PHP_EOL, FILE_APPEND);
            $lock->block(30);

            DB::beginTransaction();
            file_put_contents(storage_path('logs/debug.log'), 'Transaction started, creating bill...' . PHP_EOL, FILE_APPEND);

            $bill = BillService::store($products, $this->payment_method_id, $this->cash, $this->customer['id'], $this->observation, 0);

            foreach ($products as $product) {
                $detailBill = DetailBillService::store($bill, $product);
                BillService::updateUnitsOrStock($product, $productsDB);
                DocumentTaxService::calcTaxRatesForItems($detailBill, $product['tax_rates']);
            }

            DocumentTaxService::calcTaxRatesForDocument($bill);

            BillService::updateStock($productsDB);

            if ($this->finance === '1') {
                $bill->finance()->create(['due_date' => $this->due_date]);
            }

            DB::commit();
            file_put_contents(storage_path('logs/debug.log'), 'Bill created successfully: ' . $bill->id . PHP_EOL, FILE_APPEND);
        } catch (LockTimeoutException $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return $this->emit('error', 'Ha ocurrido un error inesperado. Vuelve a intentarlo');
        } catch (CustomException $ce) {
            DB::rollBack();

            return $this->emit('error', $ce->getMessage());
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage(), $products->toArray(), $th->getLine());

            return $this->emit('error', 'Ha sucedido un error inesperado. Vuelve a intentarlo');
        } finally {
            optional($lock)->release();
        }

        $this->products = collect([]);
        $this->customerDefault = Customer::find(1);
        $this->resetExcept('paymentMethods');
        $this->dispatchBrowserEvent('reset-properties-bill');
        $this->emitTo('admin.products.search', 'getProducts');
        
        file_put_contents(storage_path('logs/debug.log'), 'About to check electronic bill validation for bill: ' . $bill->id . PHP_EOL, FILE_APPEND);
        
        Log::info('About to check electronic bill validation for bill: ', ['bill_id' => $bill->id]);
       
        Log::info('Factus API enabled: ' , ['enabled' => FactusConfigurationService::isApiEnabled(),
        'factro_enabled' => FactroConfigurationService::isApiEnabled(),
        ]);
        if (FactusConfigurationService::isApiEnabled()
        || FactroConfigurationService::isApiEnabled()
        ) {
            if ($this->validateElectronicBill($bill)) {
                return;
            }
        }

        // Subir PDF a Cloudinary automáticamente
         Log::info('Starting PDF upload to Cloudinary for bill: ' . $bill->id);
       file_put_contents(storage_path('logs/debug.log'), 'About to call uploadPdfToCloudinary for bill: ' . $bill->id . PHP_EOL, FILE_APPEND);
       $this->uploadPdfToCloudinary($bill);

        $this->dispatchBrowserEvent('print-ticket', $bill->id);
    }

    public function validateElectronicBill(Bill $bill)
    {
        try {
            BillService::validateElectronicBill($bill);
        } catch (CustomException $ce) {
            Log::error($ce->getMessage());

            return $this->emit('error', $ce->getMessage());
        } catch (ValidationException $ce) {
            $errors = $ce->errors();
            foreach ($errors as $field => $errorMessages) {
                foreach ($errorMessages as $errorMessage) {
                    $this->addError($field, $errorMessage);
                }
            }

            return 'error';
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [], $th->getLine());

            return $this->emit('error', 'Ha sucedido un error inesperado. Vuelve a intentarlo');
        }
    }

    /**
     * Sube el PDF de la factura a Cloudinary automáticamente
     */
    private function uploadPdfToCloudinary(Bill $bill)
    {
        file_put_contents(storage_path('logs/debug.log'), 'uploadPdfToCloudinary called for bill: ' . $bill->id . PHP_EOL, FILE_APPEND);
        \Illuminate\Support\Facades\Log::info('uploadPdfToCloudinary called for bill: ' . $bill->id);
        
        try {
            // Usar el método del BillController para generar el PDF
            $controller = app(\App\Http\Controllers\Admin\BillController::class);
            $pdfBase64 = $controller->getBillBase64($bill->id);
            
            \Illuminate\Support\Facades\Log::info('PDF generated, size: ' . strlen($pdfBase64) . ' bytes');
            
            // Crear directorio temporal
            $tempDir = storage_path('app/tmp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0775, true);
            }
            
            $fileName = 'Factura_' . ($bill->number ?? $bill->id) . '.pdf';
            $filePath = $tempDir . DIRECTORY_SEPARATOR . $fileName;
            file_put_contents($filePath, base64_decode($pdfBase64));
            
            \Illuminate\Support\Facades\Log::info('PDF saved to: ' . $filePath);
            
            // Subir a Cloudinary
            $cloudinary = app(\App\Services\CloudinaryService::class);
            $upload = $cloudinary->uploadRaw($filePath, [
                'folder' => config('cloudinary.folder', 'pos-images') . '/pdfs',
                'public_id' => 'bill_' . ($bill->number ?? $bill->id) . '_' . time(),
                'resource_type' => 'raw',
            ]);
            
            // Limpiar archivo temporal
            unlink($filePath);
            
            \Illuminate\Support\Facades\Log::info('Cloudinary upload result: ' . json_encode($upload));
            
            if ($upload['success']) {
                $this->emit('success', 'PDF subido a Cloudinary: ' . ($upload['secure_url'] ?? $upload['url'] ?? 'URL no disponible'));
            } else {
                $this->emit('warning', 'Error al subir PDF a Cloudinary: ' . ($upload['error'] ?? 'Error desconocido'));
            }

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error subiendo PDF a Cloudinary: ' . $e->getMessage(), [
                'bill_id' => $bill->id,
                'trace' => $e->getTraceAsString()
            ]);
            $this->emit('warning', 'Error al subir PDF a Cloudinary: ' . $e->getMessage());
        }
    }
}
