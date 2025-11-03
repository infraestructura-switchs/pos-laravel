<button  wire:loading.attr="disabled" wire:loading.class="cursor-wait" class="inline-flex items-center border border-transparent leading-6 font-medium rounded-md text-white transition ease-in-out duration-150 text-xs sm:text-sm px-4 py-1 sm:py-1.5 bg-indigo-500 hover:bg-indigo-600 hover:ring-indigo-500 disabled:opacity-60" wire:click="update"  >

                <svg wire:target="<?php echo e('update,'); ?>" wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
        </path>
    </svg>
    
    
            <span wire:target="update," wire:loading> Actualizando... </span>
        <span wire:target="update," wire:loading.remove> Actualizar </span>
    

</button>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\storage\tenantadministrador-de-empresas\framework\views/310b508edfe3bac7a9a759a2525276032fe3ffd6.blade.php ENDPATH**/ ?>