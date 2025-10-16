<?php

namespace App\Http\Livewire\Admin\DirectSale;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\BillController;
use App\Models\Bill;
use App\Services\BillService;
use App\Services\DetailBillService;
use App\Services\DocumentTaxService;
use App\Services\CloudinaryService;
use App\Services\FactusConfigurationService;
use App\Services\FactroConfigurationService;
use App\Services\TerminalService;
use App\Utilities\CalcItemValues;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use App\Jobs\UploadBillPdfToCloudinary;

class Create extends Component
{
    public function render()
    {
        return view('livewire.admin.direct-sale.create')->layout('layouts.app');
    }

    public function storeBill(array $order, $cash, $tip, $paymentMethod)
    {
        // Log inicial
        Log::info('🚀 DirectSale::storeBill - Iniciando proceso de facturación', [
            'order_id' => $order['id'] ?? 'N/A',
            'customer_id' => $order['customer']['id'] ?? 'N/A',
            'products_count' => count($order['products'] ?? [])
        ]);

        if (! $order['is_available']) {
            Log::warning('⚠️ DirectSale::storeBill - Orden no disponible');
            return $this->emit('alert', 'La orden no está disponible');
        }

        $products = collect($order['products']);
        $productsDB = BillService::getUniqueProductsDB(clone $products);
        BillService::addCostToItems($products, $productsDB);

        try {
            Log::info('🔍 DirectSale::storeBill - Validando terminal e inventario');
            TerminalService::verifyTerminal();
            BillService::validateInventory($products, $productsDB);
            BillService::calcTotales($products, $productsDB);
            $products = CalcItemValues::taxes($products);
        } catch (CustomException $ce) {
            Log::error('❌ DirectSale::storeBill - Error en validación', ['error' => $ce->getMessage()]);
            return $this->emit('alert', $ce->getMessage());
        }

        $lock = Cache::lock('createBill', 30);

        try {
            $lock->block(30);

            DB::beginTransaction();
            Log::info('💾 DirectSale::storeBill - Creando factura en BD');

            $bill = BillService::store($products, $paymentMethod, $cash, $order['customer']['id'], '', $tip);

            foreach ($products as $product) {
                $detailBill = DetailBillService::store($bill, $product);
                BillService::updateUnitsOrStock($product, $productsDB);
                DocumentTaxService::calcTaxRatesForItems($detailBill, $product['tax_rates']);
            }

            DocumentTaxService::calcTaxRatesForDocument($bill);
            BillService::updateStock($productsDB);

            DB::commit();
            Log::info('✅ DirectSale::storeBill - Factura creada exitosamente', ['bill_id' => $bill->id]);
        } catch (LockTimeoutException $e) {
            Log::error($e);
            DB::rollBack();
            Log::error('❌ DirectSale::storeBill - Timeout en lock');
            return $this->emit('alert', 'Ha ocurrido un error inesperado. Vuelve a intentarlo');
            
        } catch (CustomException $ce) {
            DB::rollBack();
            Log::error('❌ DirectSale::storeBill - Error custom', ['error' => $ce->getMessage()]);
            return $this->emit('alert', $ce->getMessage());
            
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage(), $products->toArray(), $th->getLine());
            Log::error('❌ DirectSale::storeBill - Error inesperado', [
                'error' => $th->getMessage(),
                'line' => $th->getLine()
            ]);
            return $this->emit('alert', 'Ha ocurrido un error inesperado. Vuelve a intentarlo');
        } finally {
            optional($lock)->release();
        }

        // Verificar si la facturación electrónica está habilitada
        Log::info('🔌 DirectSale::storeBill - Verificando facturación electrónica');
        $electronicValidationFailed = false;
        
        if (FactusConfigurationService::isApiEnabled()
         || FactroConfigurationService::isApiEnabled()
        ) {
            Log::info('⚡ DirectSale::storeBill - Facturación electrónica HABILITADA, validando...');
            $validationResult = $this->validateElectronicBill($bill);
            
            if ($validationResult === 'error') {
                Log::warning('⚠️ DirectSale::storeBill - Error en validación de factura electrónica, continuando con factura normal');
                $electronicValidationFailed = true;
                // NO RETORNAR, continuar con el proceso normal
            } else {
                Log::info('✅ DirectSale::storeBill - Factura electrónica validada correctamente');
            }
        } else {
            Log::info('ℹ️ DirectSale::storeBill - Facturación electrónica NO habilitada');
        }

        // Despachar job en segundo plano para evitar timeouts en la UI
        Log::info('📤 DirectSale::storeBill - Despachando job para subir PDF a Cloudinary');
        UploadBillPdfToCloudinary::dispatchAfterResponse($bill->id);
        
        // Mensaje de éxito con advertencia si falló la facturación electrónica
        if ($electronicValidationFailed) {
            $this->emit('warning', 'Factura registrada, pero no se pudo validar como factura electrónica');
        } else {
            $this->emit('success', 'Factura registrada con éxito');
        }

        // ✅ Descarga/visualización del ticket
        // Pasar solo el id; el JS construye la URL con window.location.origin (evita mixed content)
        Log::info('📥 DirectSale::storeBill - Enviando evento para descarga de factura');
        $this->dispatchBrowserEvent('download-bill', ['id' => $bill->id]);
        
        return 'success'; // IMPORTANTE: mantener esto para que resetee el carrito
    }

    public function validateElectronicBill(Bill $bill)
    {
        Log::info('🔐 DirectSale::validateElectronicBill - Iniciando validación', ['bill_id' => $bill->id]);
        
        try {
            BillService::validateElectronicBill($bill);
            Log::info('✅ DirectSale::validateElectronicBill - Validación exitosa', [
                'bill_id' => $bill->id,
                'bill_number' => $bill->number
            ]);
            return 'success';
            
        } catch (CustomException $ce) {
            Log::error('❌ DirectSale::validateElectronicBill - Error custom', [
                'bill_id' => $bill->id,
                'error' => $ce->getMessage()
            ]);
            Log::error($ce->getMessage());
            // No mostrar error al usuario, solo registrar en logs
            // Retornar 'error' para que continúe con factura normal
            return 'error';
            
        } catch (ValidationException $ce) {
            $errors = $ce->errors();
            Log::error('❌ DirectSale::validateElectronicBill - Error de validación', [
                'bill_id' => $bill->id,
                'errors' => $errors
            ]);
            // No mostrar errores al usuario, solo registrar en logs
            return 'error';
            
        } catch (\Throwable $th) {
            Log::error('❌ DirectSale::validateElectronicBill - Error inesperado', [
                'bill_id' => $bill->id,
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString()
            ]);
            Log::error($th->getMessage(), [], $th->getLine());
            // No mostrar error al usuario, continuar con factura normal
            return 'error';
        }
    }
}