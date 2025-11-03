<button  wire:loading.attr="disabled" wire:loading.class="cursor-wait" class="inline-flex items-center border border-transparent leading-6 font-medium rounded-md text-white transition ease-in-out duration-150 text-xs sm:text-sm px-4 py-1 sm:py-1.5     bg-green-500 hover:bg-green-400 hover:ring-green-500 disabled:opacity-60" wire:click="exportProducts" success="success"  >

                <svg wire:target="{{ 'exportProducts,' }}" wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
        </path>
    </svg>
    
            <i class="ico icon-excel mr-1" wire:loading.remove ></i>
    
            <span wire:target="exportProducts," wire:loading> Exportando... </span>
        <span wire:target="exportProducts," wire:loading.remove> Exportar a excel </span>
    

</button>
