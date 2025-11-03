<div class="container">
    <x-commons.header>
        <div class="flex items-center" wire:click='updateApiStatus'>
            <div @class([
                'w-8 sm:w-12 h-4 sm:h-5 rounded-full flex items-center px-1 cursor-pointer transition-colors duration-300',
                'bg-blue-600' => $isApiEnabled,
                'bg-slate-300' => !$isApiEnabled,
            ])>
                <span @class(['h-3 w-3 sm:h-4 sm:w-4 bg-white rounded-full duration-300 transform', 'translate-x-3.5 sm:translate-x-6' => $isApiEnabled])></span>
            </div>
            <span class="ml-0.5">{{ $isApiEnabled ? 'Activado' : 'Desactivado' }}</span>
        </div>

        <div class="hidden" aria-hidden="true">
                <x-wireui.button icon="eart" wire:click="testConnection" text="Probar conexión"/>
        </div>        
    </x-commons.header>

    <x-wireui.card title="Credenciales API Factro ArquitecSoft S.A.S">

        <div class="space-y-4">
            <x-wireui.input label="URL" wire:model.defer="api.url" />
            <x-wireui.input label="API Key ID" wire:model.defer="api.api_key_id" />
            <x-wireui.input label="Company ID" wire:model.defer="api.company_id" />
            <x-wireui.input label="Program" wire:model.defer="api.program" />            
        </div>

        <x-slot:footer>
            <div class="text-right space-x-3">
                <x-wireui.button wire:click="update" text="Actualizar" load textLoad="Actualizando.." />
            </div>
        </x-slot:footer>
    </x-wireui.card>

    <x-loads.panel-fixed text="Cambiando estado..." class="no-print z-[999]" wire:loading />
</div>

@push('js')
<script>
    function authenticatedFactro() {
        return {
            async openFactro() {
                response = await this.getTokenFactro();

                if (response.token) {
                    params = new URLSearchParams({
                        token: response.token,
                        redirect_url: response.redirect_url
                    }).toString();

                    window.open(`${response.domain}/external-authentication?${params}`, '_blank');
                } else {
                    alert('Ha ocurrido un error al abrir Factro');
                }
            },

            async getTokenFactro() {
                credentials = await this.$wire.call('getTokenFactro');
                return credentials;
            },
        };
    }
</script>
@endpush
