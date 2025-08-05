<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BillService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Exceptions\CustomException;

class BillController extends Controller
{
    protected $service;

    public function __construct(BillService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        try {
            $id = $this->service->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Factura creada con éxito.',
                'data'    => ['id' => $id],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $result = $this->service->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Factura actualizada con éxito.',
                'data'    => $result,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Factura no encontrada.',
            ], 404);
        }
    }

    public function getById(int $id)
    {
        try {
            $bill = $this->service->getById($id);

            return response()->json([
                'success' => true,
                'message' => 'Factura obtenida con éxito.',
                'data'    => $bill,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Factura no encontrada.',
            ], 404);
        }
    }

    public function getByFilters(Request $request)
    {
        $results = $this->service->getByFilters($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Listado de facturas.',
            'data'    => $results,
        ]);
    }

    public function getUniqueProductsDB(Request $request)
    {
        $products = collect($request->input('products', []));
        $result = BillService::getUniqueProductsDB($products);

        return response()->json([
            'success' => true,
            'message' => 'Productos únicos obtenidos.',
            'data' => $result->toArray(),
        ]);
    }

    public function addCostToItems(Request $request)
    {
        $products = collect($request->input('products', []));
        $productsDB = collect($request->input('products_db', []));

        $productsDB = \App\Models\Product::hydrate($productsDB);
        BillService::addCostToItems($products, $productsDB);

        return response()->json([
            'success' => true,
            'message' => 'Costos añadidos a los productos.',
            'data' => $products->toArray(),
        ]);
    }

    public function validateInventory(Request $request)
    {
        try {
            $products = collect($request->input('products', []));
            $productsDB = \App\Models\Product::hydrate($request->input('products_db', []));

            BillService::validateInventory($products, $productsDB);

            return response()->json([
                'success' => true,
                'message' => 'Inventario validado correctamente.',
            ]);
        } catch (CustomException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function calcTotales(Request $request)
    {
        $products = collect($request->input('products', []));
        $productsDB = \App\Models\Product::hydrate($request->input('products_db', []));

        BillService::calcTotales($products, $productsDB);

        return response()->json([
            'success' => true,
            'message' => 'Totales calculados.',
            'data' => $products->toArray(),
        ]);
    }

    public function updateStock(Request $request)
    {
        $productsDB = \App\Models\Product::hydrate($request->input('products_db', []));

        BillService::updateStock($productsDB);

        return response()->json([
            'success' => true,
            'message' => 'Stock actualizado correctamente.',
        ]);
    }

    public function updateUnitsOrStock(Request $request)
    {
        $product = $request->input('product');
        $productsDB = \App\Models\Product::hydrate($request->input('products_db', []));

        BillService::updateUnitsOrStock($product, $productsDB);

        return response()->json([
            'success' => true,
            'message' => 'Unidades o stock actualizado correctamente.',
        ]);
    }

    public function validateElectronicBill($id)
    {
        try {
            $bill = \App\Models\Bill::findOrFail($id);
            BillService::validateElectronicBill($bill);

            return response()->json([
                'success' => true,
                'message' => 'Factura electrónica validada correctamente.',
            ]);
        } catch (CustomException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function storeElectronicCreditNote($id)
    {
        $bill = \App\Models\Bill::findOrFail($id);
        BillService::storeElectronicCreditNote($bill);

        return response()->json([
            'success' => true,
            'message' => 'Nota crédito electrónica creada.',
        ]);
    }

    public function validateElectronicCreditNote($id)
    {
        try {
            $bill = \App\Models\Bill::findOrFail($id);
            BillService::validateElectronicCreditNote($bill);

            return response()->json([
                'success' => true,
                'message' => 'Nota crédito electrónica validada.',
            ]);
        } catch (CustomException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
