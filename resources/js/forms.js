// Configuración consistente con PHP: usar formato colombiano pero con punto decimal
window.options = { style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 };
window.numberFormat = new Intl.NumberFormat('es-CO', options);

window.onlyNumbers = function(event, allowAll=false) {
  if (!allowAll) {
    if(event.charCode >= 48 && event.charCode <= 57) return true;

    event.preventDefault();
    return false;
  }
}

window.validateInteger = (value) => {
    try {
        value = parseInt(value);
        if(!value && !Number.isInteger(value)){
            value = false
        }
    } catch (error) {
        value = false;
    }
    return value
}
