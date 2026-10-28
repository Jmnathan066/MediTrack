
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Medicamento | MediTrack EPS</title>

    @vite([
        'resources/css/partials.css',
        'resources/css/medic/medicamento.css',
        'resources/js/partials.js'
    ])
</head>
<body class="medicamento-page">

    @include('partials.topbar')

    <div class="main-content medicamento-main">
        <div class="container">

            <h1>Editar Medicamento</h1>

            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form
                action="/medicamentos/actualizar/{{ $medicamento->idMedicamento }}"
                method="POST">

                @csrf

                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre"
                       value="{{ old('nombre', $medicamento->nombre) }}" required>

                <label for="categoria">Categoría</label>
                <input type="text" id="categoria" name="categoria"
                       value="{{ old('categoria', $medicamento->categoria) }}" required>

                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion">{{ old('descripcion', $medicamento->descripcion) }}</textarea>

                <label for="fechaVencimiento">Fecha de vencimiento</label>
                <input type="date" id="fechaVencimiento"
                       name="fechaVencimiento"
                       value="{{ old('fechaVencimiento', $medicamento->fechaVencimiento) }}"
                       required>

                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock"
                       value="{{ old('stock', $medicamento->stock) }}"
                       min="0" required>

                <label for="stockMinimo">Stock mínimo</label>
                <input type="number" id="stockMinimo" name="stockMinimo"
                       value="{{ old('stockMinimo', $medicamento->stockMinimo) }}"
                       min="0" required>

                <label for="valor">Valor</label>
                <input type="number" id="valor" name="valor"
                       value="{{ old('valor', $medicamento->valor) }}"
                       step="0.01" min="0" required>

                <div class="acciones">
                    <button type="submit">Guardar Cambios</button>

                    <button type="button"
                            onclick="window.location.href='/medicamentos'">
                        Volver
                    </button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>
