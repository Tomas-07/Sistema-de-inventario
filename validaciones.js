// assets/js/validaciones.js
// Validaciones del lado del cliente. El servidor (PHP) vuelve a validar todo.

document.addEventListener('DOMContentLoaded', function () {

    // 1) Validación general: todo formulario con la clase "needs-validation"
    document.querySelectorAll('form.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            // Quita espacios sobrantes en los campos de texto
            form.querySelectorAll('input[type="text"], input[type="email"]').forEach(function (campo) {
                campo.value = campo.value.trim();
            });

            // checkValidity() revisa required, min, step, maxlength, etc.
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // 2) Formulario de movimientos: una salida no puede superar el stock disponible
    var form = document.getElementById('form-movimiento');
    if (!form) { return; }

    var selProducto = document.getElementById('id_producto');
    var selTipo     = document.getElementById('tipo');
    var inpCantidad = document.getElementById('cantidad');
    var infoStock   = document.getElementById('info-stock');
    var msgCantidad = document.getElementById('msg-cantidad');
    var msgDefecto  = msgCantidad.textContent;

    function revisar() {
        var opcion   = selProducto.options[selProducto.selectedIndex];
        var hayStock = opcion && opcion.dataset.stock !== undefined;
        var stock    = hayStock ? parseInt(opcion.dataset.stock, 10) : null;
        var cantidad = parseInt(inpCantidad.value, 10);

        infoStock.textContent = hayStock ? 'Stock disponible: ' + stock : '';
        inpCantidad.setCustomValidity('');
        msgCantidad.textContent = msgDefecto;

        if (selTipo.value === 'SALIDA' && hayStock && !isNaN(cantidad) && cantidad > stock) {
            var texto = 'No puedes sacar más de ' + stock + ' unidades.';
            inpCantidad.setCustomValidity(texto);
            msgCantidad.textContent = texto;
        }
    }

    selProducto.addEventListener('change', revisar);
    selTipo.addEventListener('change', revisar);
    inpCantidad.addEventListener('input', revisar);
    revisar();
});
