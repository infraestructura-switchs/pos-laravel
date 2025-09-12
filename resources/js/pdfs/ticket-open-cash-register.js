export default () => ({
  show: false,
  datetime: null,
  terminal: '',
  user: '',
  initialCash: 0,
  initialCoins: 0,
  totalInitial: 0,
  observations: '',

  init() {
    // Mantener compatibilidad con el evento anterior
    window.addEventListener('open-cash-register', () => {
      this.show = true
      this.dateTime()
      this.$nextTick(() => {
        setTimeout(() => {
          this.setHeight()
          window.print()
          this.show = false
        }, 300)
      })
    })

    // Nuevo evento con datos completos
    window.addEventListener('print-cash-opening', (event) => {
      const data = event.detail
      this.terminal = data.terminal
      this.user = data.user
      this.initialCash = data.initial_cash
      this.initialCoins = data.initial_coins
      this.totalInitial = data.total_initial
      this.observations = data.observations
      this.datetime = data.datetime
      
      this.show = true
      this.$nextTick(() => {
        setTimeout(() => {
          this.setHeight()
          window.print()
          this.show = false
        }, 300)
      })
    })
  },

  dateTime() {
    let fechaHoraActual = new Date()

    let fechaFormateada =
      (fechaHoraActual.getDate() < 10 ? '0' : '') +
      fechaHoraActual.getDate() +
      '-' +
      (fechaHoraActual.getMonth() + 1 < 10 ? '0' : '') +
      (fechaHoraActual.getMonth() + 1) +
      '-' +
      fechaHoraActual.getFullYear()

    let horaFormateada =
      (fechaHoraActual.getHours() % 12 || 12) +
      ':' +
      (fechaHoraActual.getMinutes() < 10 ? '0' : '') +
      fechaHoraActual.getMinutes() +
      ' ' +
      (fechaHoraActual.getHours() < 12 ? 'AM' : 'PM')

    this.datetime = fechaFormateada + ' ' + horaFormateada
  },
  setHeight() {
    let style = document.getElementById('page-rule')

    style.innerHTML = `@page { size: 80mm 50mm; margin: 0cm;}`
  },
})
