<div class="">

            <div class="flex mb-1">
            <label class="block text-sm font-semibold text-gray-700" for="d9a336ca79a1ec3772484d694de2697d">
Monedas iniciales (opcional)
</label>
        </div>
    
    <div class="relative rounded-md  shadow-sm ">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                    <span class="pl-1 flex items-center self-center">
                        $
                    </span>
                            </div>
        
        <input type="text" autocomplete="off" class="placeholder-slate-400 border border-slate-300 focus:ring-cyan-400 focus:border-cyan-400 block w-full text-sm sm:text-tiny py-1 sm:py-2 rounded-md transition ease-in-out duration-100 focus:outline-none read-only:bg-slate-50 shadow-sm pl-8" wire:model.lazy="initial_coins" placeholder="0" name="initial_coins" id="d9a336ca79a1ec3772484d694de2697d"
             onkeypress='return onlyNumbers(event)'                      />

            </div>

    </div>

