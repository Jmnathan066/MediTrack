
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Consulta de pacientes | MediTrack EPS</title>

    @vite([
        'resources/css/partials.css',
        'resources/css/pacientes.css',
        'resources/js/partials.js'
    ])
</head>
<body>

    @include('partials.sidebar')
    @include('partials.topbar')

    <main class="pacientes-page">

        {{-- ENCABEZADO --}}
        <header class="pacientes-header">
            <div>
                <h1>Consulta de pacientes</h1>
                <p>Consulta los pacientes asociados a tu EPS y registra sus observaciones médicas.</p>
            </div>
        </header>

        {{-- MENSAJES --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>No se pudo realizar la operación:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- RESUMEN --}}
        <section class="pacientes-resumen">
            <div class="resumen-card">
                <div class="resumen-icono">👥</div>
                <div>
                    <span>Pacientes de tu EPS</span>
                    <strong>{{ $pacientes->total() }}</strong>
                    <small>Pacientes asociados a tu entidad</small>
                </div>
            </div>
        </section>

        {{-- BÚSQUEDA --}}
        <section class="pacientes-panel">

            <div class="panel-heading">
                <h2>Buscar pacientes</h2>
                <p>Busca por nombre, apellido o número de documento.</p>
            </div>

            <form
                action="{{ route('doctor.pacientes') }}"
                method="GET"
                class="pacientes-search"
            >

                <div class="campo-busqueda">
                    <label for="busqueda">Nombre o documento del paciente</label>

                    <input
                        type="text"
                        id="busqueda"
                        name="busqueda"
                        value="{{ $busqueda }}"
                        placeholder="Ej. María García o número de documento"
                    >
                </div>

                <div class="campo-estado">
                    <label for="estado">Estado de la cuenta</label>

                    <select id="estado" name="estado">
                        <option value="todos" {{ $estado === 'todos' ? 'selected' : '' }}>
                            Todos los pacientes
                        </option>

                        <option value="activos" {{ $estado === 'activos' ? 'selected' : '' }}>
                            Solo cuentas activas
                        </option>

                        <option value="inactivos" {{ $estado === 'inactivos' ? 'selected' : '' }}>
                            Solo cuentas desactivadas
                        </option>
                    </select>
                </div>

                <div class="acciones-busqueda">
                    <button type="submit" class="btn-buscar">
                        Buscar
                    </button>

                    <a href="{{ route('doctor.pacientes') }}" class="btn-limpiar">
                        Ver todos
                    </a>
                </div>

            </form>
        </section>

        {{-- LISTADO DE PACIENTES --}}
        <section class="pacientes-panel resultados-panel">

            <div class="resultados-header">
                <div>
                    <h2>
                        {{ $busqueda !== '' ? 'Resultados de búsqueda' : 'Pacientes registrados' }}
                    </h2>

                    <p>
                        @if ($busqueda !== '')
                            Resultados para <strong>{{ $busqueda }}</strong>
                        @else
                            Pacientes pertenecientes a tu EPS.
                        @endif
                    </p>
                </div>

                <span class="contador-resultados">
                    {{ $pacientes->total() }}
                    {{ $pacientes->total() === 1 ? 'paciente' : 'pacientes' }}
                </span>
            </div>

            @if ($pacientes->isNotEmpty())

                <div class="tabla-contenedor">

                    <table class="tabla-pacientes">

                        <thead>
                            <tr>
                                <th>Nombre completo</th>
                                <th>Documento</th>
                                <th>Correo electrónico</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($pacientes as $paciente)

                                @php
                                    $historialPaciente = $observacionesPorPaciente->get(
                                        $paciente->id,
                                        collect()
                                    );
                                @endphp

                                <tr>
                                    <td>
                                        <div class="nombre-paciente">
                                            <span class="avatar-paciente">
                                                {{ strtoupper(substr($paciente->nombre ?? 'P', 0, 1)) }}
                                            </span>

                                            <span>
                                                {{ $paciente->nombre }}
                                                {{ $paciente->apellido }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        {{ $paciente->documento ?: 'No registrado' }}
                                    </td>

                                    <td>
                                        {{ $paciente->email ?: 'No registrado' }}
                                    </td>

                                    <td>
                                        {{ $paciente->telefono ?: 'No registrado' }}
                                    </td>

                                    <td>
                                        @if ($paciente->estado)
                                            <span class="estado-paciente estado-activo">
                                                Activo
                                            </span>
                                        @else
                                            <span class="estado-paciente estado-inactivo">
                                                Desactivado
                                            </span>
                                        @endif
                                    </td>

                                    <td class="acciones-celda">

                                        {{-- REGISTRAR OBSERVACIÓN --}}
                                        <details class="acciones-paciente">
                                            <summary class="btn-buscar">
                                                Registrar observación
                                            </summary>

                                            <div class="formulario-observacion">

                                                <form
                                                    action="{{ route('doctor.observaciones.guardar') }}"
                                                    method="POST"
                                                >
                                                    @csrf

                                                    <input
                                                        type="hidden"
                                                        name="idPaciente"
                                                        value="{{ $paciente->id }}"
                                                    >

                                                    <label for="observacion-{{ $paciente->id }}">
                                                        Observación médica
                                                    </label>

                                                    <textarea
                                                        id="observacion-{{ $paciente->id }}"
                                                        name="observacion"
                                                        rows="5"
                                                        maxlength="5000"
                                                        required
                                                        placeholder="Escribe la observación médica del paciente..."
                                                    >{{ old('idPaciente') == $paciente->id ? old('observacion') : '' }}</textarea>

                                                    <button
                                                        type="submit"
                                                        class="btn-buscar"
                                                    >
                                                        Guardar observación
                                                    </button>
                                                </form>

                                            </div>
                                        </details>

                                        {{-- HISTORIAL DE OBSERVACIONES --}}
                                        <details class="acciones-paciente historial-paciente">

                                            <summary class="btn-limpiar">
                                                Historial de observaciones
                                                ({{ $historialPaciente->count() }})
                                            </summary>

                                            <div class="historial-observaciones">

                                                @forelse ($historialPaciente as $observacion)

                                                    <article class="observacion-registro">

                                                        <div class="observacion-registro-header">
                                                            <strong>
                                                                Observación médica
                                                            </strong>
                                                        </div>

                                                        <p>
                                                            {{ $observacion->observacion }}
                                                        </p>

                                                        <small>
                                                            <strong>Doctor:</strong>
                                                            {{ $observacion->doctor_nombre ?? 'No disponible' }}
                                                            {{ $observacion->doctor_apellido ?? '' }}
                                                        </small>

                                                        <small>
                                                            <strong>Fecha:</strong>

                                                            {{ $observacion->created_at
                                                                ? \Carbon\Carbon::parse($observacion->created_at)->format('d/m/Y H:i')
                                                                : 'Sin fecha' }}
                                                        </small>

                                                    </article>

                                                @empty

                                                    <div class="sin-observaciones">
                                                        <span>📋</span>
                                                        <p>
                                                            Este paciente todavía no tiene observaciones médicas registradas.
                                                        </p>
                                                    </div>

                                                @endforelse

                                            </div>

                                        </details>

                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- PAGINACIÓN --}}
                <div class="paginacion-pacientes">
                    {{ $pacientes->appends(request()->query())->links() }}
                </div>

            @else

                <div class="sin-resultados">
                    <div class="sin-resultados-icono">🔎</div>

                    <h3>No se encontraron pacientes</h3>

                    <p>
                        @if ($busqueda !== '')
                            No hay pacientes que coincidan con
                            <strong>{{ $busqueda }}</strong>.
                            Intenta con otro nombre o documento.
                        @else
                            No hay pacientes asociados a tu EPS que coincidan con el filtro seleccionado.
                        @endif
                    </p>

                    <a href="{{ route('doctor.pacientes') }}" class="btn-limpiar">
                        Ver todos los pacientes
                    </a>
                </div>

            @endif

        </section>

    </main>

</body>
</html>
