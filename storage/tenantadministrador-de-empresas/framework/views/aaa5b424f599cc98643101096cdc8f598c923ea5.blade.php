<button  wire:loading.attr="disabled" wire:loading.class="cursor-wait" class="inline-flex items-center border border-transparent leading-6 font-medium rounded-md text-white transition ease-in-out duration-150 text-xs sm:text-sm px-4 py-1 sm:py-1.5     bg-red-500 hover:bg-red-400 hover:ring-red-500 disabled:opacity-60" @click="products=[]" x-show="products.length" danger="danger"  >

    
    
            <span wire:target="" wire:loading> Limpiar </span>
        <span wire:target="" wire:loading.remove> Limpiar </span>
    

</button>
