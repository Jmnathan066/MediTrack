
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Medicamento | MediTrack EPS</title>

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

            <h1>Eliminar Medicamento</h1>

            <p>Está a punto de eliminar:</p>

            <p>
                <strong>{{ $medicamento->nombre }}</strong>
            </p>

            <p>
                ID:
                <strong>{{ $medicamento->idMedicamento }}</strong>
            </p>

            <p>Para confirmar, escriba el ID del medicamento.</p>

            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form
                action="/medicamentos/eliminar/{{ $medicamento->idMedicamento }}"
                method="POST">

                @csrf

                <label for="confirmacion">ID de confirmación</label>
                <input
                    type="number"
                    id="confirmacion"
                    name="confirmacion"
                    placeholder="Ingrese el ID"
                    required>

                <div class="acciones">
                    <button type="button"
                            onclick="window.location.href='/medicamentos'">
                        Cancelar
                    </button>

                    <button type="submit">Eliminar</button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>
