
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Órdenes de entrega | MediTrack EPS</title>

    @vite([
        'resources/css/partials.css',
        'resources/css/ordenes-entrega.css',
        'resources/js/partials.js'
    ])
</head>
<body>

@include('partials.sidebar')

<main class="main-content content">
    @include('partials.topbar')

    <div class="oe-container">

        <div class="oe-header">
            <div>
                <h1>Órdenes de entrega</h1>
                <p>Gestiona la entrega de medicamentos prescritos y actualiza el inventario de cada sede.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="oe-alert oe-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="oe-alert oe-error">
                <strong>No se pudo completar la operación.</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="oe-alert oe-error">
                {{ session('error') }}
            </div>
        @endif

        {{-- SELECCIÓN DE SEDE --}}
        <section class="oe-card">
            <div class="oe-section-title">
                <div class="oe-icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <div>
                    <h2>Sede de entrega</h2>
                    <p>Selecciona dónde se realizará la entrega de los medicamentos.</p>
                </div>
            </div>

            <label for="idSede">Sede</label>
            <select id="idSede" class="oe-input">
                <option value="">Selecciona una sede</option>

                @foreach($sedes as $sede)
                    <option value="{{ $sede->idSede }}">
                        {{ $sede->nombre }} - {{ $sede->direccion }}
                    </option>
                @endforeach
            </select>

            <small>El inventario descontado corresponderá a la sede seleccionada.</small>
        </section>

        {{-- BÚSQUEDA DE PACIENTE --}}
        <section class="oe-card">
            <div class="oe-section-title">
                <div class="oe-icon">
                    <i class="fas fa-user-search"></i>
                </div>
                <div>
                    <h2>Buscar paciente</h2>
                    <p>Busca por número de documento, nombre o apellido.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('empresa.ordenes.index') }}" class="oe-search">
                <input
                    type="text"
                    name="busqueda"
                    value="{{ $busqueda }}"
                    placeholder="Documento o nombre del paciente..."
                    class="oe-input"
                    required
                >

                <button type="submit" class="oe-button">
                    <i class="fas fa-search"></i>
                    Buscar
                </button>
            </form>

            @if($pacientes->isNotEmpty())
                <div class="oe-results">
                    <h3>Pacientes encontrados</h3>

                    @foreach($pacientes as $resultado)
                        <form method="GET" action="{{ route('empresa.ordenes.index') }}">
                            <input type="hidden" name="busqueda" value="{{ $busqueda }}">
                            <input type="hidden" name="paciente_id" value="{{ $resultado->id }}">

                            <button type="submit" class="oe-patient-result">
                                <span>
                                    <strong>{{ $resultado->nombre }} {{ $resultado->apellido }}</strong>
                                    <small>Documento: {{ $resultado->documento }}</small>
                                </span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                    @endforeach
                </div>
            @elseif($busqueda !== '' && !$paciente)
                <div class="oe-empty">
                    No se encontró ningún paciente con esos datos en tu EPS.
                </div>
            @endif

            @if($paciente)
                <div class="oe-patient">
                    <div class="oe-patient-avatar">
                        <i class="fas fa-user"></i>
                    </div>

                    <div>
                        <span>Paciente seleccionado</span>
                        <h3>{{ $paciente->nombre }} {{ $paciente->apellido }}</h3>
                        <p>Documento: {{ $paciente->documento }}</p>
                    </div>
                </div>
            @endif
        </section>

        {{-- FÓRMULAS --}}
        @if($paciente)
            <section class="oe-card">
                <div class="oe-section-title">
                    <div class="oe-icon">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <div>
                        <h2>Fórmulas médicas activas</h2>
                        <p>Selecciona la fórmula correspondiente a la entrega.</p>
                    </div>
                </div>

                @forelse($formulas as $formula)
                    <div class="oe-formula">
                        <div class="oe-formula-info">
                            <div>
                                <strong>Fórmula #{{ $formula->idFormula }}</strong>
                                <span>{{ $formula->fechaFormula }}</span>
                            </div>

                            <div>
                                <small>Doctor</small>
                                <strong>
                                    {{ $formula->doctor->nombre ?? '' }}
                                    {{ $formula->doctor->apellido ?? '' }}
                                </strong>
                            </div>

                            <span class="oe-status">Activa</span>
                        </div>

                        <div class="oe-formula-actions">
                            <a
                                href="{{ route('empresa.ordenes.index', [
                                    'busqueda' => $busqueda,
                                    'paciente_id' => $paciente->id,
                                    'idFormula' => $formula->idFormula
                                ]) }}"
                                class="oe-button oe-button-secondary"
                            >
                                Ver medicamentos
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="oe-empty">
                        Este paciente no tiene fórmulas médicas activas.
                    </div>
                @endforelse
            </section>
        @endif

        {{-- DETALLE DE LA ENTREGA --}}
        @if($formulaSeleccionada)
            <section class="oe-card">
                <div class="oe-section-title">
                    <div class="oe-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div>
                        <h2>Medicamentos de la fórmula #{{ $formulaSeleccionada->idFormula }}</h2>
                        <p>Indica cuánto medicamento vas a entregar en esta operación.</p>
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('empresa.ordenes.guardar') }}"
                    id="oe-delivery-form"
                >
                    @csrf

                    <input type="hidden" name="idFormula" value="{{ $formulaSeleccionada->idFormula }}">
                    <input type="hidden" name="idSede" id="oe-hidden-sede">

                    <div class="oe-medicines">
                        @foreach($formulaSeleccionada->medicamentos as $detalle)
                            <div class="oe-medicine">
                                <div class="oe-medicine-heading">
                                    <h3>{{ $detalle->medicamento->nombre ?? 'Medicamento' }}</h3>

                                    @if($detalle->cantidadPendiente > 0)
                                        <span class="oe-pending">Pendiente</span>
                                    @else
                                        <span class="oe-complete">Entregado</span>
                                    @endif
                                </div>

                                <div class="oe-medicine-info">
                                    <p><strong>Dosis:</strong> {{ $detalle->dosis }}</p>
                                    <p><strong>Frecuencia:</strong> {{ $detalle->frecuencia }}</p>
                                    <p><strong>Duración:</strong> {{ $detalle->duracion }}</p>
                                    <p><strong>Indicaciones:</strong> {{ $detalle->indicaciones ?: 'Sin indicaciones adicionales' }}</p>
                                </div>

                                <div class="oe-quantity-info">
                                    <div>
                                        <span>Prescrito</span>
                                        <strong>{{ $detalle->cantidad }}</strong>
                                    </div>

                                    <div>
                                        <span>Entregado</span>
                                        <strong>{{ $detalle->cantidadEntregada }}</strong>
                                    </div>

                                    <div>
                                        <span>Pendiente</span>
                                        <strong>{{ $detalle->cantidadPendiente }}</strong>
                                    </div>
                                </div>

                                @if($detalle->cantidadPendiente > 0)
                                    <div class="oe-delivery-input">
                                        <label for="cantidad-{{ $detalle->idDetalle }}">
                                            Cantidad que entregar ahora
                                        </label>

                                        <input
                                            type="number"
                                            id="cantidad-{{ $detalle->idDetalle }}"
                                            name="cantidades[{{ $detalle->idDetalle }}]"
                                            class="oe-input"
                                            min="1"
                                            max="{{ $detalle->cantidadPendiente }}"
                                            value="{{ old('cantidades.'.$detalle->idDetalle) }}"
                                            placeholder="Máximo {{ $detalle->cantidadPendiente }}"
                                        >
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="oe-submit">
                        <button type="submit" class="oe-button" id="oe-submit-button">
                            <i class="fas fa-check-circle"></i>
                            Registrar entrega
                        </button>
                    </div>
                </form>
            </section>
        @endif

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sede = document.getElementById('idSede');
    const sedeOculta = document.getElementById('oe-hidden-sede');
    const formulario = document.getElementById('oe-delivery-form');

    function sincronizarSede() {
        if (sede && sedeOculta) {
            sedeOculta.value = sede.value;
        }
    }

    if (sede) {
        sede.addEventListener('change', sincronizarSede);
        sincronizarSede();
    }

    if (formulario) {
        formulario.addEventListener('submit', function (event) {
            sincronizarSede();

            if (!sede || !sede.value) {
                event.preventDefault();
                alert('Selecciona la sede desde donde realizarás la entrega.');
                return;
            }

            const cantidades = formulario.querySelectorAll(
                'input[name^="cantidades["]'
            );

            let hayCantidad = false;

            cantidades.forEach(function (input) {
                if (input.value.trim() !== '' && Number(input.value) > 0) {
                    hayCantidad = true;
                }
            });

            if (!hayCantidad) {
                event.preventDefault();
                alert('Ingresa al menos una cantidad para registrar la entrega.');
            }
        });
    }
});
</script>

</body>
</html>
