<button  wire:loading.attr="disabled" wire:loading.class="cursor-wait" class="inline-flex items-center border border-transparent leading-6 font-medium rounded-md text-white transition ease-in-out duration-150 text-xs sm:text-sm px-4 py-1 sm:py-1.5 bg-indigo-500 hover:bg-indigo-600 hover:ring-indigo-500 disabled:opacity-60" x-on:click="$wire.emitTo('admin.roles.create', 'openCreate')"  >

    
            <i class="ico icon-user mr-1"  ></i>
    
            <span wire:target="" wire:loading> Crear rol </span>
        <span wire:target="" wire:loading.remove> Crear rol </span>
    

</button>
