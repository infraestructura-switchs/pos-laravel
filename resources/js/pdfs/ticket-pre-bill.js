export default () => ({
  show: false,
  customer: {},
  bill: {},
  range: {},
  products: {},
  delivery_address: '',
  init() {
    window.addEventListener('print-pre-ticket', (event) => {
      if (!this.$store.config.print) {
        return
      }
      this.show = true
      this.getBill(event.detail)
      
      this.$nextTick(() => {
        this.setHeight()
        
        setTimeout(() => {
          window.print()
          
          setTimeout(() => {
            this.products = {}
            this.show = false
          }, 1000)
        }, 100)
      })
    })
  },

  getBill(bill) {
        this.customer = bill.customer
        this.bill = bill.bill
        this.range = bill.range
        this.products = bill.products
        this.delivery_address = bill.delivery_address
  },

  setHeight() {
    // Usar elemento style único para evitar conflictos
    let styleId = 'page-rule-pre-bill'
    let style = document.getElementById(styleId)
    
    if (!style) {
      style = document.createElement('style')
      style.id = styleId
      document.head.appendChild(style)
    }

    let oneLine = 0
    let twoLine = 0

    this.products.forEach((element) => {
      if (element.name.length <= 31) {
        oneLine++
      } else {
        twoLine++
      }
    })

    let height = 158 + oneLine * 4.2 + twoLine * 7.7
    const width = this.$store.config.widthTicket

    style.innerHTML = `@page { size: ${width}mm ${height}mm; margin: 0cm;}`
  },
})
