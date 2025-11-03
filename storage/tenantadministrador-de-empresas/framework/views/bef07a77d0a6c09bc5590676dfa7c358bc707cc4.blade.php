<button  wire:loading.attr="disabled" wire:loading.class="cursor-wait" class="inline-flex items-center border border-transparent leading-6 font-medium rounded-md text-white transition ease-in-out duration-150 text-xs sm:text-sm px-4 py-1 sm:py-1.5     bg-green-500 hover:bg-green-400 hover:ring-green-500 disabled:opacity-60" x-on:click="$wire.emitTo('admin.products.import', 'openImport')" success="success"  >

    
            <i class="ico icon-excel mr-1"  ></i>
    
            <span wire:target="" wire:loading> Importar desde excel </span>
        <span wire:target="" wire:loading.remove> Importar desde excel </span>
    

</button>
