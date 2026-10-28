<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de sedes | MediTrack EPS</title>

    @vite([
        'resources/css/partials.css',
        'resources/css/sedes.css',
        'resources/js/partials.js'
    ])
</head>
<body>

    @include('partials.sidebar')

    <div class="sedes-layout">
        @include('partials.topbar')

        <main class="sedes-main">

            {{-- Encabezado --}}
            <header class="sedes-header">
                <div>
                    <span class="sedes-eyebrow">ADMINISTRACIÓN</span>
                    <h1>Gestión de sedes</h1>
                    <p>
                        Registra, consulta y administra las sedes
                        de MediTrack EPS desde un solo lugar.
                    </p>
                </div>

                <div class="sedes-header-icon">🏥</div>
            </header>

            {{-- Mensaje de éxito --}}
            @if (session('success'))
                <div class="sedes-alert sedes-alert-success" role="status">
                    <span>✓</span>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Mensaje de error --}}
            @if (session('error'))
                <div class="sedes-alert sedes-alert-error" role="alert">
                    <span>!</span>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Errores de validación --}}
            @if ($errors->any())
                <div class="sedes-alert sedes-alert-error" role="alert">
                    <div>
                        <strong>No se pudo completar la operación.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Estadísticas --}}
            <section class="sedes-stats">

                <div class="sedes-stat-card">
                    <div class="sedes-stat-icon">🏢</div>
                    <div>
                        <span class="sedes-stat-label">Total de sedes</span>
                        <strong>{{ $sedes->count() }}</strong>
                    </div>
                </div>

                <div class="sedes-stat-card">
                    <div class="sedes-stat-icon active-icon">✓</div>
                    <div>
                        <span class="sedes-stat-label">Sedes activas</span>
                        <strong>{{ $sedes->where('estado', true)->count() }}</strong>
                    </div>
                </div>

                <div class="sedes-stat-card">
                    <div class="sedes-stat-icon inactive-icon">○</div>
                    <div>
                        <span class="sedes-stat-label">Sedes inactivas</span>
                        <strong>{{ $sedes->where('estado', false)->count() }}</strong>
                    </div>
                </div>

            </section>

            {{-- Formulario y listado --}}
            <section class="sedes-content">

                {{-- Panel de registro y edición --}}
                <div class="sedes-form-panel">

                    <div class="sedes-section-heading">
                        <div>
                            <h2>
                                {{ $sedeEditar
                                    ? 'Editar sede'
                                    : 'Registrar nueva sede' }}
                            </h2>

                            <p>
                                {{ $sedeEditar
                                    ? 'Modifica el nombre o la dirección de esta sede.'
                                    : 'Completa los datos de la sede que deseas agregar.' }}
                            </p>
                        </div>

                        <span class="sedes-heading-icon">
                            {{ $sedeEditar ? '✎' : '＋' }}
                        </span>
                    </div>

                    <form
                        action="{{ $sedeEditar
                            ? route('sedes.update', $sedeEditar->idSede)
                            : route('sedes.store') }}"
                        method="POST"
                    >
                        @csrf

                        @if ($sedeEditar)
                            @method('PUT')
                        @endif

                        <div class="sedes-field">
                            <label for="nombre">Nombre de la sede</label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="{{ old('nombre', $sedeEditar?->nombre) }}"
                                placeholder="Ej. Sede Yumbo"
                                maxlength="150"
                                required
                            >

                            @error('nombre')
                                <span class="sedes-field-error">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="sedes-field">
                            <label for="direccion">Dirección</label>

                            <input
                                type="text"
                                id="direccion"
                                name="direccion"
                                value="{{ old('direccion', $sedeEditar?->direccion) }}"
                                placeholder="Ej. Calle 10 # 5-20"
                                maxlength="255"
                                required
                            >

                            @error('direccion')
                                <span class="sedes-field-error">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="sedes-submit">
                            {{ $sedeEditar
                                ? 'Guardar cambios'
                                : '＋ Registrar sede' }}
                        </button>

                        @if ($sedeEditar)
                            <a
                                href="{{ route('sedes.index') }}"
                                class="sedes-cancel-link"
                            >
                                Cancelar edición
                            </a>
                        @endif

                    </form>

                    <div class="sedes-form-note">
                        <span>ℹ</span>
                        <p>
                            Cada sede tiene su propio inventario de medicamentos.
                        </p>
                    </div>

                </div>

                {{-- Panel de sedes registradas --}}
                <div class="sedes-list-panel">

                    <div class="sedes-section-heading">
                        <div>
                            <h2>Sedes registradas</h2>
                            <p>
                                Consulta y administra las ubicaciones
                                disponibles en el sistema.
                            </p>
                        </div>

                        <span class="sedes-count">
                            {{ $sedes->count() }}
                        </span>
                    </div>

                    @if ($sedes->isNotEmpty())

                        <div class="sedes-table-wrapper">
                            <table class="sedes-table">

                                <thead>
                                    <tr>
                                        <th>Sede</th>
                                        <th>Dirección</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($sedes as $sede)
                                        <tr>

                                            {{-- Nombre e identificador --}}
                                            <td>
                                                <div class="sedes-name-cell">
                                                    <span class="sedes-building">🏥</span>

                                                    <div>
                                                        <strong>
                                                            {{ $sede->nombre }}
                                                        </strong>

                                                        <small>
                                                            Código #{{ $sede->idSede }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Dirección --}}
                                            <td>
                                                {{ $sede->direccion }}
                                            </td>

                                            {{-- Estado --}}
                                            <td>
                                                @if ($sede->estado)
                                                    <span class="sedes-status status-active">
                                                        Activa
                                                    </span>
                                                @else
                                                    <span class="sedes-status status-inactive">
                                                        Inactiva
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Acciones --}}
                                            <td>
                                                <div class="sedes-actions">

                                                    {{-- Editar --}}
                                                    <a
                                                        href="{{ route('sedes.edit', $sede->idSede) }}"
                                                        class="sedes-action-btn"
                                                    >
                                                        Editar
                                                    </a>

                                                    {{-- Activar o inactivar --}}
                                                    <form
                                                        action="{{ route('sedes.estado', $sede->idSede) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('¿Deseas {{ $sede->estado ? 'inactivar' : 'activar' }} esta sede?');"
                                                    >
                                                        @csrf
                                                        @method('PATCH')

                                                        <button
                                                            type="submit"
                                                            class="sedes-action-btn"
                                                        >
                                                            {{ $sede->estado
                                                                ? 'Inactivar'
                                                                : 'Activar' }}
                                                        </button>
                                                    </form>

                                                    {{-- Eliminar --}}
                                                    <form
                                                        action="{{ route('sedes.destroy', $sede->idSede) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('¿Estás seguro de eliminar esta sede? Esta acción no se puede deshacer.');"
                                                    >
                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="sedes-action-btn sedes-action-delete"
                                                        >
                                                            Eliminar
                                                        </button>
                                                    </form>

                                                </div>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    @else

                        <div class="sedes-empty">
                            <div class="sedes-empty-icon">🏢</div>

                            <h3>Aún no hay sedes registradas</h3>

                            <p>
                                Registra la primera sede usando el formulario
                                para comenzar a organizar los inventarios.
                            </p>
                        </div>

                    @endif

                </div>

            </section>

            {{-- Pie de página --}}
            <footer class="sedes-footer">
                MediTrack EPS · Administración de sedes
            </footer>

        </main>
    </div>

</body>
</html>
