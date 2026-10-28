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
        <header class="pacientes-header">
            <div>
                <h1>Consulta de pacientes</h1>
                <p>Consulta y encuentra pacientes registrados en MediTrack EPS.</p>
            </div>
        </header>

        {{-- Resumen --}}
        <section class="pacientes-resumen">
            <div class="resumen-card">
                <div class="resumen-icono">👥</div>
                <div>
                    <span>Total de pacientes</span>
                    <strong>{{ $pacientes->total() }}</strong>
                    <small>Usuarios con rol paciente</small>
                </div>
            </div>
        </section>

        {{-- Búsqueda y filtros --}}
        <section class="pacientes-panel">
            <div class="panel-heading">
                <h2>Buscar pacientes</h2>
                <p>Busca por nombre, apellido o número de documento.</p>
            </div>

            <form action="{{ route('pacientes.index') }}" method="GET"
                  class="pacientes-search">

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
                    <button type="submit" class="btn-buscar">Buscar</button>
                    <a href="{{ route('pacientes.index') }}" class="btn-limpiar">
                        Ver todos
                    </a>
                </div>
            </form>
        </section>

        {{-- Listado general o resultados --}}
        <section class="pacientes-panel resultados-panel">
            <div class="resultados-header">
                <div>
                    <h2>{{ $busqueda !== '' ? 'Resultados de búsqueda' : 'Pacientes registrados' }}</h2>
                    <p>
                        @if ($busqueda !== '')
                            Resultados para <strong>{{ $busqueda }}</strong>
                        @else
                            Listado de pacientes registrados en el sistema.
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pacientes as $paciente)
                                <tr>
                                    <td>
                                        <div class="nombre-paciente">
                                            <span class="avatar-paciente">
                                                {{ strtoupper(substr($paciente->nombre ?? 'P', 0, 1)) }}
                                            </span>
                                            <span>
                                                {{ $paciente->nombre }} {{ $paciente->apellido }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>{{ $paciente->documento ?: 'No registrado' }}</td>
                                    <td>{{ $paciente->email ?: 'No registrado' }}</td>
                                    <td>{{ $paciente->telefono ?: 'No registrado' }}</td>
                                    <td>
                                        @if ($paciente->estado)
                                            <span class="estado-paciente estado-activo">Activo</span>
                                        @else
                                            <span class="estado-paciente estado-inactivo">Desactivado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

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
                            Todavía no hay pacientes que coincidan con el filtro seleccionado.
                        @endif
                    </p>
                    <a href="{{ route('pacientes.index') }}" class="btn-limpiar">
                        Ver todos los pacientes
                    </a>
                </div>
            @endif
        </section>
    </main>
</body>
</html>
