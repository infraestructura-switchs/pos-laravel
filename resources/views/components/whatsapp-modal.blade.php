<div>
  <x-commons.modal-alpine x-on:open-whatsapp-modal.window="show=true">
    <div x-data="whatsappModal()">

      <x-wireui.card cardClasses="max-w-sm mx-auto">
        
        <!-- Header personalizado con botón X -->
        <x-slot:header>
          <div class="flex items-center justify-between px-4 py-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Enviar PDF por WhatsApp</h3>
            <button @click="$dispatch('close-modal')" 
                    type="button"
                    class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">
              <span class="sr-only">Cerrar</span>
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </x-slot:header>

        <div class="space-y-6 bg-white p-5">

          <!-- Step 1: Confirmación inicial -->
          <div x-show="step === 1">
            <div class="text-center mb-4">
              <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 mb-3">
                <i class="ti ti-brand-whatsapp text-green-600 text-2xl"></i>
              </div>
              <p class="text-lg font-semibold text-gray-900">
                ¿Desea recibir el PDF de la factura por WhatsApp?
              </p>
            </div>

            <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3">
              <x-wireui.button 
                class="w-full justify-center"
                positive
                @click="step = 2"
                text="Sí, enviar" />
              
              <x-wireui.button 
                class="w-full justify-center"
                secondary
                @click="skipWhatsapp()"
                text="No, continuar sin enviar" />
            </div>
          </div>

          <!-- Step 2: Captura de número -->
          <div x-show="step === 2">
            <div>
              <label class="mb-1 block font-semibold">NÚMERO DE WHATSAPP</label>
              <input 
                type="tel" 
                id="whatsapp-phone"
                x-ref="phoneInput"
                x-model="phoneNumber"
                x-on:keyup.enter="confirmWhatsapp()"
                placeholder="Ej: 3001234567"
                class="block w-full rounded-md border border-slate-300 py-1 text-right text-lg font-semibold placeholder-slate-400 shadow-sm transition duration-100 ease-in-out focus:border-cyan-400 focus:outline-none focus:ring-cyan-400 sm:py-2"
                autocomplete="tel">
              
              <p class="mt-2 text-xs text-gray-500">
                Ingrese el número sin espacios ni caracteres especiales
              </p>
              
              <div x-show="error" class="mt-2 inline-flex items-center text-sm text-red-600">
                <span x-text="error"></span>
              </div>
            </div>
          </div>

        </div>

        <x-slot:footer>
          <div class="flex justify-end space-x-2" x-show="step === 2">
            <x-wireui.button 
              secondary
              @click="step = 1; error = ''"
              text="Atrás" />
            
            <x-wireui.button 
              @click="confirmWhatsapp()"
              text="Confirmar y enviar" />
          </div>
        </x-slot:footer>

      </x-wireui.card>

    </div>
  </x-commons.modal-alpine>
</div>

@push('js')
<script>
function whatsappModal() {
  return {
    step: 1,
    phoneNumber: '',
    error: '',
    onConfirm: null,
    onSkip: null,

    init() {
      // Escuchar evento para abrir el modal
      window.addEventListener('open-whatsapp-modal', (event) => {
        this.step = 1;
        this.phoneNumber = '';
        this.error = '';
        
        // Guardar callbacks
        if (event.detail) {
          this.onConfirm = event.detail.onConfirm;
          this.onSkip = event.detail.onSkip;
        }
      });

      // Escuchar evento para cerrar el modal
      window.addEventListener('close-modal', () => {
        this.closeModal();
      });

      // Watch para enfocar el input cuando cambie al step 2
      this.$watch('step', (value) => {
        if (value === 2) {
          this.$nextTick(() => {
            if (this.$refs.phoneInput) {
              this.$refs.phoneInput.focus();
            }
          });
        }
      });
    },

    validatePhone(phone) {
      // Eliminar espacios y caracteres no numéricos excepto +
      const cleaned = phone.replace(/[^\d+]/g, '');
      
      // Debe tener al menos 10 dígitos
      const digitsOnly = cleaned.replace(/\D/g, '');
      if (digitsOnly.length < 10) {
        return { valid: false, message: 'El número debe tener al menos 10 dígitos' };
      }
      
      return { valid: true, cleaned: cleaned };
    },

    confirmWhatsapp() {
      this.error = '';
      
      // Validar número
      const validation = this.validatePhone(this.phoneNumber);
      if (!validation.valid) {
        this.error = validation.message;
        return;
      }
      
      // Guardar datos
      const callback = this.onConfirm;
      const phone = validation.cleaned;
      
      // Cerrar modal
      this.closeModal();
      
      // Ejecutar callback
      if (callback && typeof callback === 'function') {
        setTimeout(() => {
          callback(phone);
        }, 100);
      }
    },

    skipWhatsapp() {
      // Guardar callback
      const callback = this.onSkip;
      
      // Cerrar modal
      this.closeModal();
      
      // Ejecutar callback
      if (callback && typeof callback === 'function') {
        setTimeout(() => {
          callback();
        }, 100);
      }
    },

    closeModal() {
      // Limpiar datos
      this.step = 1;
      this.phoneNumber = '';
      this.error = '';
      this.onConfirm = null;
      this.onSkip = null;
      
      // Disparar evento al documento para cerrar el modal padre
      window.dispatchEvent(new CustomEvent('close-whatsapp-modal'));
    }
  }
}
</script>
@endpush
