<div {{ $attributes }} class="bg-white border border-slate-200 shadow-sm overflow-x-auto rounded-lg" style="width: 100%; max-width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">

    @if (isset($top))
        <div class="flex items-end justify-between py-1.5 px-2 bg-slate-100 border text-xs sm:text-sm md:text-tiny border-b shadow">
            <div>
                <span class="font-bold text-2xl">{{ $top->attributes['title'] }}</span>
            </div>
            <div class="divide-x-2">
                {{ $top }}
            </div>
        </div>
    @endif

    @if (isset($header))
        <div class="flex w-full items-end p-4 space-x-3">
            {{ $header  }}
        </div>
    @endif

    {{ $slot }}

</div>
