@props(['route', 'name', 'icon', 'active'])

@php
    $class = $active ? 'text-cyan-400 bg-gray-100 rounded-md shadow-inner shadow-slate-300' :
                        'text-gray-600'
@endphp

<div class="flex flex-col items-center justify-end cursor-pointer {{ $class }}" title="{{ $name }}">

    <a href="{{ $route }}" class="inline-flex items-center font-medium w-full px-2 h-8 lg:h-9 text-sm lg:text-base" title="{{$name}}">
        <i class="ico icon-{{ $icon }} text-base lg:text-lg"></i>
        @if (!request()->routeIs('admin.quick-sales.create') && !request()->routeIs('admin.direct-sale.create'))
            <span class="whitespace-nowrap text-xs lg:text-sm ml-3 lg:ml-4"> {{ $name }} </span>
        @endif
    </a>

</div>
