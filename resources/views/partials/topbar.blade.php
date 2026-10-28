@php
    $sedesDisponibles = \App\Models\Sede::where('estado', true)
        ->orderBy('nombre')
        ->get();

    $sedeSeleccionada = session(
        'idSede',
        $sedesDisponibles->first()?->idSede
    );
@endphp

<div class="topbar">
    <div class="logo">MediTrack EPS</div>

    <div class="profile-container">

        <form
            action="{{ route('sedes.cambiar') }}"
            method="POST"
            class="sede-selector"
        >
            @csrf

            <label for="idSede">Sede:</label>

            <select
                name="idSede"
                id="idSede"
                onchange="this.form.submit()"
                aria-label="Seleccionar sede"
            >
                @forelse($sedesDisponibles as $sede)
                    <option
                        value="{{ $sede->idSede }}"
                        @selected($sedeSeleccionada == $sede->idSede)
                    >
                        {{ $sede->nombre }}
                    </option>
                @empty
                    <option value="">No hay sedes disponibles</option>
                @endforelse
            </select>
        </form>

        <button
            type="button"
            class="profile"
            onclick="toggleMenu()"
            aria-label="Abrir menú de usuario"
            aria-expanded="false"
            id="profileButton"
        >
            {{ session('nombre')[0] ?? 'U' }}
        </button>

        <div id="menu" class="dropdown hidden">
            <div class="dropdown-header">
                <div class="avatar-large">
                    {{ session('nombre')[0] ?? 'U' }}
                </div>

                <p>
                    {{ session('nombre') }} {{ session('apellido') }}
                </p>

                <p>Rol: {{ session('tipoUsuario') }}</p>
            </div>

            <a href="/perfil" class="dropdown-link profile-menu-btn">
                <span>👤</span>
                <span>Mi perfil</span>
            </a>

            <div class="theme-option">
                <span id="themeLabel">Modo oscuro</span>

                <button
                    type="button"
                    id="themeToggle"
                    class="theme-toggle"
                    role="switch"
                    aria-checked="false"
                    aria-label="Activar o desactivar modo oscuro"
                >
                    <span class="theme-toggle-knob"></span>
                </button>
            </div>

            <a href="/logout" class="logout-btn">Cerrar sesión</a>
        </div>
    </div>
</div>
