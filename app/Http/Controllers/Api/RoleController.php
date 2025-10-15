<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RoleService;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    protected $service;

    public function __construct(RoleService $service)
    {
        $this->middleware('auth:sanctum');
        $this->service = $service;
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'max:250', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        $id = $this->service->create($data);

        return response()->json(['id' => $id], 201);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => ['required', 'max:250', Rule::unique('roles', 'name')->ignore($id)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        try {
            $this->service->update($id, $data);
            return response()->json(true);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getById(int $id)
    {
        return response()->json($this->service->getById($id));
    }

    public function getByFilters(Request $request)
    {
        $filters = $request->only(['name', 'guard_name', 'per_page']);
        return response()->json($this->service->getByFilters($filters));
    }
}
