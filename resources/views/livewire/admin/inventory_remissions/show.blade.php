@extends('layouts.app')

@section('title', 'Ver remisión')

@section('content')
<div class="p-6 max-w-2xl">
  <h1 class="text-2xl mb-4">Remisión {{ $remission->folio }}</h1>

  <div class="mb-2"><strong>Almacén:</strong> {{ $remission->warehouse?->name }}</div>
  <div class="mb-2"><strong>Usuario:</strong> {{ $remission->user?->name }}</div>
  <div class="mb-2"><strong>Fecha:</strong> {{ $remission->remission_date }}</div>
  <div class="mb-2"><strong>Concepto:</strong> {{ $remission->concept }}</div>
  <div class="mb-2"><strong>Nota:</strong> {{ $remission->note }}</div>

  <div class="mt-4">
    <a href="{{ route('admin.inventory-remissions.index') }}" class="btn">Volver</a>
  </div>
</div>
@endsection



