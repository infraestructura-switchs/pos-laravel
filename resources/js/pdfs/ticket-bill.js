export default () => ({
  show: false,
  company: {},
  customer: {},
  bill: {},
  range: {},
  products: {},
  taxes: {},
  electronic_bill: {},
  isElectronic: false,

  init() {
    // NUEVO: Descarga automática para DirectSale (Vender)
    window.addEventListener('direct-sale-download-ticket', (event) => {
      this.downloadBill(event.detail)
    })

    // Mantener impresión para QuickSale (Ventas Rápidas)
    window.addEventListener('quick-sale-print-ticket', (event) => {
      if (!this.$store.config.print) return
      this.show = true
      this.getBill(`/administrador/facturas/informacion/${event.detail}`).then(() => {
        this.$nextTick(() => {
          this.setHeight()
          window.print()
          this.products = {}
          this.show = false
        })
      })
    })

    window.addEventListener('print-ticket', (event) => {
      this.show = true
      this.getBill(`/administrador/facturas/informacion/${event.detail}`).then(() => {
        this.$nextTick(() => {
          this.setHeight()
          window.print()
          this.products = {}
          this.show = false
        })
      })
    })
  },

  // NUEVO: Método para descargar automáticamente
  downloadBill(billId) {
    // Mostrar feedback visual (opcional)
    if (window.Livewire) {
      window.Livewire.emit('alert', 'Descargando factura...')
    }

    // Método 1: Simple y compatible con todos los navegadores
    const link = document.createElement('a')
    link.href = `/administrador/facturas-download/${billId}`
    link.download = `factura-${billId}.pdf`
    link.style.display = 'none'
    
    document.body.appendChild(link)
    link.click()
    
    // Limpiar
    setTimeout(() => {
      document.body.removeChild(link)
    }, 100)
  },

  getBill(url) {
    return fetch(url)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`Error de red: ${response.status}`)
        }
        return response.json()
      })
      .then((data) => {
        this.company = data.data.company
        this.customer = data.data.customer
        this.bill = data.data.bill
        this.range = data.data.range
        this.products = data.data.products
        this.taxes = data.data.taxes
        this.electronic_bill = data.data.electronic_bill
        this.isElectronic = data.data.is_electronic
      })
      .catch((error) => {
        console.error('Error al obtener datos:', error)
      })
  },

  setHeight() {
    let style = document.getElementById('page-rule')

    let oneLine = 0
    let twoLine = 0

    this.products.forEach((element) => {
      if (element.name.length <= 31) {
        oneLine++
      } else {
        twoLine++
      }
    })

    let height = Object.keys(this.range).length ? 12 : 0

    if (this.isElectronic) {
      height += 50
    }

    height += 182 + oneLine * 4.2 + twoLine * 7.7

    const width = this.$store.config.widthTicket

    style.innerHTML = `@page { size: ${width}mm ${height}mm; margin: 0cm;}`
  },
})