<!DOCTYPE html>
<html>
<head>
    <title>Cambiar contraseña</title>
      @vite([
        'resources/css/partials.css',
        'resources/css/perfil/password.css',
        'resources/js/partials.js'
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
        <input type="password" name="password_actual" required>

        <label>Nueva contraseña</label>
        <input type="password" name="password_nueva" required minlength="6">
        <small>Mínimo 6 caracteres, con al menos una letra y un número.</small>

        <label>Confirmar nueva contraseña</label>
        <input type="password" name="password_nueva_confirmation" required minlength="6">

        <button type="submit">Actualizar contraseña</button>
    </form>
</div>

</body>
</html>
