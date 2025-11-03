<div>
  <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.commons.modal-alpine','data' => ['xOn:openWhatsappModal.window' => 'show=true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('commons.modal-alpine'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:open-whatsapp-modal.window' => 'show=true']); ?>
    <div x-data="whatsappModal()">

      <?php if (isset($component)) { $__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d = $component; } ?>
<?php $component = App\View\Components\Wireui\Card::resolve(['cardClasses' => 'max-w-sm mx-auto'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        
        <!-- Header personalizado con botón X -->
         <?php $__env->slot('header', null, []); ?> 
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
         <?php $__env->endSlot(); ?>

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
              <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Sí, enviar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full justify-center','positive' => true,'@click' => 'step = 2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
              
              <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'No, continuar sin enviar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full justify-center','secondary' => true,'@click' => 'skipWhatsapp()']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
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
                x-on:keyup.enter="confirmWhatsapp()"
                placeholder="300 123 4567"
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

         <?php $__env->slot('footer', null, []); ?> 
          <div class="flex justify-end space-x-2" x-show="step === 2">
            <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Atrás'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['secondary' => true,'@click' => 'step = 1; error = \'\'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
            
            <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Confirmar y enviar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'confirmWhatsapp()']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
          </div>
         <?php $__env->endSlot(); ?>

       <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d)): ?>
<?php $component = $__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d; ?>
<?php unset($__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d); ?>
<?php endif; ?>

    </div>
   <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
</div>

<?php $__env->startPush('js'); ?>
<style>
  /* Estilos personalizados para intl-tel-input con Tailwind */
  .iti {
    width: 100%;
  }
  
  .iti__flag-container {
    padding: 0;
  }
  
  .iti__selected-flag {
    padding: 0 0 0 12px;
    background-color: white;
    border-right: 1px solid rgb(203 213 225);
  }
  
  .iti__selected-flag:hover,
  .iti__selected-flag:focus {
    background-color: rgb(248 250 252);
  }
  
  .iti input[type="tel"] {
    width: 100%;
    border-radius: 0.375rem;
    border: 1px solid rgb(203 213 225);
    padding: 0.25rem 0.5rem 0.25rem 60px;
    font-size: 1.125rem;
    font-weight: 600;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    transition: all 0.1s ease-in-out;
  }
  
  @media (min-width: 640px) {
    .iti input[type="tel"] {
      padding-top: 0.5rem;
      padding-bottom: 0.5rem;
    }
  }
  
  .iti input[type="tel"]:focus {
    outline: none;
    border-color: rgb(103 232 249);
    box-shadow: 0 0 0 1px rgb(103 232 249);
  }
  
  .iti input[type="tel"]::placeholder {
    color: rgb(148 163 184);
  }
  
  .iti__country-list {
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    border-radius: 0.375rem;
    max-height: 200px;
  }
  
  .iti__divider {
    border-bottom: 1px solid rgb(226 232 240);
  }
</style>

<script>
function whatsappModal() {
  return {
    step: 1,
    phoneNumber: '',
    error: '',
    onConfirm: null,
    onSkip: null,
    iti: null, // Instancia de intl-tel-input

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

      // Watch para inicializar intl-tel-input cuando cambie al step 2
      this.$watch('step', (value) => {
        if (value === 2) {
          this.$nextTick(() => {
            this.initPhoneInput();
          });
        }
      });
    },

    initPhoneInput() {
      const input = this.$refs.phoneInput;
      if (!input) return;

      // Destruir instancia previa si existe
      if (this.iti) {
        this.iti.destroy();
      }

      // Inicializar intl-tel-input
      this.iti = window.intlTelInput(input, {
        initialCountry: 'co', // Colombia por defecto
        preferredCountries: ['co', 'us', 'mx', 've', 'ec'], // Países preferidos
        separateDialCode: true, // Mostrar código de país separado
        autoPlaceholder: 'polite', // Placeholder automático
        formatOnDisplay: true, // Formatear al mostrar
        nationalMode: false, // Incluir código de país
        utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js' // Validación y formato
      });

      // Enfocar el input
      input.focus();
    },

    validatePhone() {
      if (!this.iti) {
        return { valid: false, message: 'Error al inicializar el selector de teléfono' };
      }

      // Verificar si el número es válido usando intl-tel-input
      if (!this.iti.isValidNumber()) {
        return { valid: false, message: 'Por favor, ingrese un número de teléfono válido' };
      }

      // Obtener el número en formato internacional (E.164)
      const phoneNumber = this.iti.getNumber();
      
      // Verificar que tenga contenido
      if (!phoneNumber || phoneNumber.length < 10) {
        return { valid: false, message: 'El número debe tener al menos 10 dígitos' };
      }
      
      return { valid: true, cleaned: phoneNumber };
    },

    confirmWhatsapp() {
      this.error = '';
      
      // Validar número usando intl-tel-input
      const validation = this.validatePhone();
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
      // Destruir instancia de intl-tel-input si existe
      if (this.iti) {
        this.iti.destroy();
        this.iti = null;
      }
      
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
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/whatsapp-modal.blade.php ENDPATH**/ ?>