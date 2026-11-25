
function toggleDetalle(id) {
    const detalle = document.getElementById('detalle-' + id);

    if (detalle) {
        detalle.classList.toggle('hidden');
    }
}

window.toggleDetalle = toggleDetalle;


// Detección automática de medicamentos existentes.
document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('form-medicamento');
    const campoNombre = document.getElementById('nombre');
    const mensaje = document.getElementById('mensaje-deteccion');

    if (!formulario || !campoNombre || !mensaje) {
        return;
    }

    const urlDeteccion = formulario.dataset.urlDeteccion;

    const camposGenerales = {
        categoria: document.getElementById('categoria'),
        descripcion: document.getElementById('descripcion'),
        valor: document.getElementById('valor')
    };

    let temporizador = null;
    let solicitudActual = 0;

    // Guarda los valores que fueron completados automáticamente.
    let valoresAutocompletados = {};

    function limpiarAutocompletados() {
        Object.entries(camposGenerales).forEach(([nombre, campo]) => {
            if (
                campo &&
                valoresAutocompletados[nombre] !== undefined &&
                campo.value === String(valoresAutocompletados[nombre] ?? '')
            ) {
                campo.value = '';
            }
        });

        valoresAutocompletados = {};
    }

    function completarDatos(medicamento) {
        if (!medicamento) {
            return;
        }

        Object.entries(camposGenerales).forEach(([nombre, campo]) => {
            if (campo && medicamento[nombre] !== undefined) {
                campo.value = medicamento[nombre] ?? '';

                valoresAutocompletados[nombre] = campo.value;
            }
        });
    }

    campoNombre.addEventListener('input', () => {
        clearTimeout(temporizador);

        const nombre = campoNombre.value.trim();
        const numeroSolicitud = ++solicitudActual;

        limpiarAutocompletados();

        mensaje.hidden = true;
        mensaje.textContent = '';

        if (nombre.length < 2) {
            return;
        }

        mensaje.hidden = false;
        mensaje.textContent = 'Verificando medicamento...';

        temporizador = setTimeout(async () => {
            try {
                const respuesta = await fetch(
                    `${urlDeteccion}?nombre=${encodeURIComponent(nombre)}`,
                    {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!respuesta.ok) {
                    throw new Error('No se pudo consultar el medicamento.');
                }

                const resultado = await respuesta.json();

                // Evita mostrar resultados de búsquedas anteriores.
                if (numeroSolicitud !== solicitudActual) {
                    return;
                }

                mensaje.textContent = resultado.mensaje;
                mensaje.hidden = false;

                // Completa los datos generales si el medicamento existe.
                if (
                    resultado.tipo === 'existente' ||
                    resultado.tipo === 'otra_sede'
                ) {
                    completarDatos(resultado.medicamento);
                }

            } catch (error) {
                if (numeroSolicitud !== solicitudActual) {
                    return;
                }

                mensaje.textContent =
                    'No fue posible verificar el medicamento. Intenta nuevamente.';

                mensaje.hidden = false;

                console.error(error);
            }
        }, 400);
    });
});
