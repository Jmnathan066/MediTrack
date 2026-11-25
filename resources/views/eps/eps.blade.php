
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de EPS | MediTrack EPS</title>

    @vite([
        'resources/css/partials.css',
        'resources/css/eps.css',
        'resources/js/partials.js'
    ])
</head>
<body>

    @include('partials.sidebar')
    @include('partials.topbar')

    <main class="eps-layout">
        <div class="eps-header">
            <div>
                <h1>Gestión de EPS</h1>
                <p>Administra las entidades promotoras de salud registradas en MediTrack.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="eps-alert eps-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="eps-alert eps-alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="eps-alert eps-alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="eps-stats">
            <div class="eps-stat-card">
                <div class="eps-stat-info">
                    <span>Total de EPS</span>
                    <h2>{{ $eps->count() }}</h2>
                </div>
            </div>

            <div class="eps-stat-card">
                <div class="eps-stat-info">
                    <span>EPS activas</span>
                    <h2>{{ $eps->where('estado', true)->count() }}</h2>
                </div>
            </div>

            <div class="eps-stat-card">
                <div class="eps-stat-info">
                    <span>EPS inactivas</span>
                    <h2>{{ $eps->where('estado', false)->count() }}</h2>
                </div>
            </div>
        </section>

        <section class="eps-content">

            <div class="eps-form-container">
                <div class="eps-section-header">
                    <h2>{{ $epsEditar ? 'Editar EPS' : 'Registrar EPS' }}</h2>
                    <p>
                        {{ $epsEditar
                            ? 'Actualiza la información de la entidad.'
                            : 'Completa los datos para registrar una nueva entidad.' }}
                    </p>
                </div>

                <form
                    action="{{ $epsEditar ? route('eps.update', $epsEditar->idEPS) : route('eps.store') }}"
                    method="POST"
                    class="eps-form"
                >
                    @csrf

                    @if($epsEditar)
                        @method('PUT')
                    @endif

                    <div class="eps-form-group">
                        <label for="nombre">Nombre de la EPS</label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="{{ old('nombre', $epsEditar->nombre ?? '') }}"
                            placeholder="Ej. Nombre de la entidad"
                            maxlength="150"
                            required
                        >
                    </div>

                    <div class="eps-form-group">
                        <label for="nit">NIT</label>
                        <input
                            type="text"
                            id="nit"
                            name="nit"
                            value="{{ old('nit', $epsEditar->nit ?? '') }}"
                            placeholder="Ingrese el NIT"
                            maxlength="30"
                            required
                        >
                    </div>

                    <div class="eps-form-actions">
                        <button type="submit" class="eps-btn eps-btn-primary">
                            {{ $epsEditar ? 'Guardar cambios' : 'Registrar EPS' }}
                        </button>

                        @if($epsEditar)
                            <a href="{{ route('eps.index') }}" class="eps-btn eps-btn-secondary">
                                Cancelar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="eps-table-container">
                <div class="eps-section-header">
                    <h2>EPS registradas</h2>
                    <p>Consulta y administra las entidades existentes.</p>
                </div>

                <div class="eps-table-wrapper">
                    <table class="eps-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>NIT</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($eps as $entidad)
                                <tr>
                                    <td>{{ $entidad->idEPS }}</td>
                                    <td>{{ $entidad->nombre }}</td>
                                    <td>{{ $entidad->nit }}</td>
                                    <td>
                                        @if($entidad->estado)
                                            <span class="status-active">Activa</span>
                                        @else
                                            <span class="status-inactive">Inactiva</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="eps-actions">
                                            <a
                                                href="{{ route('eps.edit', $entidad->idEPS) }}"
                                                class="eps-btn eps-btn-edit"
                                            >
                                                Editar
                                            </a>

                                            <form
                                                action="{{ route('eps.estado', $entidad->idEPS) }}"
                                                method="POST"
                                                onsubmit="return confirm('¿Estás seguro de cambiar el estado de esta EPS?');"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="eps-btn {{ $entidad->estado ? 'eps-btn-danger' : 'eps-btn-success' }}"
                                                >
                                                    {{ $entidad->estado ? 'Inactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="eps-empty">
                                        No hay EPS registradas todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
    </main>

</body>
</html>
