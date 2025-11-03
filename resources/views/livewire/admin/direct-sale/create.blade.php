  <div id="direct-sale-root" x-data="{ 
    toggleView: true,
    order: {
      id: null,
      name: 'Venta en caja',
      customer: {
        id: 1,
        names: 'Consumidor Final'
      },
      delivery_address: ''
    },
    delivery_address: ''
  }"
  class="pb-10">

  <x-loads.panel-fixed id="load-panel"
    text="Cargando..."
    class="z-[999] hidden" />

  <!-- Panel de carga para facturación -->
  <x-loads.panel-fixed text="Procesando factura..." class="no-print z-[999]" wire:loading wire:target='storeBill' />

  <livewire:admin.quick-sale.customers />

  <livewire:admin.customers.create />

  <livewire:admin.quick-sale.change />

  @include('pdfs.ticket-bill')

  @include('pdfs.ticket-pre-bill')

  @include('pdfs.ticket-command-bill')

  <!-- Modal de WhatsApp -->
  <x-whatsapp-modal />

  <script>
    window.directSaleOrder = null;

    window.alpineOrdersInstance = {
      showCustomers: function(order) {
        if (order) {
          window.directSaleCurrentOrder = order;
        }
        window.dispatchEvent(new CustomEvent('open-customers', { detail: true }));
      }
    };

    window.directSaleShowCustomers = function() {
      const alpineComponent = Alpine.$data(document.getElementById('direct-sale-root'));
      const currentOrder = alpineComponent && alpineComponent.order ? alpineComponent.order : {
        id: null,
        name: 'Venta en caja',
        customer: {
          id: 1,
          names: 'Consumidor Final'
        },
        delivery_address: ''
      };
      
      window.directSaleCurrentOrder = currentOrder;
      window.dispatchEvent(new CustomEvent('open-customers', { detail: true }));
    };

    // Variable global para guardar el número de WhatsApp
    window.directSaleWhatsappPhone = null;
    window.directSaleWantsWhatsapp = false;

    window.addEventListener('set-change', (event) => {
      const alpineComponent = Alpine.$data(document.getElementById('direct-sale-root'));
      const selectedCustomer = alpineComponent && alpineComponent.order && alpineComponent.order.customer
        ? alpineComponent.order.customer
        : { id: 1, names: 'Consumidor Final' };

      window.directSaleOrder = {
        id: null,
        is_available: true,
        products: event.detail.products,
        customer: { ...selectedCustomer },
        total: event.detail.total,
        delivery_address: ''
      }
      
      const config = Alpine.store('config');
      if (config && config.change) {
        window.dispatchEvent(new CustomEvent('open-change', {
          detail: window.directSaleOrder.total
        }));
      } else {
        directSaleStoreBill(window.directSaleOrder.total, 0, '1');
      }
    })

    window.addEventListener('store-bill', ({ detail }) => {
      if (window.directSaleOrder) {
        // Guardar parámetros
        window.directSalePendingParams = {
          cash: detail.cash,
          tip: detail.tip,
          paymentMethod: detail.paymentMethod
        };
        
        // Ahora sí crear la factura (ya tenemos el número de WhatsApp si aplica)
        directSaleStoreBill(detail.cash, detail.tip, detail.paymentMethod);
      }
    });

    window.addEventListener('set-customer', (event) => {
      if (event.detail && event.detail.names) {
        const alpineComponent = Alpine.$data(document.getElementById('direct-sale-root'));
        if (alpineComponent && alpineComponent.order) {
          const prev = alpineComponent.order;
          alpineComponent.order = { ...prev, customer: { ...event.detail } };
        }
        if (window.directSaleCurrentOrder) {
          window.directSaleCurrentOrder.customer = event.detail;
        }
        if (window.directSaleOrder) {
          window.directSaleOrder.customer = event.detail;
        }
      }
    });

    window.addEventListener('update-customer', (event) => {
      if (event.detail && event.detail.names) {
        const alpineComponent = Alpine.$data(document.getElementById('direct-sale-root'));
        if (alpineComponent && alpineComponent.order) {
          const prev = alpineComponent.order;
          alpineComponent.order = { ...prev, customer: { ...event.detail } };
        }
        if (window.directSaleCurrentOrder) {
          window.directSaleCurrentOrder.customer = event.detail;
        }
        if (window.directSaleOrder) {
          window.directSaleOrder.customer = event.detail;
        }
      }
    });

    function directSaleStoreBill(cash, tip = 0, paymentMethod = '1') {
      @this.call('storeBill', window.directSaleOrder, cash, tip, paymentMethod).then((result) => {
        if (result === 'success') {
          window.directSaleOrder = null;
          window.dispatchEvent(new CustomEvent('reset-cart'));
        }
      });
    }

    // Variable para almacenar el ID de la factura creada
    window.directSaleLastBillId = null;

    // Escuchar evento de factura creada con PDF URL listo
    window.addEventListener('bill-created', (event) => {
      if (event.detail && event.detail.billId) {
        window.directSaleLastBillId = event.detail.billId;
        const pdfUrl = event.detail.pdfUrl;
        console.log('📝 Factura creada con ID:', window.directSaleLastBillId, 'PDF URL:', pdfUrl);
        console.log('🔍 Estado de variables:', {
          wantsWhatsapp: window.directSaleWantsWhatsapp,
          hasPhone: !!window.directSaleWhatsappPhone,
          phone: window.directSaleWhatsappPhone,
          hasPdfUrl: !!pdfUrl
        });
        
        // Si el usuario quiere WhatsApp Y tenemos el PDF URL listo, enviar ahora
        if (window.directSaleWantsWhatsapp && window.directSaleWhatsappPhone && pdfUrl) {
          console.log('📤 Enviando PDF por WhatsApp inmediatamente');
          sendWhatsappNow(window.directSaleLastBillId, window.directSaleWhatsappPhone);
        } else {
          console.log('⏭️ No se enviará por WhatsApp:', {
            reason: !window.directSaleWantsWhatsapp ? 'Usuario no quiere WhatsApp' :
                    !window.directSaleWhatsappPhone ? 'No hay teléfono' :
                    !pdfUrl ? 'No hay PDF URL' : 'Desconocido'
          });
          
          // Si el usuario omitió WhatsApp, recargar después de un momento
          if (!window.directSaleWantsWhatsapp) {
            setTimeout(() => {
              console.log('🔄 Recargando página después de omitir WhatsApp...');
              window.location.reload();
            }, 1500); // Dar tiempo para que se descargue el PDF
          }
        }
      }
    });

    // Función NUEVA: Mostrar modal ANTES de todo el proceso
    window.showWhatsappConfirmation = function() {
      // Resetear variables
      window.directSaleWantsWhatsapp = false;
      window.directSaleWhatsappPhone = null;
      
      window.dispatchEvent(new CustomEvent('open-whatsapp-modal', {
        detail: {
          onConfirm: (phoneNumber) => {
            console.log('📱 WhatsApp confirmado con número:', phoneNumber);
            // Guardar el número para después
            window.directSaleWantsWhatsapp = true;
            window.directSaleWhatsappPhone = phoneNumber;
            
            // Ahora sí continuar con el flujo normal
            // Disparar el evento que normalmente dispararía el botón Facturar
            continuarConFacturacion();
          },
          onSkip: () => {
            console.log('⏭️ WhatsApp omitido');
            window.directSaleWantsWhatsapp = false;
            window.directSaleWhatsappPhone = null;
            
            // Continuar con el flujo normal sin WhatsApp
            continuarConFacturacion();
          }
        }
      }));
    };

    // Función auxiliar para continuar con la facturación
    function continuarConFacturacion() {
      // Buscar el elemento del carrito
      const cartElement = document.querySelector('[x-data="alpineCart"]');
      if (!cartElement) {
        console.error('No se encontró el componente del carrito');
        return;
      }

      // Obtener el componente Alpine
      const alpineCart = Alpine.$data(cartElement);
      if (!alpineCart) {
        console.error('No se pudo obtener los datos de Alpine del carrito');
        return;
      }

      // Verificar que hay productos
      if (!alpineCart.products || !alpineCart.products.length) {
        Livewire.emit('alert', 'Agrega uno o mas productos');
        return;
      }

      // Disparar el evento set-change manualmente
      window.dispatchEvent(new CustomEvent('set-change', {
        detail: {
          products: JSON.parse(JSON.stringify(alpineCart.products)),
          total: alpineCart.total
        }
      }));
    }

    // Función para enviar WhatsApp una vez que tenemos el PDF URL
    async function sendWhatsappNow(billId, phoneNumber) {
      try {
        console.log('🚀 sendWhatsappNow - Llamando a Livewire', { billId, phoneNumber });
        const result = await @this.call('sendBillViaWhatsapp', billId, phoneNumber);
        console.log('✅ sendWhatsappNow - Respuesta de Livewire:', result);
        // Limpiar variables
        window.directSaleWantsWhatsapp = false;
        window.directSaleWhatsappPhone = null;
        
        // Recargar página después de enviar WhatsApp
        setTimeout(() => {
          console.log('🔄 Recargando página después de enviar WhatsApp...');
          window.location.reload();
        }, 1500); // Dar tiempo para que el usuario vea el mensaje de éxito
      } catch (error) {
        console.error('❌ Error enviando WhatsApp:', error);
        // Incluso si hay error, recargar la página
        setTimeout(() => {
          console.log('🔄 Recargando página después de error...');
          window.location.reload();
        }, 1500);
      }
    }
  </script>

  <div class="sticky top-14 z-20 mb-2 w-full bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/70 border-b border-slate-200">
    <div class="flex items-center justify-between gap-1 sm:gap-2 px-2 md:px-4 py-2">

      <button onclick="window.history.back()"
        class="inline-flex items-center gap-1 sm:gap-2 rounded-md bg-cyan-500/90 px-2 sm:px-3 py-1.5 text-white text-sm hover:bg-cyan-600 focus:outline-none focus:ring-0">
        <i class="ico icon-arrow-l text-sm sm:text-base"></i>
        <span class="font-semibold hidden sm:inline">Volver</span>
      </button>

      <div class="flex items-center gap-1 sm:gap-2 flex-1 justify-center">
        <i class="ti ti-cash text-base sm:text-lg md:text-xl text-cyan-600"></i>
        <span class="font-semibold text-sm sm:text-base md:text-lg">Venta en caja</span>
      </div>

      <div class="flex items-center gap-1 sm:gap-2">
        <span class="text-slate-600 hidden sm:inline text-xs">Cliente:</span>
        <span class="font-semibold truncate max-w-[6rem] sm:max-w-[9rem] md:max-w-[14rem] text-xs sm:text-sm" x-text="order.customer.names">Consumidor Final</span>

        <button @click="directSaleShowCustomers()"
          class="inline-flex items-center justify-center h-6 w-6 sm:h-7 sm:w-7 rounded-md text-cyan-600 hover:bg-cyan-50 focus:outline-none focus:ring-0 border-0"
          title="Seleccionar cliente">
          <i class="ti ti-user text-sm sm:text-base"></i>
        </button>

        <button @click="Livewire.emitTo('admin.customers.create', 'openCreate')"
          class="inline-flex items-center justify-center h-6 w-6 sm:h-7 sm:w-7 rounded-md text-green-600 hover:bg-green-50 focus:outline-none focus:ring-0 border-0"
          title="Crear cliente nuevo">
          <i class="ti ti-user-plus text-sm sm:text-base"></i>
        </button>
      </div>

    </div>
  </div>

  <!-- Vista de productos y carrito -->
  <div class="flex flex-col xl:flex-row space-y-4 xl:space-y-0 xl:space-x-4">

    <div class="w-full xl:w-3/5 2xl:w-2/3">
      <livewire:admin.quick-sale.products />
      @include('livewire.admin.quick-sale.presentations')
    </div>

    <div class="w-full xl:w-2/5 2xl:w-1/3">
      @include('livewire.admin.quick-sale.cart')
    </div>

  </div>


@push('js')
<script>
window.addEventListener('download-bill', async event => {
  try {
    // Construir URL con el mismo esquema/host del navegador para evitar mixed content
    const base = window.location.origin.replace(/\/$/, '');
    const url = `${base}/administrador/vender/facturas-download/${event.detail.id}`;

    const res = await fetch(url, {
      method: 'GET',
      credentials: 'include',
      headers: { 'Accept': 'application/pdf,*/*;q=0.9' }
    });

    if (!res.ok) {
      let body = '';
      try { body = await res.text(); } catch(e) {}
      console.error('Descarga fallida HTTP', res.status, res.statusText, body?.substring(0, 500));
      return;
    }

    const type = (res.headers.get('Content-Type') || '').toLowerCase();
    if (!type.includes('pdf')) {
      const text = await res.text();
      console.warn('Respuesta no-PDF. Primeros 300 chars:', text.substring(0, 300));
      return;
    }

    const blob = await res.blob();
    const objectUrl = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = objectUrl;
    a.download = `Factura-${event.detail.id}.pdf`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(objectUrl);
  } catch (e) {
    console.error('Error en descarga', e);
  }
});
</script>
@endpush
</div>