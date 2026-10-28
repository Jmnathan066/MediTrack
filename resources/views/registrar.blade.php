
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta | MediTrack EPS</title>
    @vite('resources/css/styleRegistro.css')
</head>
<body>

<main class="register-page">

    <section class="register-card">

        <header class="register-header">
            <a href="/login" class="brand">
                <span class="brand-icon">
                    <svg viewBox="0 0 48 48" fill="none" aria-hidden="true">
                        <path d="M24 12V36M12 24H36"
                              stroke="white" stroke-width="5"
                              stroke-linecap="round"/>
                    </svg>
                </span>

                <span class="brand-name">
                    MediTrack <small>EPS</small>
                </span>
            </a>

            <span class="welcome-tag">¡Únete a nosotros!</span>

            <h1>Crear una cuenta</h1>
            <p>Completa tus datos para comenzar.</p>
        </header>


        @if(session('error'))
            <div class="alerta-error" role="alert">
                <span class="alert-icon">!</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="alerta-success" role="status">
                <span class="alert-icon">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alerta-error" role="alert">
                <span class="alert-icon">!</span>
                <div>
                    <strong>Revisa los siguientes datos:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif


        <form method="POST" action="/registrar" class="register-form">
            @csrf

            <div class="form-section-title">
                <span class="section-number">1</span>
                <div>
                    <h2>Datos personales</h2>
                    <p>Cuéntanos un poco sobre ti.</p>
                </div>
            </div>

            <div class="form-grid">

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        placeholder="Tu nombre"
                        value="{{ old('nombre') }}"
                        autocomplete="given-name"
                        required
                    >
                    @error('nombre')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input
                        type="text"
                        id="apellido"
                        name="apellido"
                        placeholder="Tu apellido"
                        value="{{ old('apellido') }}"
                        autocomplete="family-name"
                        required
                    >
                    @error('apellido')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="ejemplo@correo.com"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                    >
                    @error('email')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="documento">Documento de identidad</label>
                    <input
                        type="text"
                        id="documento"
                        name="documento"
                        placeholder="Número de documento"
                        value="{{ old('documento') }}"
                        autocomplete="off"
                        required
                    >
                    @error('documento')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input
                        type="tel"
                        id="telefono"
                        name="telefono"
                        placeholder="Tu número de teléfono"
                        value="{{ old('telefono') }}"
                        autocomplete="tel"
                        required
                    >
                    @error('telefono')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input
                        type="text"
                        id="direccion"
                        name="direccion"
                        placeholder="Tu dirección de residencia"
                        value="{{ old('direccion') }}"
                        autocomplete="street-address"
                        required
                    >
                    @error('direccion')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

            </div>


            <div class="form-section-title account-title">
                <span class="section-number">2</span>
                <div>
                    <h2>Tu cuenta</h2>
                    <p>Configura tu acceso a MediTrack.</p>
                </div>
            </div>

            <div class="form-grid">

                <div class="form-group">
                    <label for="tipoUsuario">Tipo de usuario</label>
                    <select
                        name="tipoUsuario"
                        id="tipoUsuario"
                        onchange="controlarEmpresa()"
                        required
                    >
                        <option value="">Selecciona una opción</option>

                        <option value="paciente"
                            {{ old('tipoUsuario') === 'paciente' ? 'selected' : '' }}>
                            Paciente
                        </option>

                        <option value="entidad"
                            {{ old('tipoUsuario') === 'entidad' ? 'selected' : '' }}>
                            Entidad
                        </option>
                    </select>
                    @error('tipoUsuario')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="empresa">Empresa <span class="optional">(opcional)</span></label>
                    <input
                        type="text"
                        id="empresa"
                        name="empresa"
                        placeholder="Nombre de la empresa"
                        value="{{ old('empresa') }}"
                        autocomplete="organization"
                    >
                    @error('empresa')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                    <small class="field-hint" id="empresaHint">
                        Si perteneces a una entidad, puedes indicarla aquí.
                    </small>
                </div>

                <div class="form-group full-width">
                    <label for="password">Contraseña</label>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Crea una contraseña"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            id="togglePassword"
                            aria-label="Mostrar contraseña"
                            aria-pressed="false"
                        >
                            <svg id="eyeIcon" viewBox="0 0 24 24"
                                 fill="none" aria-hidden="true">
                                <path d="M2.5 12S6 5.5 12 5.5S21.5 12 21.5 12S18 18.5 12 18.5S2.5 12 2.5 12Z"
                                      stroke="currentColor" stroke-width="1.7"
                                      stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="3"
                                        stroke="currentColor" stroke-width="1.7"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

            </div>


            <button type="submit" class="register-button">
                <span>Crear mi cuenta</span>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M5 12H19M12 5L19 12L12 19"
                          stroke="currentColor" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <p class="login-link">
                ¿Ya tienes una cuenta?
                <a href="{{ url('/login') }}">Inicia sesión</a>
            </p>

            <div class="security-note">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3L19 6V11C19 15.5 16.2 19 12 21C7.8 19 5 15.5 5 11V6L12 3Z"
                          stroke="currentColor" stroke-width="1.7"
                          stroke-linejoin="round"/>
                    <path d="M9 12L11 14L15 10"
                          stroke="currentColor" stroke-width="1.7"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Tus datos se gestionan desde MediTrack EPS.
            </div>

        </form>

    </section>

</main>


<script>
function controlarEmpresa() {
    const rol = document.getElementById('tipoUsuario').value;
    const empresaInput = document.getElementById('empresa');
    const empresaHint = document.getElementById('empresaHint');

    if (rol === 'paciente') {
        empresaInput.disabled = true;
        empresaInput.value = '';
        empresaInput.placeholder = 'No aplica para pacientes';
        empresaHint.textContent = 'Este campo no aplica para pacientes.';
    } else {
        empresaInput.disabled = false;
        empresaInput.placeholder = 'Nombre de la empresa';
        empresaHint.textContent =
            'Si perteneces a una entidad, puedes indicarla aquí.';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    controlarEmpresa();

    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    const eyeOpen = `
        <path d="M2.5 12S6 5.5 12 5.5S21.5 12 21.5 12S18 18.5 12 18.5S2.5 12 2.5 12Z"
              stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
        <circle cx="12" cy="12" r="3"
                stroke="currentColor" stroke-width="1.7"/>
    `;

    const eyeClosed = `
        <path d="M3 3L21 21" stroke="currentColor" stroke-width="1.7"
              stroke-linecap="round"/>
        <path d="M10.6 5.8C11.1 5.6 11.5 5.5 12 5.5C18 5.5 21.5 12 21.5 12
                 S20.3 14.2 18.3 16M6.2 6.7C3.8 8.4 2.5 12 2.5 12
                 S6 18.5 12 18.5C13.5 18.5 14.8 18.1 16 17.4"
              stroke="currentColor" stroke-width="1.7"
              stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M9.9 9.9A3 3 0 0 0 14.1 14.1"
              stroke="currentColor" stroke-width="1.7"
              stroke-linecap="round"/>
    `;

    togglePassword.addEventListener('click', function () {
        const isPassword = passwordInput.type === 'password';

        passwordInput.type = isPassword ? 'text' : 'password';
        eyeIcon.innerHTML = isPassword ? eyeClosed : eyeOpen;

        togglePassword.setAttribute('aria-pressed', String(isPassword));
        togglePassword.setAttribute(
            'aria-label',
            isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'
        );
    });
});
</script>

</body>
</html>
