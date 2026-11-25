
document.addEventListener('DOMContentLoaded', function () {

    // =========================================
    // DATOS RECIBIDOS DESDE BLADE
    // =========================================

    const elementoDatos = document.getElementById('datosFormula');

    if (!elementoDatos) {
        console.error('No se encontraron los datos de la fórmula.');
        return;
    }

    const datos = JSON.parse(elementoDatos.textContent);

    const pacientes = datos.pacientes || [];
    const medicamentos = datos.medicamentos || [];

    // =========================================
    // ELEMENTOS PRINCIPALES
    // =========================================

    const formulario = document.getElementById('formularioFormula');

    const lista = document.getElementById('listaMedicamentos');
    const plantilla = document.getElementById('plantillaMedicamento');
    const botonAgregar = document.getElementById('agregarMedicamento');

    const buscarPaciente = document.getElementById('buscarPaciente');
    const idPaciente = document.getElementById('idPaciente');
    const resultadosPacientes = document.getElementById('resultadosPacientes');
    const estadoPaciente = document.getElementById('estadoPaciente');

    let indice = Number(datos.indiceInicial) || 0;

    // =========================================
    // BUSCADOR DE PACIENTES
    // =========================================

    function mostrarPacientes(termino) {

        resultadosPacientes.innerHTML = '';

        const busqueda = termino.trim().toLocaleLowerCase();

        if (busqueda.length === 0) {
            resultadosPacientes.classList.remove('visible');
            return;
        }

        const encontrados = pacientes.filter(function (paciente) {

            const texto = [
                paciente.nombre,
                paciente.apellido,
                paciente.documento,
                paciente.email
            ].filter(Boolean).join(' ').toLocaleLowerCase();

            return texto.includes(busqueda);

        }).slice(0, 10);

        if (encontrados.length === 0) {

            resultadosPacientes.innerHTML =
                '<div class="autocomplete-empty">No se encontraron pacientes.</div>';

            resultadosPacientes.classList.add('visible');
            return;
        }

        encontrados.forEach(function (paciente) {

            const opcion = document.createElement('button');

            opcion.type = 'button';
            opcion.className = 'autocomplete-option';

            const nombre = document.createElement('span');

            nombre.textContent =
                paciente.nombre + ' ' + paciente.apellido;

            const detalle = document.createElement('small');

            detalle.textContent =
                'Documento: ' + (paciente.documento || 'No registrado');

            opcion.appendChild(nombre);
            opcion.appendChild(detalle);

            opcion.addEventListener('mousedown', function (event) {

                event.preventDefault();

                buscarPaciente.value =
                    paciente.nombre + ' ' + paciente.apellido;

                idPaciente.value = paciente.id;

                estadoPaciente.textContent = 'Paciente seleccionado';

                resultadosPacientes.classList.remove('visible');

            });

            resultadosPacientes.appendChild(opcion);

        });

        resultadosPacientes.classList.add('visible');
    }

    buscarPaciente.addEventListener('input', function () {

        idPaciente.value = '';
        estadoPaciente.textContent = '';

        mostrarPacientes(this.value);

    });

    buscarPaciente.addEventListener('focus', function () {

        if (this.value.trim() !== '' && idPaciente.value === '') {
            mostrarPacientes(this.value);
        }

    });

    buscarPaciente.addEventListener('blur', function () {

        setTimeout(function () {
            resultadosPacientes.classList.remove('visible');
        }, 150);

    });

    // =========================================
    // BUSCADOR DE MEDICAMENTOS
    // =========================================

    function configurarBuscadorMedicamento(fila) {

        const input = fila.querySelector('.buscarMedicamento');
        const hidden = fila.querySelector('.idMedicamento');
        const resultados = fila.querySelector('.resultadosMedicamentos');
        const estado = fila.querySelector('.estadoMedicamento');

        if (!input || !hidden || !resultados) {
            return;
        }

        function mostrarResultados(termino) {

            resultados.innerHTML = '';

            const busqueda = termino.trim().toLocaleLowerCase();

            if (busqueda.length === 0) {
                resultados.classList.remove('visible');
                return;
            }

            const encontrados = medicamentos.filter(function (medicamento) {

                const texto = [
                    medicamento.nombre,
                    medicamento.categoria
                ].filter(Boolean).join(' ').toLocaleLowerCase();

                return texto.includes(busqueda);

            }).slice(0, 10);

            if (encontrados.length === 0) {

                resultados.innerHTML =
                    '<div class="autocomplete-empty">No se encontraron medicamentos.</div>';

                resultados.classList.add('visible');
                return;
            }

            encontrados.forEach(function (medicamento) {

                const opcion = document.createElement('button');

                opcion.type = 'button';
                opcion.className = 'autocomplete-option';

                const nombre = document.createElement('span');

                nombre.textContent = medicamento.nombre;

                const detalle = document.createElement('small');

                detalle.textContent =
                    medicamento.categoria || 'Sin categoría';

                opcion.appendChild(nombre);
                opcion.appendChild(detalle);

                opcion.addEventListener('mousedown', function (event) {

                    event.preventDefault();

                    input.value = medicamento.nombre;
                    hidden.value = medicamento.id;

                    if (estado) {
                        estado.textContent = 'Medicamento seleccionado';
                    }

                    resultados.classList.remove('visible');

                });

                resultados.appendChild(opcion);

            });

            resultados.classList.add('visible');
        }

        input.addEventListener('input', function () {

            hidden.value = '';

            if (estado) {
                estado.textContent = '';
            }

            mostrarResultados(this.value);

        });

        input.addEventListener('focus', function () {

            if (this.value.trim() !== '' && hidden.value === '') {
                mostrarResultados(this.value);
            }

        });

        input.addEventListener('blur', function () {

            setTimeout(function () {
                resultados.classList.remove('visible');
            }, 150);

        });
    }

    // =========================================
    // CONFIGURAR BUSCADORES EXISTENTES
    // =========================================

    function configurarTodosLosBuscadores() {

        lista.querySelectorAll('.medicamento-card').forEach(function (fila) {
            configurarBuscadorMedicamento(fila);
        });

    }

    configurarTodosLosBuscadores();

    // =========================================
    // AGREGAR MEDICAMENTOS
    // =========================================

    botonAgregar.addEventListener('click', function () {

        const html = plantilla.innerHTML.replaceAll('INDEX', indice);

        lista.insertAdjacentHTML('beforeend', html);

        const nuevaFila = lista.lastElementChild;

        configurarBuscadorMedicamento(nuevaFila);

        indice++;

    });

    // =========================================
    // QUITAR MEDICAMENTOS
    // =========================================

    lista.addEventListener('click', function (event) {

        if (event.target.classList.contains('eliminarMedicamento')) {

            const filas = lista.querySelectorAll('.medicamento-card');

            if (filas.length > 1) {

                event.target.closest('.medicamento-card').remove();

            } else {

                alert('La fórmula debe tener al menos un medicamento.');

            }
        }

    });

    // =========================================
    // VALIDACIÓN ANTES DE ENVIAR
    // =========================================

    formulario.addEventListener('submit', function (event) {

        if (!idPaciente.value) {

            event.preventDefault();

            buscarPaciente.setCustomValidity(
                'Debes seleccionar un paciente de la lista.'
            );

            buscarPaciente.reportValidity();

            buscarPaciente.setCustomValidity('');

            return;
        }

        const filas = lista.querySelectorAll('.medicamento-card');

        for (const fila of filas) {

            const input = fila.querySelector('.buscarMedicamento');
            const hidden = fila.querySelector('.idMedicamento');

            if (!hidden.value) {

                event.preventDefault();

                input.setCustomValidity(
                    'Debes seleccionar un medicamento de la lista.'
                );

                input.reportValidity();

                input.setCustomValidity('');

                return;
            }
        }

    });

    // =========================================
    // HU-027 - DISPONIBILIDAD POR SEDE
    // =========================================

    const panelDisponibilidad = document.getElementById('panelDisponibilidad');

    if (panelDisponibilidad) {

        const botonToggle = document.getElementById('toggleDisponibilidad');
        const contenido = document.getElementById('contenidoDisponibilidad');

        const buscarDisponibilidad = document.getElementById('buscarDisponibilidad');
        const idMedicamentoDisponibilidad = document.getElementById('idMedicamentoDisponibilidad');
        const resultadosDisponibilidad = document.getElementById('resultadosDisponibilidad');

        const estadoBusqueda = document.getElementById('estadoBusquedaDisponibilidad');
        const resultadosSedes = document.getElementById('resultadosDisponibilidadSedes');

        const urlTemplate = panelDisponibilidad.dataset.urlTemplate;

        let consultaActual = 0;

        // -------------------------------------
        // MOSTRAR Y OCULTAR PANEL
        // -------------------------------------

        botonToggle.addEventListener('click', function () {

            const abierto = !contenido.hidden;

            contenido.hidden = abierto;

            botonToggle.setAttribute('aria-expanded', String(!abierto));

            botonToggle.textContent = abierto
                ? 'Consultar disponibilidad'
                : 'Ocultar disponibilidad';

            if (!abierto) {
                buscarDisponibilidad.focus();
            }

        });

        // -------------------------------------
        // MENSAJES
        // -------------------------------------

        function mostrarMensaje(contenedor, mensaje, tipo = '') {

            contenedor.replaceChildren();

            const elemento = document.createElement('div');

            elemento.className = 'disponibilidad-mensaje';

            if (tipo) {
                elemento.classList.add(tipo);
            }

            elemento.textContent = mensaje;

            contenedor.appendChild(elemento);
        }

        // -------------------------------------
        // AUTOCOMPLETADO DE MEDICAMENTOS
        // -------------------------------------

        function mostrarMedicamentosDisponibilidad(termino) {

            resultadosDisponibilidad.replaceChildren();

            const busqueda = termino.trim().toLocaleLowerCase();

            if (!busqueda) {
                resultadosDisponibilidad.classList.remove('visible');
                return;
            }

            const encontrados = medicamentos.filter(function (medicamento) {

                const texto = [
                    medicamento.nombre,
                    medicamento.categoria
                ].filter(Boolean).join(' ').toLocaleLowerCase();

                return texto.includes(busqueda);

            }).slice(0, 10);

            if (encontrados.length === 0) {

                const vacio = document.createElement('div');

                vacio.className = 'autocomplete-empty';
                vacio.textContent = 'No se encontraron medicamentos.';

                resultadosDisponibilidad.appendChild(vacio);
                resultadosDisponibilidad.classList.add('visible');

                return;
            }

            encontrados.forEach(function (medicamento) {

                const opcion = document.createElement('button');

                opcion.type = 'button';
                opcion.className = 'autocomplete-option';

                const nombre = document.createElement('span');
                nombre.textContent = medicamento.nombre;

                const categoria = document.createElement('small');
                categoria.textContent = medicamento.categoria || 'Sin categoría';

                opcion.appendChild(nombre);
                opcion.appendChild(categoria);

                opcion.addEventListener('mousedown', function (event) {

                    event.preventDefault();

                    buscarDisponibilidad.value = medicamento.nombre;
                    idMedicamentoDisponibilidad.value = medicamento.id;

                    resultadosDisponibilidad.classList.remove('visible');

                    estadoBusqueda.textContent = 'Consultando disponibilidad...';

                    consultarDisponibilidad(medicamento);

                });

                resultadosDisponibilidad.appendChild(opcion);

            });

            resultadosDisponibilidad.classList.add('visible');
        }

        buscarDisponibilidad.addEventListener('input', function () {

            idMedicamentoDisponibilidad.value = '';

            // Invalida cualquier consulta anterior.
            consultaActual++;

            resultadosSedes.replaceChildren();

            estadoBusqueda.textContent = '';

            mostrarMedicamentosDisponibilidad(this.value);

        });

        buscarDisponibilidad.addEventListener('focus', function () {

            if (this.value.trim() !== '' && !idMedicamentoDisponibilidad.value) {
                mostrarMedicamentosDisponibilidad(this.value);
            }

        });

        buscarDisponibilidad.addEventListener('blur', function () {

            setTimeout(function () {
                resultadosDisponibilidad.classList.remove('visible');
            }, 150);

        });

        // -------------------------------------
        // CONSULTAR DISPONIBILIDAD EN EL SERVIDOR
        // -------------------------------------

        async function consultarDisponibilidad(medicamento) {

            if (!urlTemplate) {

                estadoBusqueda.textContent = 'No se configuró la ruta de disponibilidad.';

                mostrarMensaje(
                    resultadosSedes,
                    'No se pudo obtener la dirección del servicio.',
                    'error'
                );

                return;
            }

            const numeroConsulta = ++consultaActual;

            const url = urlTemplate.replace('__ID__', encodeURIComponent(medicamento.id));

            resultadosSedes.replaceChildren();

            mostrarMensaje(
                resultadosSedes,
                'Consultando existencias en las sedes...',
                'cargando'
            );

            try {

                const respuesta = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!respuesta.ok) {
                    throw new Error('No fue posible consultar la disponibilidad.');
                }

                const sedes = await respuesta.json();

                // Evita mostrar resultados de una búsqueda anterior.
                if (numeroConsulta !== consultaActual) {
                    return;
                }

                if (!Array.isArray(sedes)) {
                    throw new Error('La respuesta del servidor no tiene el formato esperado.');
                }

                estadoBusqueda.textContent =
                    'Disponibilidad consultada para: ' + medicamento.nombre;

                mostrarResultadosSedes(sedes, medicamento);

            } catch (error) {

                if (numeroConsulta !== consultaActual) {
                    return;
                }

                console.error('Error consultando disponibilidad:', error);

                estadoBusqueda.textContent = 'No se pudo completar la consulta.';

                mostrarMensaje(
                    resultadosSedes,
                    error.message || 'Ocurrió un error al consultar las sedes.',
                    'error'
                );

            }
        }

        // -------------------------------------
        // MOSTRAR RESULTADOS DE LAS SEDES
        // -------------------------------------

        function mostrarResultadosSedes(sedes, medicamento) {

            resultadosSedes.replaceChildren();

            if (sedes.length === 0) {

                mostrarMensaje(
                    resultadosSedes,
                    'No hay sedes activas registradas para consultar.'
                );

                return;
            }

            const encabezado = document.createElement('div');
            encabezado.className = 'disponibilidad-resultados-heading';

            const titulo = document.createElement('div');

            const nombre = document.createElement('h3');
            nombre.textContent = medicamento.nombre;

            const descripcion = document.createElement('p');
            descripcion.textContent =
                'Existencias registradas en ' + sedes.length +
                (sedes.length === 1 ? ' sede' : ' sedes');

            titulo.appendChild(nombre);
            titulo.appendChild(descripcion);

            encabezado.appendChild(titulo);

            resultadosSedes.appendChild(encabezado);

            const contenedorSedes = document.createElement('div');
            contenedorSedes.className = 'disponibilidad-sedes';

            sedes.forEach(function (sede) {

                const tarjeta = document.createElement('article');
                tarjeta.className = 'disponibilidad-sede';

                // Nombre de la sede.
                const encabezadoSede = document.createElement('div');
                encabezadoSede.className = 'disponibilidad-sede-heading';

                const nombreSede = document.createElement('h4');
                nombreSede.textContent = sede.nombre || 'Sede sin nombre';

                const estado = document.createElement('span');
                estado.className = 'disponibilidad-estado';

                const estadoTexto = sede.estado || 'Sin inventario';

                estado.textContent = estadoTexto;

                const estadoClase = {
                    'Disponible': 'disponible',
                    'Existencias bajas': 'bajo',
                    'Agotado': 'agotado',
                    'Inventario inactivo': 'inactivo',
                    'Sin inventario': 'sin-inventario'
                };

                estado.classList.add(
                    estadoClase[estadoTexto] || 'sin-inventario'
                );

                encabezadoSede.appendChild(nombreSede);
                encabezadoSede.appendChild(estado);

                tarjeta.appendChild(encabezadoSede);

                // Dirección.
                const direccion = document.createElement('p');
                direccion.className = 'disponibilidad-sede-direccion';

                direccion.textContent =
                    sede.direccion || 'Dirección no registrada';

                tarjeta.appendChild(direccion);

                // Stock.
                const bloqueStock = document.createElement('div');
                bloqueStock.className = 'disponibilidad-sede-stock';

                const etiquetaStock = document.createElement('span');
                etiquetaStock.textContent = 'Existencias disponibles';

                const cantidadStock = document.createElement('strong');

                cantidadStock.textContent =
                    Number(sede.stock || 0).toLocaleString('es-CO');

                bloqueStock.appendChild(etiquetaStock);
                bloqueStock.appendChild(cantidadStock);

                tarjeta.appendChild(bloqueStock);

                // Stock mínimo.
                const stockMinimo = document.createElement('small');
                stockMinimo.className = 'disponibilidad-stock-minimo';

                stockMinimo.textContent =
                    'Stock mínimo: ' +
                    Number(sede.stockMinimo || 0).toLocaleString('es-CO');

                tarjeta.appendChild(stockMinimo);

                contenedorSedes.appendChild(tarjeta);

            });

            resultadosSedes.appendChild(contenedorSedes);
        }

    }

});
