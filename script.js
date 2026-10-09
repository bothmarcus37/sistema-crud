// Validación del formulario antes de enviarlo (el servidor vuelve a validar).
const formulario = document.getElementById('formCliente');

if (formulario) {
    const aviso = document.getElementById('errorFormulario');

    formulario.addEventListener('submit', function (e) {
        const nombre = document.getElementById('nombre');
        const correo = document.getElementById('correo');
        const servicio = document.getElementById('tipo_servicio');
        let mensaje = '';
        let campo = null;

        if (nombre.value.trim() === '') {
            mensaje = 'Escribe el nombre del cliente.';
            campo = nombre;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo.value.trim())) {
            mensaje = 'Escribe un correo válido, por ejemplo nombre@dominio.com.';
            campo = correo;
        } else if (servicio.value === '') {
            mensaje = 'Selecciona un servicio.';
            campo = servicio;
        }

        if (mensaje) {
            e.preventDefault(); // no se envía hasta corregir
            aviso.textContent = mensaje;
            aviso.hidden = false;
            campo.focus();
        }
    });
}

// Eliminar pide confirmación en el mismo botón: el primer clic pregunta, el segundo borra.
document.querySelectorAll('.form-eliminar').forEach(function (form) {
    const boton = form.querySelector('button');
    let confirmado = false;

    form.addEventListener('submit', function (e) {
        if (confirmado) return;
        e.preventDefault();
        confirmado = true;
        boton.textContent = '¿Seguro? Eliminar';
        boton.classList.add('boton--confirmar');

        // Si no confirma en 4 segundos, el botón vuelve a la normalidad
        setTimeout(function () {
            confirmado = false;
            boton.textContent = 'Eliminar';
            boton.classList.remove('boton--confirmar');
        }, 4000);
    });
});
