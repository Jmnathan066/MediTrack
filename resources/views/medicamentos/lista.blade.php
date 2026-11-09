
<!DOCTYPE html>
<html>
<head>
    <title>Medicamentos</title>

    @vite([
        'resources/css/partials.css',
        'resources/css/medic/medicamento.css',
        'resources/js/partials.js',
        'resources/js/medic/medicamento.js'
    ])
</head>
<body>

@include('partials.sidebar')
@include('partials.topbar')

<div class="main-content">

    <div class="container">

        <h1>Listado de Medicamentos</h1>

        <form method="GET" action="/medicamentos" class="busqueda-form">

            <input
                type="text"
                name="busqueda"
                placeholder="Buscar por ID, nombre o categoría"
                value="{{ $busqueda ?? '' }}">

            <button type="submit">
                Buscar
            </button>

        </form>

        <br>

        @if(in_array(session('tipoUsuario'), ['admin', 'entidad']))
            <button onclick="window.location.href='/medicamentos/crear'">
                Registrar Medicamento
            </button>
        @endif

        <br><br>

        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Valor</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>

            @forelse($medicamentos as $medicamento)

                <tr class="{{ !$medicamento->estado ? 'medicamento-inactivo' : '' }}">

                    <td>{{ $medicamento->idMedicamento }}</td>
                    <td>{{ $medicamento->nombre }}</td>
                    <td>{{ $medicamento->categoria }}</td>
                    <td>{{ $medicamento->stock }}</td>
                    <td>${{ $medicamento->valor }}</td>

                    <td>
                        @if($medicamento->estado)
                            <span class="estado-medicamento estado-activo">
                                Activo
                            </span>
                        @else
                            <span class="estado-medicamento estado-inactivo">
                                Inactivo
                            </span>
                        @endif
                    </td>

                    <td>

                        <button onclick="toggleDetalle({{ $medicamento->idMedicamento }})">
                            Ver
                        </button>

                        @if(in_array(session('tipoUsuario'), ['admin', 'entidad']))
                            <button
                                onclick="window.location.href='/medicamentos/editar/{{ $medicamento->idMedicamento }}'">
                                Editar
                            </button>
                        @endif

                        @if(session('tipoUsuario') === 'admin')

                            <button
                                class="{{ $medicamento->estado ? 'btn-inactivar' : 'btn-reactivar' }}"
                                onclick="window.location.href='/medicamentos/eliminar/{{ $medicamento->idMedicamento }}'">

                                {{ $medicamento->estado ? 'Inactivar' : 'Reactivar' }}

                            </button>

                        @endif

                    </td>
                </tr>

                <tr id="detalle-{{ $medicamento->idMedicamento }}"
                    class="detalle hidden {{ !$medicamento->estado ? 'medicamento-inactivo' : '' }}">

                    <td colspan="7">

                        <strong>Descripción:</strong>
                        {{ $medicamento->descripcion }}

                        <br><br>

                        <strong>Fecha de vencimiento:</strong>
                        {{ $medicamento->fechaVencimiento }}

                        <br><br>

                        <strong>Stock mínimo:</strong>
                        {{ $medicamento->stockMinimo }}

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7">
                        No existen medicamentos registrados.
                    </td>
                </tr>

            @endforelse

        </table>

    </div>

</div>

</body>
</html>
