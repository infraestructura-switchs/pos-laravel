const downloadsInFlight = new Set()

function showToast(icon, title) {
  if (window.Swal) {
    window.Swal.mixin({
      toast: true,
      position: 'bottom-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      customClass: 'no-print',
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', window.Swal.stopTimer)
        toast.addEventListener('mouseleave', window.Swal.resumeTimer)
      },
    }).fire({ icon, title })
  } else if (window.Livewire) {
    window.Livewire.emit('alert', title)
  }
}

function normalizeDetail(detail) {
  if (!detail || typeof detail !== 'object') {
    return detail
  }

  const values = Object.values(detail)
  if (values.length === 1 && values[0] && typeof values[0] === 'object') {
    return values[0]
  }

  return detail
}

function resolveBillId(detail) {
  detail = normalizeDetail(detail)

  if (typeof detail === 'number' || typeof detail === 'string') {
    return String(detail)
  }

  if (detail && typeof detail === 'object') {
    if (detail.bill_id !== undefined && detail.bill_id !== null) {
      return String(detail.bill_id)
    }

    if (detail.id !== undefined && detail.id !== null) {
      return String(detail.id)
    }
  }

  return null
}

function resolveDownloadUrl(detail, billId) {
  detail = normalizeDetail(detail)

  if (detail && typeof detail === 'object' && typeof detail.download_url === 'string' && detail.download_url.length > 0) {
    return detail.download_url
  }

  if (window.location.pathname.includes('/vender/')) {
    return `/vender/facturas-download/${billId}`
  }

  return `/administrador/facturas-download/${billId}`
}

function downloadBillFile(detail) {
  const billId = resolveBillId(detail)

  if (!billId) {
    console.error('[ticket-bill] No se pudo resolver billId para descarga', detail)
    showToast('error', 'No se pudo descargar la factura. Intenta nuevamente.')
    return
  }

  if (downloadsInFlight.has(billId)) {
    console.info('[ticket-bill] Descarga ya en curso, omitiendo duplicado', { billId })
    return
  }

  downloadsInFlight.add(billId)
  showToast('info', 'Generando PDF, descarga iniciará en breve...')

  const downloadUrl = resolveDownloadUrl(detail, billId)

  console.info('[ticket-bill] Iniciando descarga', {
    billId,
    downloadUrl,
    rawDetail: detail,
  })

  fetch(downloadUrl, { credentials: 'same-origin' })
    .then(async (response) => {
      if (!response.ok) {
        const text = await response.text()
        throw new Error(text || `Error de red: ${response.status}`)
      }

      const disposition = response.headers.get('Content-Disposition') || ''
      const match = disposition.match(/filename="?([^"]+)"?/i)
      const filename = match?.[1] || `Factura-${billId}.pdf`

        const blob = await response.blob()
        const url = URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = filename
        document.body.appendChild(link)
        link.click()
        link.remove()
        URL.revokeObjectURL(url)
        showToast('success', 'Factura descargada correctamente')
      })
    .catch((error) => {
      console.error('[ticket-bill] Error en descarga', error)
      showToast('error', 'No se pudo descargar la factura. Intenta nuevamente.')
    })
    .finally(() => {
      downloadsInFlight.delete(billId)
    })
}

function getTicketBillComponent() {
  const root = document.querySelector('.print[x-data]') || document.querySelector('.print')

  if (!root || typeof Alpine === 'undefined' || typeof Alpine.$data !== 'function') {
    return null
  }

  return Alpine.$data(root)
}

function registerTicketBillListenersOnce() {
  if (window.__ticketBillListenersReady) {
    return
  }

  window.__ticketBillListenersReady = true

  window.addEventListener('direct-sale-download-ticket', (event) => {
    downloadBillFile(event.detail)
  })

  window.addEventListener('download-bill', (event) => {
    downloadBillFile(event.detail)
  })

  window.addEventListener('quick-sale-print-ticket', (event) => {
    const component = getTicketBillComponent()
    if (!component || !component.$store?.config?.print) return

    component.show = true
    component.getBill(`/administrador/facturas/informacion/${event.detail}`).then(() => {
      console.info('[ticket-bill] quick-sale-print-ticket', {
        billId: event.detail,
        typeBill: component.typeBill,
        isElectronic: component.isElectronic,
      })

      if (String(component.typeBill) !== '1') {
        component.show = false
        downloadBillFile(event.detail)
        return
      }

      component.$nextTick(() => {
        component.setHeight()
        window.print()
        component.products = {}
        component.show = false
      })
    })
  })

  window.addEventListener('print-ticket', (event) => {
    const component = getTicketBillComponent()
    if (!component) return

    component.show = true
    component.getBill(`/administrador/facturas/informacion/${event.detail}`).then(() => {
      console.info('[ticket-bill] print-ticket', {
        billId: event.detail,
        typeBill: component.typeBill,
        isElectronic: component.isElectronic,
      })

      if (String(component.typeBill) !== '1') {
        component.show = false
        downloadBillFile(event.detail)
        return
      }

      component.$nextTick(() => {
        component.setHeight()
        window.print()
        component.products = {}
        component.show = false
      })
    })
  })
}

registerTicketBillListenersOnce()

export default () => ({
  show: false,
  typeBill: '1',
  company: {},
  customer: {},
  bill: {},
  range: {},
  products: {},
  taxes: {},
  electronic_bill: {},
  isElectronic: false,

  init() {
    registerTicketBillListenersOnce()
  },

  normalizeDetail,
  resolveBillId,
  resolveDownloadUrl,

  downloadBill(detail) {
    downloadBillFile(detail)
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
        this.typeBill = String(data.data.type_bill ?? '1')
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
