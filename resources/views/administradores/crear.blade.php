
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear usuario | MediTrack EPS</title>

    @vite([
        'resources/css/partials.css',
        'resources/css/administradores.css',
        'resources/js/partials.js',
        'resources/js/administradores.js'
    ])
</head>

<body>
    @include('partials.topbar')
    @include('partials.sidebar')

    @php
        $rolSeleccionado = old(
            'tipoUsuario',
            session('rolCreado', 'admin')
        );

        if (!in_array($rolSeleccionado, ['admin', 'doctor'], true)) {
            $rolSeleccionado = 'admin';
        }

        $esDoctor = $rolSeleccionado === 'doctor';
    @endphp

    <main class="admin-page">
        <div class="admin-container">

            {{-- Encabezado --}}
            <header class="admin-heading">
                <h1 id="adminPageTitle">
                    {{ $esDoctor ? 'Crear doctor' : 'Crear administrador' }}
                </h1>

                <p id="adminPageDescription">
                    {{ $esDoctor
                        ? 'Registra nuevos profesionales médicos en MediTrack EPS.'
                        : 'Gestiona el acceso de nuevos administradores a MediTrack EPS.' }}
                </p>
            </header>

            {{-- Selector del tipo de usuario --}}
            <div class="admin-role-selector" aria-label="Tipo de usuario">
                <button
                    type="button"
                    class="admin-role-option {{ !$esDoctor ? 'active' : '' }}"
                    data-role="admin"
                    aria-pressed="{{ !$esDoctor ? 'true' : 'false' }}"
                >
                    Crear administrador
                </button>

                <button
                    type="button"
                    class="admin-role-option {{ $esDoctor ? 'active' : '' }}"
                    data-role="doctor"
                    aria-pressed="{{ $esDoctor ? 'true' : 'false' }}"
                >
                    Crear doctor
                </button>
            </div>

            {{-- Mensaje de éxito --}}
            @if (session('success'))
                <div class="admin-alert admin-alert-success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Mensaje de error --}}
            @if (session('error'))
                <div class="admin-alert admin-alert-error" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Errores de validación --}}
            @if ($errors->any())
                <div class="admin-alert admin-alert-error" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulario --}}
            <section class="admin-card">

                <h2 id="adminCardTitle">
                    {{ $esDoctor ? 'Información del doctor' : 'Información del administrador' }}
                </h2>

                <p class="description" id="adminCardDescription">
                    {{ $esDoctor
                        ? 'Completa los datos para registrar un profesional médico.'
                        : 'Completa los datos para registrar una nueva cuenta administrativa.' }}
                </p>

                <form
                    method="POST"
                    action="{{ route('usuarios.guardar') }}"
                >
                    @csrf

                    {{-- Tipo de usuario --}}
                    <input
                        type="hidden"
                        name="tipoUsuario"
                        id="tipoUsuario"
                        value="{{ $rolSeleccionado }}"
                    >

                    <div class="admin-form-grid admin-create-grid">

                        {{-- Nombre --}}
                        <div class="admin-field">
                            <label for="nombre">Nombre</label>
                            <input
                                id="nombre"
                                type="text"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                maxlength="255"
                                placeholder="Nombre"
                                autocomplete="given-name"
                                required
                            >
                        </div>

                        {{-- Apellido --}}
                        <div class="admin-field">
                            <label for="apellido">Apellido</label>
                            <input
                                id="apellido"
                                type="text"
                                name="apellido"
                                value="{{ old('apellido') }}"
                                maxlength="255"
                                placeholder="Apellido"
                                autocomplete="family-name"
                                required
                            >
                        </div>

                        {{-- Correo --}}
                        <div class="admin-field">
                            <label for="email">Correo electrónico</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                maxlength="255"
                                placeholder="correo@ejemplo.com"
                                autocomplete="email"
                                required
                            >
                        </div>

                        {{-- Documento --}}
                        <div class="admin-field">
                            <label for="documento">Documento</label>
                            <input
                                id="documento"
                                type="text"
                                name="documento"
                                value="{{ old('documento') }}"
                                placeholder="Número de documento"
                                autocomplete="off"
                                required
                            >
                        </div>

                        {{-- Teléfono --}}
                        <div class="admin-field">
                            <label for="telefono">Teléfono</label>
                            <input
                                id="telefono"
                                type="tel"
                                name="telefono"
                                value="{{ old('telefono') }}"
                                pattern="[0-9]{7,15}"
                                placeholder="Entre 7 y 15 dígitos"
                                autocomplete="tel"
                                required
                            >
                        </div>

                        {{-- Empresa --}}
                        <div class="admin-field">
                            <label for="empresa">Empresa (opcional)</label>
                            <input
                                id="empresa"
                                type="text"
                                name="empresa"
                                value="{{ old('empresa') }}"
                                maxlength="255"
                                placeholder="Nombre de la empresa"
                                autocomplete="organization"
                            >
                        </div>

                        {{-- EPS: solamente para doctores --}}
                        <div
                            class="admin-field eps-field"
                            id="epsField"
                            {{ !$esDoctor ? 'hidden' : '' }}
                        >
                            <label for="idEPS">EPS del doctor</label>

                            <select
                                id="idEPS"
                                name="idEPS"
                                {{ $esDoctor ? 'required' : 'disabled' }}
                            >
                                <option value="">Selecciona una EPS</option>

                                @foreach ($eps as $item)
                                    <option
                                        value="{{ $item->idEPS }}"
                                        {{ (string) old('idEPS') === (string) $item->idEPS ? 'selected' : '' }}
                                    >
                                        {{ $item->nombre }}
                                    </option>
                                @endforeach
                            </select>

                            @if ($eps->isEmpty())
                                <small>
                                    No hay EPS activas disponibles. Primero debes registrar o activar una EPS.
                                </small>
                            @else
                                <small>
                                    Selecciona la EPS a la que estará asociado este doctor.
                                </small>
                            @endif
                        </div>

                        {{-- Dirección --}}
                        <div class="admin-field direccion-field">
                            <label for="direccion">Dirección</label>
                            <input
                                id="direccion"
                                type="text"
                                name="direccion"
                                value="{{ old('direccion') }}"
                                maxlength="255"
                                placeholder="Dirección de residencia"
                                autocomplete="street-address"
                                required
                            >
                        </div>

                        {{-- Contraseña --}}
                        <div class="admin-field password-field">
                            <label for="password">Contraseña</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Ingrese una contraseña"
                                autocomplete="new-password"
                                required
                            >
                            <small>
                                Mínimo 6 caracteres, con al menos una letra y un número.
                            </small>
                        </div>

                        {{-- Confirmar contraseña --}}
                        <div class="admin-field password-confirmation-field">
                            <label for="password_confirmation">
                                Repetir contraseña
                            </label>

                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                placeholder="Repita la contraseña"
                                autocomplete="new-password"
                                required
                            >
                        </div>

                    </div>

                    {{-- Acciones --}}
                    <div class="admin-actions">
                        <a
                            href="{{ url('/home') }}"
                            class="admin-button admin-button-secondary"
                        >
                            Volver al inicio
                        </a>

                        <button
                            type="submit"
                            class="admin-button admin-button-primary"
                            id="adminSubmitButton"
                        >
                            {{ $esDoctor ? 'Crear doctor' : 'Crear administrador' }}
                        </button>
                    </div>

                </form>
            </section>

        </div>
    </main>
</body>
</html>
