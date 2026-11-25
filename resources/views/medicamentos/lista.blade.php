
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos | MediTrack EPS</title>

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

            @if(session('success'))
                <div class="success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="GET" action="/medicamentos" class="busqueda-form">
                <input
                    type="text"
                    name="busqueda"
                    placeholder="Buscar por ID, nombre o categoría"
                    value="{{ $busqueda ?? '' }}"
                >

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

            @php
                $tipoUsuario = session('tipoUsuario');
                $esPaciente = $tipoUsuario === 'paciente';
                $puedeGestionar = in_array($tipoUsuario, ['admin', 'entidad']);
                $totalColumnas = $esPaciente ? 6 : 7;
            @endphp

            <table border="1">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Valor</th>

                        @if(!$esPaciente)
                            <th>Estado</th>
                        @endif

                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($medicamentos as $medicamento)

                        @php
                            $inventario = $medicamento->inventarios->first();

                            $estadoActivo = $inventario
                                ? $inventario->estado
                                : false;

                            $stockTotal = $inventario
                                ? $inventario->lotes
                                    ->where('estado', true)
                                    ->sum('stockDisponible')
                                : 0;

                            $stockMinimo = $inventario
                                ? $inventario->stockMinimo
                                : 0;
                        @endphp

                        <tr class="{{ !$estadoActivo ? 'medicamento-inactivo' : '' }}">

                            <td>{{ $medicamento->idMedicamento }}</td>

                            <td>{{ $medicamento->nombre }}</td>

                            <td>{{ $medicamento->categoria }}</td>

                            <td>{{ $stockTotal }}</td>

                            <td>${{ number_format($medicamento->valor, 2, ',', '.') }}</td>

                            @if(!$esPaciente)
                                <td>
                                    @if($estadoActivo)
                                        <span class="estado-medicamento estado-activo">
                                            Activo
                                        </span>
                                    @else
                                        <span class="estado-medicamento estado-inactivo">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                            @endif

                            <td>

                                {{-- VER --}}
                                <button
                                    type="button"
                                    onclick="toggleDetalle({{ $medicamento->idMedicamento }})"
                                >
                                    Ver
                                </button>

                                {{-- EDITAR --}}
                                @if($puedeGestionar)
                                    <button
                                        type="button"
                                        onclick="window.location.href='/medicamentos/editar/{{ $medicamento->idMedicamento }}'"
                                    >
                                        Editar
                                    </button>
                                @endif

                                {{-- LOTES --}}
                                @if($puedeGestionar)
                                    <button
                                        type="button"
                                        class="btn-lotes"
                                        onclick="toggleAccionesLotes({{ $medicamento->idMedicamento }})"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="m12 2 9 5-9 5-9-5 9-5Z"/>
                                            <path d="m3 12 9 5 9-5"/>
                                            <path d="m3 17 9 5 9-5"/>
                                        </svg>
                                        Lotes
                                    </button>
                                @endif

                                {{-- INACTIVAR / REACTIVAR --}}
                                @if($tipoUsuario === 'admin')
                                    <button
                                        type="button"
                                        class="{{ $estadoActivo ? 'btn-inactivar' : 'btn-reactivar' }}"
                                        onclick="window.location.href='/medicamentos/eliminar/{{ $medicamento->idMedicamento }}'"
                                    >
                                        {{ $estadoActivo ? 'Inactivar' : 'Reactivar' }}
                                    </button>
                                @endif

                            </td>

                        </tr>

                        {{-- DETALLE DEL MEDICAMENTO --}}
                        <tr
                            id="detalle-{{ $medicamento->idMedicamento }}"
                            class="detalle hidden {{ !$estadoActivo ? 'medicamento-inactivo' : '' }}"
                        >

                            <td colspan="{{ $totalColumnas }}">

                                <strong>Descripción:</strong>
                                {{ $medicamento->descripcion }}

                                <br><br>

                                <strong>Stock mínimo:</strong>
                                {{ $stockMinimo }}

                                <br><br>

                                <strong>Stock total en esta sede:</strong>
                                {{ $stockTotal }}

                                @if(!$esPaciente)
                                    <br><br>

                                    <strong>Estado del inventario:</strong>

                                    @if($estadoActivo)
                                        <span class="estado-medicamento estado-activo">
                                            Activo
                                        </span>
                                    @else
                                        <span class="estado-medicamento estado-inactivo">
                                            Inactivo
                                        </span>
                                    @endif
                                @endif

                            </td>

                        </tr>

                        {{-- SECCIÓN INDEPENDIENTE DE LOTES --}}
                        @if($puedeGestionar)

                            <tr
                                id="acciones-lotes-{{ $medicamento->idMedicamento }}"
                                class="detalle hidden"
                            >

                                <td colspan="{{ $totalColumnas }}">

                                    <strong>
                                        Lotes registrados de {{ $medicamento->nombre }}:
                                    </strong>

                                    @if($inventario && $inventario->lotes->count() > 0)

                                        <table border="1" width="100%">

                                            <thead>
                                                <tr>
                                                    <th>Código del lote</th>
                                                    <th>Fecha de ingreso</th>
                                                    <th>Fecha de vencimiento</th>
                                                    <th>Cantidad inicial</th>
                                                    <th>Stock disponible</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                @foreach($inventario->lotes->sortBy('fechaVencimiento') as $lote)

                                                    <tr class="{{ !$lote->estado ? 'medicamento-inactivo' : '' }}">

                                                        <td>{{ $lote->codigoLote }}</td>

                                                        <td>
                                                            {{ \Carbon\Carbon::parse($lote->fechaIngreso)->format('d/m/Y') }}
                                                        </td>

                                                        <td>
                                                            {{ \Carbon\Carbon::parse($lote->fechaVencimiento)->format('d/m/Y') }}
                                                        </td>

                                                        <td>{{ $lote->cantidadInicial }}</td>

                                                        <td>
                                                            {{ $lote->estado ? $lote->stockDisponible : 0 }}
                                                        </td>

                                                        <td>
                                                            @if($lote->estado)
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
                                                            <form
                                                                method="POST"
                                                                action="{{ route('medicamentos.lotes.estado', [
                                                                    'idMedicamento' => $medicamento->idMedicamento,
                                                                    'idLote' => $lote->idLote
                                                                ]) }}"
                                                                onsubmit="return confirm('¿Estás seguro de que deseas {{ $lote->estado ? 'inactivar' : 'reactivar' }} este lote?');"
                                                            >
                                                                @csrf

                                                                <button
                                                                    type="submit"
                                                                    class="{{ $lote->estado ? 'btn-inactivar' : 'btn-reactivar' }}"
                                                                >
                                                                    {{ $lote->estado ? 'Inactivar' : 'Reactivar' }}
                                                                </button>
                                                            </form>
                                                        </td>

                                                    </tr>

                                                @endforeach

                                            </tbody>

                                        </table>

                                    @else

                                        <p>No hay lotes registrados para este medicamento.</p>

                                    @endif

                                </td>

                            </tr>

                        @endif

                    @empty

                        <tr>
                            <td colspan="{{ $totalColumnas }}">
                                No existen medicamentos registrados en esta sede.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>

    <script>
        function toggleAccionesLotes(id) {
            const fila = document.getElementById('acciones-lotes-' + id);

            if (fila) {
                fila.classList.toggle('hidden');
            }
        }
    </script>

</body>

</html>
