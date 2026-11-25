
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Validar fórmulas | MediTrack EPS</title>

    @vite([
        'resources/css/partials.css',
        'resources/css/validar-formulas.css',
        'resources/js/partials.js'
    ])
</head>
<body>

@include('partials.sidebar')
@include('partials.topbar')

<main class="content validar-formulas-content">

    <div class="validar-formulas-page">

        {{-- ENCABEZADO --}}
        <header class="vf-header">
            <div>
                <h1>Validar fórmulas</h1>
                <p>
                    Consulta las fórmulas médicas de los pacientes
                    antes de realizar la entrega de medicamentos.
                </p>
            </div>
        </header>

        {{-- MENSAJES --}}
        @if(session('success'))
            <div class="vf-alert vf-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="vf-alert vf-alert-danger">
                <strong>No se pudo realizar la consulta:</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- BUSCADOR --}}
        <section class="vf-panel">

            <div class="vf-panel-heading">
                <div class="vf-heading-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg>
                </div>

                <div>
                    <h2>Buscar paciente</h2>
                    <p>
                        Ingresa el documento para consultar sus fórmulas médicas.
                    </p>
                </div>
            </div>

           
            <form
                action="{{ route('empresa.formulas.index') }}"
                method="GET"
                class="vf-search-form"
            >
                <div class="vf-search-field">
                    <label for="busqueda">Documento o nombre del paciente</label>

                    <input
                        type="text"
                        id="busqueda"
                        name="busqueda"
                        value="{{ $busqueda }}"
                        placeholder="Ej. 1234567890 o Juan Pérez"
                        maxlength="100"
                        autocomplete="off"
                        required
                    >
                </div>

                <button type="submit" class="vf-btn-search">
                    <svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg>

                    Buscar paciente
                </button>

                @if($busqueda !== '')
                    <a
                        href="{{ route('empresa.formulas.index') }}"
                        class="vf-btn-clear"
                    >
                        Limpiar búsqueda
                    </a>
                @endif
            </form>


        </section>

      
{{-- RESULTADOS DE LA BÚSQUEDA --}}
@if($busqueda !== '')

    {{-- VARIOS PACIENTES ENCONTRADOS --}}
    @if($pacientes->isNotEmpty())

        <section class="vf-panel">

            <div class="vf-section-title">
                <div>
                    <h2>Seleccionar paciente</h2>
                    <p>
                        Encontramos varios pacientes. Verifica el documento
                        y selecciona a la persona correcta.
                    </p>
                </div>

                <span class="vf-count">
                    {{ $pacientes->count() }}
                    {{ $pacientes->count() === 1 ? 'resultado' : 'resultados' }}
                </span>
            </div>

            @foreach($pacientes as $resultado)

                <form
                    action="{{ route('empresa.formulas.index') }}"
                    method="GET"
                    class="vf-patient-result"
                >
                    <input
                        type="hidden"
                        name="busqueda"
                        value="{{ $busqueda }}"
                    >

                    <input
                        type="hidden"
                        name="paciente_id"
                        value="{{ $resultado->id }}"
                    >

                    <div class="vf-patient-data">
                        <span>Nombre completo</span>
                        <strong>
                            {{ $resultado->nombre }}
                            {{ $resultado->apellido }}
                        </strong>
                    </div>

                    <div class="vf-patient-data">
                        <span>Documento</span>
                        <strong>{{ $resultado->documento }}</strong>
                    </div>

                    <button type="submit" class="vf-btn-search">
                        Consultar fórmulas
                    </button>
                </form>

            @endforeach

        </section>

    {{-- PACIENTE ENCONTRADO --}}
    @elseif($paciente)

        <section class="vf-panel vf-patient-panel">

            <div class="vf-section-title">
                <div>
                    <h2>Información del paciente</h2>
                    <p>Identificación del paciente encontrado.</p>
                </div>

                <span class="vf-verified">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="m5 12 4 4L19 6"></path>
                    </svg>
                    Paciente encontrado
                </span>
            </div>

            <div class="vf-patient-grid">

                <div class="vf-patient-data">
                    <span>Nombre completo</span>
                    <strong>
                        {{ $paciente->nombre }}
                        {{ $paciente->apellido }}
                    </strong>
                </div>

                <div class="vf-patient-data">
                    <span>Documento</span>
                    <strong>{{ $paciente->documento }}</strong>
                </div>

                <div class="vf-patient-data">
                    <span>Correo electrónico</span>
                    <strong>
                        {{ $paciente->email ?: 'No registrado' }}
                    </strong>
                </div>

                <div class="vf-patient-data">
                    <span>Teléfono</span>
                    <strong>
                        {{ $paciente->telefono ?: 'No registrado' }}
                    </strong>
                </div>

            </div>

        </section>

        {{-- FÓRMULAS MÉDICAS --}}
        <section class="vf-panel">

            <div class="vf-section-title">
                <div>
                    <h2>Fórmulas médicas</h2>
                    <p>Fórmulas registradas para el paciente.</p>
                </div>

                <span class="vf-count">
                    {{ $formulas->count() }}
                    {{ $formulas->count() === 1 ? 'fórmula' : 'fórmulas' }}
                </span>
            </div>

            @forelse($formulas as $formula)

                <article class="vf-formula-card">

                    <div class="vf-formula-header">

                        <div class="vf-formula-info">
                            <span class="vf-formula-label">
                                Fórmula médica #{{ $formula->idFormula }}
                            </span>

                            <h3>
                                {{ $formula->fechaFormula
                                    ? $formula->fechaFormula->format('d/m/Y')
                                    : 'Fecha no disponible' }}
                            </h3>

                            <p>
                                Médico:
                                <strong>
                                    @if($formula->doctor)
                                        {{ $formula->doctor->nombre }}
                                        {{ $formula->doctor->apellido }}
                                    @else
                                        No disponible
                                    @endif
                                </strong>
                            </p>
                        </div>

                        <span class="vf-status
                            {{ strtolower($formula->estado) === 'activa'
                                ? 'vf-status-active'
                                : 'vf-status-other' }}">
                            {{ ucfirst($formula->estado) }}
                        </span>

                    </div>

                    @if($formula->observaciones)
                        <div class="vf-observations">
                            <strong>Observaciones generales</strong>
                            <p>{{ $formula->observaciones }}</p>
                        </div>
                    @endif

                    <div class="vf-medicines-heading">
                        <h4>Medicamentos prescritos</h4>
                        <span>
                            {{ $formula->medicamentos->count() }}
                            {{ $formula->medicamentos->count() === 1
                                ? 'medicamento'
                                : 'medicamentos' }}
                        </span>
                    </div>

                    @forelse($formula->medicamentos as $detalle)

                        <div class="vf-medicine-card">

                            <div class="vf-medicine-name">
                                <div class="vf-medicine-icon">
                                    <svg viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor"
                                         stroke-width="1.8"
                                         stroke-linecap="round"
                                         stroke-linejoin="round">
                                        <path d="m10 14 4-4"></path>
                                        <path d="M7 17a5 5 0 0 1 0-7l3-3a5 5 0 0 1 7 7l-3 3a5 5 0 0 1-7 0Z"></path>
                                    </svg>
                                </div>

                                <div>
                                    <span>Medicamento</span>
                                    <strong>
                                        {{ $detalle->medicamento->nombre
                                            ?? 'Medicamento no disponible' }}
                                    </strong>
                                </div>
                            </div>

                            <div class="vf-medicine-details">

                                <div>
                                    <span>Dosis</span>
                                    <strong>{{ $detalle->dosis }}</strong>
                                </div>

                                <div>
                                    <span>Frecuencia</span>
                                    <strong>{{ $detalle->frecuencia }}</strong>
                                </div>

                                <div>
                                    <span>Duración</span>
                                    <strong>{{ $detalle->duracion }}</strong>
                                </div>

                                <div>
                                    <span>Cantidad</span>
                                    <strong>{{ $detalle->cantidad }}</strong>
                                </div>

                                <div class="vf-indications">
                                    <span>Indicaciones</span>
                                    <strong>
                                        {{ $detalle->indicaciones
                                            ?: 'Sin indicaciones adicionales' }}
                                    </strong>
                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="vf-empty-medicines">
                            Esta fórmula no tiene medicamentos registrados.
                        </div>

                    @endforelse

                </article>

            @empty

                <div class="vf-empty">
                    <div class="vf-empty-icon">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.7"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path>
                            <path d="M14 2v6h6"></path>
                            <path d="M9 13h6"></path>
                            <path d="M9 17h6"></path>
                        </svg>
                    </div>

                    <h3>Sin fórmulas médicas</h3>

                    <p>
                        El paciente fue encontrado, pero todavía
                        no tiene fórmulas médicas registradas.
                    </p>
                </div>

            @endforelse

        </section>

    {{-- NINGÚN PACIENTE ENCONTRADO --}}
    @else

        <section class="vf-panel">

            <div class="vf-empty">
                <div class="vf-empty-icon vf-empty-icon-danger">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.7"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m16.5 16.5 4 4"></path>
                        <path d="M8 8l6 6"></path>
                        <path d="m14 8-6 6"></path>
                    </svg>
                </div>

                <h3>Paciente no encontrado</h3>

                <p>
                    No encontramos un paciente con el documento o nombre
                    <strong>{{ $busqueda }}</strong>
                    asociado a tu EPS.
                </p>

                <span class="vf-empty-hint">
                    Verifica los datos e intenta nuevamente.
                </span>
            </div>

        </section>

    @endif

@else

    {{-- ESTADO INICIAL --}}
    <section class="vf-panel vf-initial-state">

        <div class="vf-empty">
            <div class="vf-empty-icon">
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.7"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 3h6"></path>
                    <path d="M10 3v5l-5 9a3 3 0 0 0 3 4h8a3 3 0 0 0 3-4l-5-9V3"></path>
                    <path d="M8 15h8"></path>
                </svg>
            </div>

            <h3>Consulta de fórmulas médicas</h3>

            <p>
                Busca un paciente por su número de documento,
                nombre o apellido para consultar sus fórmulas médicas.
            </p>
        </div>

    </section>

@endif


    </div>

</main>

</body>
</html>
