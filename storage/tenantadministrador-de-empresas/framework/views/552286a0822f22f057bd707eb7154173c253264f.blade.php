<?php extract(collect($attributes->getAttributes())->mapWithKeys(function ($value, $key) { return [Illuminate\Support\Str::camel(str_replace([':', '.'], ' ', $key)) => $value]; })->all(), EXTR_SKIP); ?>
@props(['label','hasError'])
<x-wireui.label :label="$label" :has-error="$hasError" {{ $attributes }}>

{{ $slot ?? "" }}
</x-wireui.label>