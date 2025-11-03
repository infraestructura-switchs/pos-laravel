<div x-data="createCustomer">
  <?php if (isset($component)) { $__componentOriginal1c97c2c41815187e0919457cda1ee0c550e97c19 = $component; } ?>
<?php $component = App\View\Components\Wireui\Modal::resolve(['maxWidth' => '4xl'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'openCreate']); ?>
    <?php if (isset($component)) { $__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d = $component; } ?>
<?php $component = App\View\Components\Wireui\Card::resolve(['title' => 'Crear cliente'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

      <?php if (isset($component)) { $__componentOriginalfc218273c6509951100fffb520610b6c38f96d07 = $component; } ?>
<?php $component = App\View\Components\Wireui\Errors::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.errors'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Errors::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfc218273c6509951100fffb520610b6c38f96d07)): ?>
<?php $component = $__componentOriginalfc218273c6509951100fffb520610b6c38f96d07; ?>
<?php unset($__componentOriginalfc218273c6509951100fffb520610b6c38f96d07); ?>
<?php endif; ?>

      <div class="grid sm:grid-cols-2 gap-6">

        <?php if (isset($component)) { $__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1 = $component; } ?>
<?php $component = App\View\Components\Wireui\NativeSelect::resolve(['label' => 'Documento de identidad','optionKeyValue' => 'true','options' => $identificationDocuments] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.native-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\NativeSelect::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'identification_document_id','x-model' => 'identification_document_id','class' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1)): ?>
<?php $component = $__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1; ?>
<?php unset($__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1); ?>
<?php endif; ?>

        <div class="flex gap-x-4">
          <div class="flex-1">
            <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'N° Identificación'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'no_identification','x-model' => 'no_identification','x-on:keypress' => 'onlyNumbers($event, allowOnlyNumbers())','class' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9)): ?>
<?php $component = $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9; ?>
<?php unset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9); ?>
<?php endif; ?>
          </div>

          <template x-if="identification_document_id === '6'">
            <div class="w-20">
              <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'DV'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-model' => 'dv','name' => 'dv','x-on:keypress' => 'onlyNumbers($event)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9)): ?>
<?php $component = $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9; ?>
<?php unset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9); ?>
<?php endif; ?>
            </div>
          </template>
        </div>

        <div x-show="identification_document_id == '6'" class="grid sm:grid-cols-2 gap-6 col-span-2">
          <?php if (isset($component)) { $__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1 = $component; } ?>
<?php $component = App\View\Components\Wireui\NativeSelect::resolve(['label' => 'Tipo de persona','optionKeyValue' => 'true','options' => $legalOrganizations] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.native-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\NativeSelect::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:key' => 'identification_document_id','wire:model.defer' => 'legal_organization','class' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1)): ?>
<?php $component = $__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1; ?>
<?php unset($__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1); ?>
<?php endif; ?>

          <?php if (isset($component)) { $__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1 = $component; } ?>
<?php $component = App\View\Components\Wireui\NativeSelect::resolve(['label' => 'Responsabilidad tributaria','optionKeyValue' => 'true','options' => $tributes] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.native-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\NativeSelect::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:key' => 'tribute','wire:model.defer' => 'tribute','class' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1)): ?>
<?php $component = $__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1; ?>
<?php unset($__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1); ?>
<?php endif; ?>
        </div>

        <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'Nombres y apellidos'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'names']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9)): ?>
<?php $component = $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9; ?>
<?php unset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'Dirección'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'direction']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9)): ?>
<?php $component = $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9; ?>
<?php unset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'Celular'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'phone']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9)): ?>
<?php $component = $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9; ?>
<?php unset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'Email'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'email']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9)): ?>
<?php $component = $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9; ?>
<?php unset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9); ?>
<?php endif; ?>

      </div>

       <?php $__env->slot('footer', null, []); ?> 
        <div class="text-right space-x-3">
          <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Cerrar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['secondary' => true,'x-on:click' => 'show=false']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
          <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Guardar','load' => true,'textLoad' => 'Guardando..'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'store']); ?>
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
   <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1c97c2c41815187e0919457cda1ee0c550e97c19)): ?>
<?php $component = $__componentOriginal1c97c2c41815187e0919457cda1ee0c550e97c19; ?>
<?php unset($__componentOriginal1c97c2c41815187e0919457cda1ee0c550e97c19); ?>
<?php endif; ?>
</div>

<?php $__env->startPush('js'); ?>
<script lang="js">
  function createCustomer() {
    return {
      foreingDocuments: ['7', '8', '10'],
      dv: <?php if ((object) ('dv') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('dv'->value()); ?>')<?php echo e('dv'->hasModifier('defer') ? '.defer' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('dv'); ?>')<?php endif; ?>.defer,
      identification_document_id: <?php if ((object) ('identification_document_id') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('identification_document_id'->value()); ?>')<?php echo e('identification_document_id'->hasModifier('defer') ? '.defer' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('identification_document_id'); ?>')<?php endif; ?>.defer,
      no_identification: <?php if ((object) ('no_identification') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('no_identification'->value()); ?>')<?php echo e('no_identification'->hasModifier('defer') ? '.defer' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('no_identification'); ?>')<?php endif; ?>.defer,

      init(){
        this.$watch('identification_document_id', (value, oldValue) => {
          this.calc()
        })

        this.$watch('no_identification', (value, oldValue) => {
          this.calc()
        })
      },

      calc(){
        if (this.identification_document_id !== '6') return (this.dv = '')

        let nit = this.no_identification
        let isNitValid = nit >>> 0 === parseFloat(nit) ? !0 : !1

        if (isNitValid) {
          this.dv = calculateCheckDigit(nit)
        }
      },

      allowOnlyNumbers() {
        return this.foreingDocuments.includes(this.identification_document_id)
      },
    }
  }
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/livewire/admin/customers/create.blade.php ENDPATH**/ ?>