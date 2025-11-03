<?php $__env->startPush('html'); ?>
<div x-data="alpineTicketBill()" class="print">
  <div x-show="show" class="absolute -z-40 font-roboto top-0 left-0 bg-white py-10 !text-[12px] px-4 w-full">

    <div class="flex justify-center">
      <img class="h-28" src="<?php echo e(getUrlLogo()); ?>">
    </div>

    
    <ul class="flex flex-col items-center leading-4">
      <li class="font-semibold">
        <span x-text="company.name"></span>
      </li>
      <li>
        <span x-text="company.nit"></span>
      </li>
      <li>
        <span x-text="company.direction"></span>
      </li>
      <li>
        <span x-text="company.phone"></span>
      </li>
    </ul>

    <hr class="border border-slate-400 my-3">

    
    <div class="overflow-hidden">
      <p x-show="!isElectronic" class="font-bold text-right">Venta: <span x-text="bill.number"></span></p>
      <p x-show="isElectronic" class="font-bold text-right">Factura de electrónica de venta: <span x-text="bill.number"></span></p>
      <ul class="leading-4 whitespace-nowrap">
        <li>
          <span class="w-14 inline-block">Fecha</span>
          :
          <span class="font-medium" x-text="bill.format_created_at"></span>
        </li>
        <li class="">
          <span class="w-14 inline-block">Cajero</span>
          :
          <span class="font-medium truncate" x-text="bill.user_name"></span>
        </li>
        <li>
          <span class="w-14 inline-block">C.C / NIT</span>
          :
          <span class="font-medium" x-text="customer.identification"></span>
        </li>
        <li>
          <span class="w-14 inline-block">Cliente</span>
          :
          <span class="font-medium" x-text="customer.names"></span>
        </li>
      </ul>
    </div>

    <hr class="border border-slate-400 my-3">

    
    <table class="w-full leading-3">
      <thead>
        <tr>
          <th width="70%" class="text-left font-medium">
            Producto o servicio
          </th>
          <th width="10%">
            Cant
          </th>
          <th width="20%" class="text-right font-medium">
            Total
          </th>
        </tr>
      </thead>
      <tbody>
        <template x-for="item in products">
          <tr>
            <td class="text-left">
              <span x-text="strLimit(item.name, 60)"></span>
            </td>
            <td class="text-center">
              <span x-text="item.amount"></span>
            </td>
            <td class="text-right">
              <span x-text="formatToCop(item.total)"></span>
            </td>
          </tr>
        </template>
      </tbody>
    </table>

    <h1 class="border-b-2 border-dotted my-5 border-slate-400"></h1>

    
    <ul class="leading-4">
      <li class="flex justify-end">
        <span>Valor bruto:</span>
        <span class="font-medium w-24 inline-block text-right" x-text="formatToCop(bill.subtotal)"></span>
      </li>
      <li class="flex justify-end">
        <span>Servicio voluntario:</span>
        <span class="font-medium w-24 inline-block text-right" x-text="formatToCop(bill.tip)"></span>
      </li>
      <li class="flex justify-end">
        <span>Descuento:</span>
        <span class="font-medium w-24 inline-block text-right" x-text="formatToCop(bill.discount)"></span>
      </li>
      <template x-for="tax in taxes">
        <li class="flex justify-end">
          <span x-text="tax.tribute_name"></span>
          <span class="font-medium w-24 inline-block text-right" x-text="formatToCop(tax.tax_amount)"></span>
        </li>
      </template>
      <li class="flex justify-end">
        <span>Total a pagar:</span>
        <span class="font-medium w-24 inline-block text-right" x-text="formatToCop(bill.final_total)"></span>
      </li>
    </ul>

    <hr class="border border-slate-400 my-3">

    
    <ul class="leading-4">
      <li class="flex justify-end">
        <span class="font-bold">Forma de pago</span>
        <span class="font-medium w-24 inline-block text-right"></span>
      </li>
      <li class="flex justify-end">
        <span x-text="bill.payment_method + ':'"></span>
        <span class="font-medium w-24 inline-block text-right" x-text="formatToCop(bill.cash)"></span>
      </li>
      <li class="flex justify-end">
        <span>Cambio:</span>
        <span class="font-medium w-24 inline-block text-right" x-text="formatToCop(bill.change)"></span>
      </li>
    </ul>

    
    <template x-if="isElectronic && Object.keys(range).length">
      <div class="mt-1">
        <p class="leading-3 text-center">
          Resolución DIAN <span x-text="range.resolution_number"></span>
          autorizada el <span x-text="range.start_date"></span>
          prefijo <span x-text="range.prefix"></span>
          del <span x-text="range.from"></span>
          al <span x-text="range.to"></span>
          Vig <span x-text="range.months"></span> meses
        </p>
      </div>
    </template>

    
    <template x-if="isElectronic">
      <div class="flex justify-center mt-1">
        <img class="max-w-[140px]" :src="electronic_bill.qr_image">
      </div>
    </template>
    <template x-if="isElectronic">
      <div>
        <p class="text-center font-semibold text-xs">CUFE</p>
        <p class="break-all leading-3" x-text="electronic_bill.cufe"></p>
      </div>
    </template>

    
    <div class="text-xs mt-4">
      <p class="text-center leading-4">Elaborado por: <span x-text="(company && company.invoice_provider) ? company.invoice_provider.name : ''"></span></p>
      <p class="text-center leading-4"><span x-text="(company && company.invoice_provider) ? company.invoice_provider.url : ''"></span> NIT: <span x-text="(company && company.invoice_provider) ? company.invoice_provider.nit : ''"></span></p>
    </div>

  </div>
</div>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/pdfs/ticket-bill.blade.php ENDPATH**/ ?>