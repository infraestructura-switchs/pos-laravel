<?php

use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\CashClosingController;
use App\Http\Controllers\Admin\DailySaleController;
use App\Http\Controllers\Admin\ElectronicBillController;
use App\Http\Controllers\Admin\OutputController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\RemissionController;
use App\Http\Livewire\Admin\Bills\Create as BillsCreate;
use App\Http\Livewire\Admin\Bills\Index as BillsIndex;
use App\Http\Livewire\Admin\Bills\Show as BillsShow;
use App\Http\Livewire\Admin\CashClosing\Index as CashClosingIndex;
use App\Http\Livewire\Admin\CashOpening\Index as CashOpeningIndex;
use App\Http\Livewire\Admin\Company\Index as CompanyIndex;
use App\Http\Livewire\Admin\Customers\Index;
use App\Http\Livewire\Admin\DailySales\Index as DailySalesIndex;
use App\Http\Livewire\Admin\Factus\Connection;
use App\Http\Livewire\Admin\Finances\Index as FinancesIndex;
use App\Http\Livewire\Admin\Logs\Index as LogsIndex;
use App\Http\Livewire\Admin\Modules\Index as ModulesIndex;
use App\Http\Livewire\Admin\NumberingRanges\Index as NumberingRangesIndex;
use App\Http\Livewire\Admin\Outputs\Index as OutputsIndex;
use App\Http\Livewire\Admin\Payroll\Factus;
use App\Http\Livewire\Admin\Payroll\Index as PayrollIndex;
use App\Http\Livewire\Admin\Products\Index as ProductsIndex;
use App\Http\Livewire\Admin\Warehouses\Index as WarehousesIndex;
use App\Http\Livewire\Admin\Providers\Index as ProvidersIndex;
use App\Http\Livewire\Admin\Purchases\Create;
use App\Http\Livewire\Admin\Purchases\Index as PurchasesIndex;
use App\Http\Livewire\Admin\Purchases\Show;
use App\Http\Livewire\Admin\Roles\Index as RolesIndex;
use App\Http\Livewire\Admin\Sales\Index as SalesIndex;
use App\Http\Livewire\Admin\Staff\Index as StaffIndex;
use App\Http\Livewire\Admin\TaxRates\Index as TaxRatesIndex;
use App\Http\Livewire\Admin\Terminals\Index as TerminalsIndex;
use App\Http\Livewire\Admin\Users\Index as UsersIndex;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Admin\DirectSale\Create as DirectSaleCreate;
use App\Http\Livewire\Admin\InventoryRemissions\Index as InventoryRemissionsIndex;
use App\Http\Livewire\Admin\StockMovements\Index as StockMovementsIndex;
use App\Http\Livewire\Admin\WarehouseTransfers\Index as WarehouseTransfers;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('home');

Route::get('clientes', Index::class)->middleware('module:clientes')->name('customers.index');

Route::get('proveedores', ProvidersIndex::class)->middleware('module:proveedores')->name('providers.index');
Route::get('almacenes', WarehousesIndex::class)->middleware('module:bodegas')->name('warehouses.index');

Route::get('productos', ProductsIndex::class)->middleware('module:productos')->name('products.index');

Route::get('inventario-remisiones', InventoryRemissionsIndex::class)->middleware('module:inventario-remisiones')->name('inventory-remissions.index');

// Ruta alternativa para remisiones (sin nombre duplicado, redirecciona a la principal)
Route::get('remisiones', InventoryRemissionsIndex::class)->middleware('module:inventario-remisiones');

Route::get('entrada-salidas', StockMovementsIndex::class)->middleware('module:entrada-salidas')->name('stock_movements.index');

Route::get('transferencias', WarehouseTransfers::class)->name('warehouse-transfers.index');


Route::get('/pdf/remission/{id}', [RemissionController::class, 'download'])->name('pdf.remission');

Route::get('empleados', StaffIndex::class)->middleware(['module:empleados', 'check-payroll'])->name('staff.index');

Route::group(['middleware' => ['module:nomina', 'check-payroll']], function () {

    Route::get('nomina/factus/', Factus::class)->name('payroll.factus')->withoutMiddleware('check-payroll');

    Route::get('nomina', PayrollIndex::class)->name('payroll.index');

    Route::get('nomina/{payroll}', [PayrollController::class, 'show'])->name('payroll.show');

    Route::get('nomina-download/{payroll}', [PayrollController::class, 'download'])->name('payroll.download');
});

Route::group(['middleware' => ['module:egresos']], function () {

    Route::get('egresos', OutputsIndex::class)->name('outputs.index');

    Route::get('egreso/{output}', [OutputController::class, 'show'])->name('outputs.show');

    Route::get('egreso/pdf-upload/{output}', [OutputController::class, 'uploadPdf'])
        ->withoutMiddleware(['module:egresos', 'auth'])
        ->name('outputs.pdf-upload');

    Route::get('egreso/pdf-whatsapp/{output}', [OutputController::class, 'showWithWhatsapp'])->name('outputs.pdf-whatsapp');

    Route::get('egreso-download/{output}', [OutputController::class, 'download'])->name('outputs.download');

});

Route::group(['middleware' => ['module:compras']], function () {

    Route::get('compras', PurchasesIndex::class)->name('purchases.index');

    Route::get('compras/agregar-compra', Create::class)->name('purchases.create');

    Route::get('compra/{purchase}', Show::class)->name('purchases.show');

    Route::get('compra/pdf/{purchase}', [PurchaseController::class, 'show'])->name('purchases.pdf');

    Route::get('compra-download/{purchase}', [PurchaseController::class, 'download'])->name('purchases.download');

});

Route::group(['middleware' => ['module:facturas']], function () {

    Route::get('facturas', BillsIndex::class)->name('bills.index');

    Route::get('facturas/nueva-factura', BillsCreate::class)->name('bills.create');

    Route::get('facturas/{bill}', BillsShow::class)->name('bills.show');

    Route::get('facturas/informacion/{bill}', [BillController::class, 'getBill'])->name('bills.information');

    Route::get('facturas/pdf/{bill}', [BillController::class, 'show'])->name('bills.pdf');

    Route::get('facturas/pdf-upload/{bill}', [BillController::class, 'uploadPdf'])
        ->withoutMiddleware(['module:facturas', 'auth'])
        ->name('bills.pdf-upload');

    Route::get('facturas/pdf-whatsapp/{bill}', [BillController::class, 'showWithWhatsapp'])->name('bills.pdf-whatsapp');

    Route::get('facturas-download/{bill}', [BillController::class, 'download'])->name('bills.download');

    Route::post('facturas/{bill}/whatsapp', [BillController::class, 'sendWhatsapp'])->name('bills.whatsapp');

    // Rutas para facturación electrónica
    Route::get('facturas-electronicas/{bill}/pdf', [ElectronicBillController::class, 'downloadPdf'])->name('electronic-bills.pdf');
    Route::get('facturas-electronicas/{bill}/xml', [ElectronicBillController::class, 'downloadXml'])->name('electronic-bills.xml');
    Route::get('facturas-electronicas/{bill}/info', [ElectronicBillController::class, 'show'])->name('electronic-bills.show');

});

Route::get('ventas-rapidas/nueva-venta', fn () => view('livewire.admin.quick-sale.Index'))->name('quick-sales.create')->middleware('module:ventas rapidas');

// Ruta dedicada para "Vender" - vista de venta directa con productos en grid
Route::get('vender', DirectSaleCreate::class)->name('direct-sale.create')->middleware('module:vender');

// Descarga de factura desde Vender (sin exigir módulo facturas)
Route::get('vender/facturas-download/{bill}', [BillController::class, 'download'])
    ->middleware('module:vender')
    ->name('direct-sale.bills.download');

Route::get('financiaciones', FinancesIndex::class)->middleware('module:financiaciones')->name('finances.index');

Route::get('usuarios', UsersIndex::class)->middleware('module:usuarios')->name('users.index');

Route::get('productos-vendidos', SalesIndex::class)->name('sold-products.index');

Route::get('configuración', CompanyIndex::class)->middleware('module:configuraciones')->name('companies.settings');

Route::group(['middleware' => ['module:cierre de caja']], function () {

    Route::get('cierre-de-caja', CashClosingIndex::class)->name('cash-closing.index');

    Route::get('cierre-de-caja/pdf/{cashClosing}', [CashClosingController::class, 'show'])->name('cash-closing.pdf');

});

Route::get('apertura-de-caja', CashOpeningIndex::class)->middleware('module:cierre de caja')->name('cash-opening.index');

Route::get('impuestos', TaxRatesIndex::class)->middleware('module:impuestos')->name('tax-rates.index');

Route::get('ventas-diarias', DailySalesIndex::class)->middleware('module:roles y permisos')->name('daily-sales.index');

Route::get('ventas-diarias/pdf/{dailySale?}', [DailySaleController::class, 'show'])->middleware('module:roles y permisos')->name('daily-sales.pdf');

Route::get('roles-y-permisos', RolesIndex::class)->middleware('module:roles y permisos')->name('roles.index');

Route::get('rangos-de-numeracion', NumberingRangesIndex::class)->name('numbering-ranges.index')->middleware('module:rangos de numeración');

Route::get('terminales', TerminalsIndex::class)->middleware('module:terminales')->name('terminals.index');

Route::get('modulos', ModulesIndex::class)->name('modules.index');

Route::get('factus/conexion', Connection::class)->name('factus.connection');

Route::get('factro/conexion', \App\Http\Livewire\Admin\Factro\Connection::class)->name('factro.connection');

Route::get('logs', LogsIndex::class)->name('logs.index');

Route::get('logs-file', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs.file');

