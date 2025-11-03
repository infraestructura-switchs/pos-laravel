<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\Company;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TaxRateController;
use App\Http\Controllers\Api\TributeController;
use App\Http\Controllers\Api\PresentationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TerminalController;
use Illuminate\Support\Facades\Route;
use App\Models\Bill;
use App\Services\CloudinaryService;
use App\Http\Controllers\Admin\BillController as AdminBillController;

// Rutas públicas (sin autenticación)
Route::post('/auth/login', [AuthController::class, 'login']);

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Products
    Route::prefix('products')->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::get('/{id}', [ProductController::class, 'getById']);
        Route::get('/', [ProductController::class, 'getByFilters']);
        Route::post('/import', [ProductController::class, 'import']);
        Route::get('/import/template', [ProductController::class, 'downloadTemplate']);
        Route::get('/export/excel', [ProductController::class, 'exportExcel']);

        // Product Images
        Route::prefix('images')->group(function () {
            Route::post('/upload-base64', [ProductImageController::class, 'uploadBase64']);
            Route::get('/{productId}', [ProductImageController::class, 'show']);
            Route::delete('/{productId}', [ProductImageController::class, 'destroy']);
        });
    });

    // Categories
    Route::prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::get('/{id}', [CategoryController::class, 'getById']);
        Route::get('/', [CategoryController::class, 'getByFilters']);
    });

    // Tax Rates
    Route::prefix('tax_rates')->group(function () {
        Route::post('/', [TaxRateController::class, 'create']);
        Route::put('/{id}', [TaxRateController::class, 'update']);
        Route::get('/{id}', [TaxRateController::class, 'getById']);
        Route::get('/', [TaxRateController::class, 'getByFilters']);
    });

    // Tribute
    Route::prefix('tributes')->group(function () {
        Route::post('/', [TributeController::class, 'create']);
        Route::put('/{id}', [TributeController::class, 'update']);
        Route::get('/{id}', [TributeController::class, 'getById']);
        Route::get('/', [TributeController::class, 'getByFilters']);
    });

    // Presentations
    Route::prefix('presentations')->group(function () {
        Route::post('/', [PresentationController::class, 'create']);
        Route::put('/{id}', [PresentationController::class, 'update']);
        Route::get('/{id}', [PresentationController::class, 'getById']);
        Route::get('/', [PresentationController::class, 'getByFilters']);
    });

    // Users
    Route::prefix('users')->group(function () {
        Route::post('/', [UserController::class, 'create']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::get('/{id}', [UserController::class, 'getById']);
        Route::get('/', [UserController::class, 'getByFilters']);
    });

    // Roles
    Route::prefix('roles')->group(function () {
        Route::post('/', [RoleController::class, 'create']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::get('/{id}', [RoleController::class, 'getById']);
        Route::get('/', [RoleController::class, 'getByFilters']);
    });

    // Terminals 
    Route::prefix('terminals')->group(function () {
        Route::get('/verify-terminal', [TerminalController::class, 'verifyTerminal']);
        Route::post('/', [TerminalController::class, 'create']);
        Route::put('/{id}', [TerminalController::class, 'update']);
        Route::get('/{id}', [TerminalController::class, 'getById']);
        Route::get('/', [TerminalController::class, 'getByFilters']);
    });

    // Customers
    Route::prefix('customers')->group(function () {
        Route::post('/', [CustomerController::class, 'create']);
        Route::put('/{id}', [CustomerController::class, 'update']);
        Route::get('/{id}', [CustomerController::class, 'getById']);
        Route::get('/', [CustomerController::class, 'getByFilters']);
    });

    // Bills
    Route::prefix('bills')->group(function () {
        Route::post('/', [BillController::class, 'create']);
        Route::put('/{id}', [BillController::class, 'update']);
        Route::get('/{id}', [BillController::class, 'getById']);
        Route::get('/', [BillController::class, 'getByFilters']);
        Route::post('/get-unique-products', [BillController::class, 'getUniqueProductsDB']);
        Route::post('/add-cost', [BillController::class, 'addCostToItems']);
        Route::post('/validate-inventory', [BillController::class, 'validateInventory']);
        Route::post('/calc-totales', [BillController::class, 'calcTotales']);
        Route::post('/update-stock', [BillController::class, 'updateStock']);
        Route::post('/update-units-stock', [BillController::class, 'updateUnitsOrStock']);
        Route::get('/{id}/validate-electronic', [BillController::class, 'validateElectronicBill']);
        Route::get('/{id}/store-credit-note', [BillController::class, 'storeElectronicCreditNote']);
        Route::get('/{id}/validate-credit-note', [BillController::class, 'validateElectronicCreditNote']);
    });

    // Empresa
    Route::get('company/show', [Company::class, 'show']);
    Route::post('company/update', [Company::class, 'update']);
});

// Test API
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando correctamente',
        'timestamp' => now()->toISOString()
    ]);
});

// Ruta pública SOLO para probar subida de PDF de factura a Cloudinary
Route::get('pdf-upload/bill/{bill}', function (Bill $bill) {
    try {
        $controller = app(AdminBillController::class);
        
        // Si es factura electrónica, usar el PDF completo con QR y CUFE
        if ($bill->isElectronic && $bill->electronicBill) {
            \Log::info('⚡ API pdf-upload - Generando PDF con QR y CUFE', ['bill_id' => $bill->id]);
            $pdfBase64 = $controller->getElectronicBillBase64($bill->id);
        } else {
            \Log::info('📄 API pdf-upload - Generando PDF estándar', ['bill_id' => $bill->id]);
            $pdfBase64 = $controller->getDirectSaleBillBase64($bill->id);
        }
        
        $pdfContent = base64_decode($pdfBase64);

        $dir = storage_path('app/pdfs');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $fileName = 'Factura_' . ($bill->number ?? $bill->id) . '.pdf';
        $filePath = $dir . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($filePath, $pdfContent);

        /** @var CloudinaryService $cloud */
        $cloud = app(CloudinaryService::class);
        $upload = $cloud->uploadRaw($filePath, [
            'folder' => config('cloudinary.folder', 'pos-images') . '/bills',
            'public_id' => 'bill_' . ($bill->number ?? $bill->id) . '_' . time(),
            'resource_type' => 'raw',
        ]);

        @unlink($filePath);

        if (!($upload['success'] ?? false)) {
            return response()->json($upload, 422);
        }

        return response()->json([
            'success' => true,
            'file_url' => $upload['secure_url'] ?? $upload['url'] ?? null,
        ]);
    } catch (\Throwable $e) {
        \Log::error('api pdf-upload bill error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});
