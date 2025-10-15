@extends('layouts.app')

@section('title', 'Ver almacén')

@section('content')
<div class="p-6 max-w-2xl">
  <h1 class="text-2xl mb-4">{{ $warehouse->name }}</h1>

  <div class="mb-2"><strong>Dirección:</strong> {{ $warehouse->address }}</div>
  <div class="mb-2"><strong>Teléfono:</strong> {{ $warehouse->phone }}</div>

  <div class="mt-4">
    <a href="{{ route('warehouses.index') }}" class="btn">Volver</a>
    <a href="{{ route('admin.inventory-remissions.index', ['warehouse_id' => $warehouse->warehouse_id]) }}" class="btn btn-secondary ml-2" x-data @click.prevent="$wire.emitTo('admin.inventory-remissions.index', 'filterByWarehouse', {{ $warehouse->warehouse_id }}); window.location='{{ route('admin.inventory-remissions.index', ['warehouse_id' => $warehouse->warehouse_id]) }}'">Ver remisiones</a>
  </div>
</div>
@endsection



