const taxRatesByTribute = (products) => {
  const totalsByTributeId = {}

  products.forEach((item) => {
    const grossValue = getGrossValue(item)

    const taxRates = item.tax_rates

    taxRates.forEach((element) => {
      //calcular el impuesto
      if (!totalsByTributeId[element.tribute_name]) {
        totalsByTributeId[element.tribute_name] = {
          name: element.tribute_name,
          value: 0,
        }
      }

      if (element.has_percentage) {
        const rate = Number(element.rate) / 100
        totalsByTributeId[element.tribute_name].value += custRound(grossValue * rate, 0)
      } else {
        totalsByTributeId[element.tribute_name].value += Number(element.value) * Number(item.amount)
      }
    })
  })

  return Object.values(totalsByTributeId)
}

const getGrossValue = (product) => {
  const total = (Number(product.price) * Number(product.amount)) - Number(product.discount)

  const sumPercentTax = product.tax_rates
    .map((item) => (item.has_percentage ? Number(item.rate) : 0))
    .reduce((acu, curr) => acu + curr / 100, 0)

  const sumValueTax = product.tax_rates
    .map((item) => (item.has_percentage ? 0 : Number(item.value)))
    .reduce((acu, curr) => acu + curr, 0)

  const result = total - sumValueTax * Number(product.amount)

  const rate = sumPercentTax + 1

  return custRound(result / rate)
}

const totalTaxRates = (taxRates) => {
  return taxRates.map((item) => Number(item.value)).reduce((prev, curr) => prev + curr, 0)
}

export { taxRatesByTribute, totalTaxRates }
