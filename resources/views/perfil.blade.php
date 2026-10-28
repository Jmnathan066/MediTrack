<!DOCTYPE html>
<html>
<head>
    <title>Mi perfil</title>
    @vite([
        'resources/css/partials.css',
        'resources/css/perfil/perfil.css',
        'resources/js/partials.js'
    ])
</head>
<body>

@include('partials.sidebar')
@include('partials.topbar')

<div class="content">
    <h1>Mi perfil</h1>

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

    <form action="/perfil" method="POST" class="form-perfil">
        @csrf

        <label>Nombre</label>
        <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required>

        <label>Apellido</label>
        <input type="text" name="apellido" value="{{ old('apellido', $usuario->apellido) }}" required>

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required>

        <label>Teléfono</label>
        <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}" required>

        <label>Dirección</label>
        <input type="text" name="direccion" value="{{ old('direccion', $usuario->direccion) }}" required>

        <label>Documento</label>
        <input type="text" value="{{ $usuario->documento }}" disabled>
        <small>El documento no se puede modificar.</small>

        <label>Código de usuario</label>
        <input type="text" value="{{ $usuario->codigo }}" disabled>

        <button type="submit">Guardar cambios</button>
    </form>
</div>

</body>
</html>
