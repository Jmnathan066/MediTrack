
<!DOCTYPE html>
<html>
<head>
    <title>Cambiar contraseña</title>
    @vite([
        'resources/css/partials.css',
        'resources/css/perfil/password.css',
        'resources/js/partials.js',
        'resources/js/password.js'
    ])
</head>
<body>

@include('partials.sidebar')
@include('partials.topbar')

<div class="content">
    <h1>Cambiar contraseña</h1>

    @if(session('success'))
        <div class="alerta-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alerta-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/perfil/password" method="POST" class="form-password">
        @csrf

        <label>Contraseña actual</label>
        <div class="password-wrapper">
            <input type="password" name="password_actual" required>
            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m3 3 18 18"/>
                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                    <path d="M9.9 5.2A11 11 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8"/>
                    <path d="M6.6 6.6C3.6 8.5 2 12 2 12s3.6 7 10 7c1.4 0 2.7-.3 3.8-.8"/>
                </svg>
            </button>
        </div>

        <label>Nueva contraseña</label>
        <div class="password-wrapper">
            <input type="password" name="password_nueva" required minlength="6">
            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m3 3 18 18"/>
                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                    <path d="M9.9 5.2A11 11 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8"/>
                    <path d="M6.6 6.6C3.6 8.5 2 12 2 12s3.6 7 10 7c1.4 0 2.7-.3 3.8-.8"/>
                </svg>
            </button>
        </div>
        <small>Mínimo 6 caracteres, con al menos una letra y un número.</small>

        <label>Confirmar nueva contraseña</label>
        <div class="password-wrapper">
            <input type="password" name="password_nueva_confirmation" required minlength="6">
            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m3 3 18 18"/>
                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                    <path d="M9.9 5.2A11 11 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8"/>
                    <path d="M6.6 6.6C3.6 8.5 2 12 2 12s3.6 7 10 7c1.4 0 2.7-.3 3.8-.8"/>
                </svg>
            </button>
        </div>

        <button type="submit">Actualizar contraseña</button>
    </form>
</div>

</body>
</html>
