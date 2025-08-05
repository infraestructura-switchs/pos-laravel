<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PresentationService;
use Illuminate\Http\Request;

class PresentationController extends Controller
{
    protected $service;

    public function __construct(PresentationService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        $id = $this->service->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Presentación creada con éxito.',
            'data'    => ['id' => $id],
        ]);
    }

    public function update(int $id, Request $request)
    {
        $updated = $this->service->update($id, $request->all());

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Presentación actualizada con éxito.' : 'No se pudo actualizar.',
        ]);
    }

    public function getById(int $id)
    {
        $presentation = $this->service->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Presentación encontrada con éxito.',
            'data'    => $presentation,
        ]);
    }

    public function getByFilters(Request $request)
    {
        $results = $this->service->getByFilters($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Presentaciones encontradas con éxito.',
            'data'    => $results,
        ]);
    }
}
