
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de usuarios | MediTrack EPS</title>

    @vite([
        'resources/css/partials.css',
        'resources/css/usuarios.css',
        'resources/js/partials.js'
    ])
</head>
<body>

    @include('partials.sidebar')
    @include('partials.topbar')

    <main class="usuarios-page">

        <header class="usuarios-header">
            <div>
                <p class="usuarios-eyebrow">ADMINISTRACIÓN</p>
                <h1>Gestión de usuarios</h1>
                <p class="usuarios-subtitle">
                    Consulta las cuentas registradas y administra su acceso al sistema.
                </p>
            </div>
        </header>

        @if(session('success'))
            <div class="usuarios-alerta usuarios-alerta-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="usuarios-alerta usuarios-alerta-error" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <section class="usuarios-resumen">
            <article class="usuarios-stat">
                <span>Total de usuarios</span>
                <strong>{{ $totalUsuarios }}</strong>
            </article>

            <article class="usuarios-stat">
                <span>Cuentas activas</span>
                <strong>{{ $usuariosActivos }}</strong>
            </article>

            <article class="usuarios-stat">
                <span>Cuentas desactivadas</span>
                <strong>{{ $usuariosInactivos }}</strong>
            </article>
        </section>

        <section class="usuarios-panel">
            <div class="usuarios-panel-heading">
                <div>
                    <h2>Usuarios registrados</h2>
                    <p>Busca por datos del usuario o filtra los resultados por rol.</p>
                </div>
            </div>

            <form
                method="GET"
                action="{{ route('usuarios.index') }}"
                class="usuarios-filtros"
            >
                <div class="usuarios-campo usuarios-busqueda">
                    <label for="buscar">Buscar usuario</label>
                    <input
                        type="search"
                        id="buscar"
                        name="buscar"
                        value="{{ $busqueda }}"
                        placeholder="Código, nombre, apellido o correo"
                    >
                </div>

                <div class="usuarios-campo usuarios-rol">
                    <label for="rol">Filtrar por rol</label>
                    <select name="rol" id="rol">
                        <option value="" {{ $rol === '' ? 'selected' : '' }}>
                            Todos los roles
                        </option>
                        <option value="admin" {{ $rol === 'admin' ? 'selected' : '' }}>
                            Administrador
                        </option>
                        <option value="paciente" {{ $rol === 'paciente' ? 'selected' : '' }}>
                            Paciente
                        </option>
                        <option value="entidad" {{ $rol === 'entidad' ? 'selected' : '' }}>
                            Entidad
                        </option>
                        <option value="doctor" {{ $rol === 'doctor' ? 'selected' : '' }}>
                            Doctor
                        </option>
                    </select>
                </div>

                <div class="usuarios-acciones-filtro">
                    <button type="submit" class="usuarios-btn usuarios-btn-primary">
                        Buscar
                    </button>

                    <a
                        href="{{ route('usuarios.index') }}"
                        class="usuarios-btn usuarios-btn-secondary"
                    >
                        Limpiar
                    </a>
                </div>
            </form>

            <div class="usuarios-tabla-contenedor">
                <table class="usuarios-tabla">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Código</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($usuarios as $usuario)
                            <tr>
                                <td>
                                    <div class="usuarios-identidad">
                                        <strong>
                                            {{ $usuario->nombre }} {{ $usuario->apellido }}
                                        </strong>
                                        <span>{{ $usuario->email }}</span>
                                    </div>
                                </td>

                                <td>{{ $usuario->codigo ?? 'Sin código' }}</td>

                                <td>
                                    <span class="usuarios-rol-badge">
                                        @switch($usuario->tipoUsuario)
                                            @case('admin')
                                                Administrador
                                                @break
                                            @case('paciente')
                                                Paciente
                                                @break
                                            @case('entidad')
                                                Entidad
                                                @break
                                            @case('doctor')
                                                Doctor
                                                @break
                                            @default
                                                {{ $usuario->tipoUsuario }}
                                        @endswitch
                                    </span>
                                </td>

                                <td>
                                    <span class="usuarios-estado {{ $usuario->estado ? 'estado-activo' : 'estado-inactivo' }}">
                                        {{ $usuario->estado ? 'Activo' : 'Desactivado' }}
                                    </span>
                                </td>

                                <td>
                                    @if((int) $usuario->id === (int) session('user_id'))
                                        <span class="usuarios-propia-cuenta">
                                            Tu cuenta
                                        </span>
                                    @else
                                        <form
                                            method="POST"
                                            action="{{ route('usuarios.estado', $usuario) }}"
                                            onsubmit="return confirm('¿Seguro que deseas {{ $usuario->estado ? 'desactivar' : 'activar' }} esta cuenta?');"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="usuarios-btn-estado {{ $usuario->estado ? 'btn-desactivar' : 'btn-activar' }}"
                                            >
                                                {{ $usuario->estado ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="usuarios-vacio">
                                    No se encontraron usuarios con esos filtros.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="usuarios-paginacion">
                <span>
                    Mostrando {{ $usuarios->firstItem() ?? 0 }}
                    a {{ $usuarios->lastItem() ?? 0 }}
                    de {{ $usuarios->total() }} usuarios
                </span>

                {{ $usuarios->links() }}
            </div>
        </section>
    </main>

</body>
</html>
