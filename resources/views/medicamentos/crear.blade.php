
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Medicamento | MediTrack EPS</title>

    @vite([
        'resources/css/partials.css',
        'resources/css/medic/medicamento.css',
        'resources/js/partials.js',
        'resources/js/medic/medicamento.js'
    ])
</head>
<body class="medicamento-page">

    @include('partials.topbar')

    <div class="main-content medicamento-main">
        <div class="container">

            <h1>Registrar Medicamento</h1>

            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('medicamentos.guardar') }}" method="POST">
                @csrf

                <label for="nombre">Nombre</label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    placeholder="Ej. Acetaminofén"
                    value="{{ old('nombre') }}"
                    required
                >

                <label for="categoria">Categoría</label>
                <input
                    type="text"
                    id="categoria"
                    name="categoria"
                    placeholder="Ej. Analgésicos"
                    value="{{ old('categoria') }}"
                    required
                >

                <label for="descripcion">Descripción</label>
                <textarea
                    id="descripcion"
                    name="descripcion"
                    placeholder="Describe el medicamento, su uso o sus características..."
                >{{ old('descripcion') }}</textarea>

                <label for="fechaVencimiento">Fecha de vencimiento</label>
                <input
                    type="date"
                    id="fechaVencimiento"
                    name="fechaVencimiento"
                    value="{{ old('fechaVencimiento') }}"
                    required
                >

                <label for="stock">Stock</label>
                <input
                    type="number"
                    id="stock"
                    name="stock"
                    placeholder="Ej. 100"
                    value="{{ old('stock') }}"
                    min="0"
                    required
                >

                <label for="stockMinimo">Stock mínimo</label>
                <input
                    type="number"
                    id="stockMinimo"
                    name="stockMinimo"
                    placeholder="Ej. 10"
                    value="{{ old('stockMinimo') }}"
                    min="0"
                    required
                >

                <label for="valor">Valor</label>
                <input
                    type="number"
                    id="valor"
                    name="valor"
                    placeholder="Ej. 2500.00"
                    value="{{ old('valor') }}"
                    step="0.01"
                    min="0"
                    required
                >

                <div class="acciones">
                    <button type="submit">Guardar</button>

                    <button
                        type="button"
                        onclick="window.location.href='/medicamentos'"
                    >
                        Volver
                    </button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>
