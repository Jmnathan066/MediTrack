
@php
    $editando = isset($formula);

    $unidades = [
        'tableta' => 'Tabletas',
        'capsula' => 'Cápsulas',
        'frasco' => 'Frascos',
        'ampolla' => 'Ampollas',
        'tubo' => 'Tubos',
        'sobre' => 'Sobres',
        'unidad' => 'Unidades',
        'ml' => 'Mililitros (ml)',
        'g' => 'Gramos (g)',
    ];

    $detallesFormulario = old('medicamentos');

    if ($detallesFormulario === null) {
        $detallesFormulario = $editando
            ? $formula->medicamentos->map(function ($detalle) {
                return [
                    'idDetalle' => $detalle->idDetalle,
                    'idMedicamento' => $detalle->idMedicamento,
                    'dosis' => $detalle->dosis,
                    'frecuencia' => $detalle->frecuencia,
                    'duracion' => $detalle->duracion,
                    'cantidad' => $detalle->cantidad,
                    'unidad' => $detalle->unidad ?? 'unidad',
                    'indicaciones' => $detalle->indicaciones,
                ];
            })->toArray()
            : [[]];
    }

    $idPacienteSeleccionado = old('idPaciente', $formula->idPaciente ?? '');

    $pacienteSeleccionado = $pacientes->firstWhere(
        'id',
        $idPacienteSeleccionado
    );

    $pacientesJson = $pacientes->map(function ($p) {
        return [
            'id' => $p->id,
            'nombre' => $p->nombre,
            'apellido' => $p->apellido,
            'documento' => $p->documento,
            'email' => $p->email,
        ];
    })->values();

    $medicamentosJson = $medicamentos->map(function ($m) {
        return [
            'id' => $m->idMedicamento,
            'nombre' => $m->nombre,
            'categoria' => $m->categoria,
        ];
    })->values();
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fórmulas médicas | MediTrack EPS</title>

    @vite([
        'resources/css/partials.css',
        'resources/css/formulas.css',
        'resources/js/partials.js',
        'resources/js/formulas.js'
    ])
</head>
<body>

@include('partials.sidebar')
@include('partials.topbar')

<script type="application/json" id="datosFormula">
    @json([
        'pacientes' => $pacientesJson,
        'medicamentos' => $medicamentosJson,
        'indiceInicial' => count($detallesFormulario)
    ])
</script>

<main class="content formulas-content">
    <div class="formulas-page">

        {{-- ENCABEZADO --}}
        <header class="formulas-header">
            <div>
                <h1>Fórmulas médicas</h1>
                <p>Registra y administra las fórmulas médicas de tus pacientes.</p>
            </div>
        </header>

        {{-- MENSAJES --}}
        @if(session('success'))
            <div class="formula-alert formula-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="formula-alert formula-alert-danger">
                <strong>Revisa los siguientes errores:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORMULARIO --}}
        <section class="card formula-panel">

            <div class="formula-panel-heading">
                <h2>
                    {{ $editando ? 'Modificar fórmula médica' : 'Nueva fórmula médica' }}
                </h2>

                <p>
                    Completa los datos del paciente y los medicamentos prescritos.
                </p>
            </div>

            <form
                id="formularioFormula"
                method="POST"
                action="{{ $editando
                    ? route('doctor.formulas.actualizar', $formula->idFormula)
                    : route('doctor.formulas.guardar') }}"
            >
                @csrf

                @if($editando)
                    @method('PUT')
                @endif

                <div class="formula-grid">

                    {{-- PACIENTE --}}
                    <div class="formula-field patient-field">
                        <label for="buscarPaciente">Paciente</label>

                        <div class="autocomplete">
                            <input
                                type="text"
                                id="buscarPaciente"
                                placeholder="Buscar por nombre, apellido o documento..."
                                autocomplete="off"
                                value="{{ $pacienteSeleccionado
                                    ? $pacienteSeleccionado->nombre . ' ' . $pacienteSeleccionado->apellido
                                    : '' }}"
                                required
                            >

                            <input
                                type="hidden"
                                name="idPaciente"
                                id="idPaciente"
                                value="{{ $idPacienteSeleccionado }}"
                            >

                            <div
                                id="resultadosPacientes"
                                class="autocomplete-results"
                            ></div>
                        </div>

                        <small id="estadoPaciente" class="selection-status">
                            {{ $pacienteSeleccionado ? 'Paciente seleccionado' : '' }}
                        </small>
                    </div>

                    {{-- FECHA --}}
                    <div class="formula-field date-field">
                        <label for="fechaFormula">Fecha de la fórmula</label>

                        <input
                            type="date"
                            id="fechaFormula"
                            name="fechaFormula"
                            value="{{ old(
                                'fechaFormula',
                                isset($formula)
                                    ? $formula->fechaFormula->format('Y-m-d')
                                    : now()->format('Y-m-d')
                            ) }}"
                            required
                        >
                    </div>

                </div>

                {{-- OBSERVACIONES --}}
                <div class="formula-field observations-field">
                    <label for="observaciones">Observaciones generales</label>

                    <textarea
                        id="observaciones"
                        name="observaciones"
                        rows="3"
                        placeholder="Observaciones de la fórmula"
                    >{{ old('observaciones', $formula->observaciones ?? '') }}</textarea>
                </div>

                <div class="formula-separator"></div>

                {{-- MEDICAMENTOS --}}
                <div class="medicamentos-heading">
                    <div>
                        <h3>Medicamentos prescritos</h3>
                        <p>Agrega uno o varios medicamentos a esta fórmula.</p>
                    </div>
                </div>

                <div id="listaMedicamentos">

                    @foreach($detallesFormulario as $i => $detalle)

                        @php
                            $medicamentoSeleccionado = $medicamentos->firstWhere(
                                'idMedicamento',
                                $detalle['idMedicamento'] ?? null
                            );
                        @endphp

                        <div class="medicamento-card">

                            {{-- IDENTIFICADOR DEL DETALLE EXISTENTE --}}
                            @if(!empty($detalle['idDetalle']))
                                <input
                                    type="hidden"
                                    name="medicamentos[{{ $i }}][idDetalle]"
                                    value="{{ $detalle['idDetalle'] }}"
                                >
                            @endif

                            <div class="medicamento-card-heading">
                                <strong>Medicamento</strong>

                                <button
                                    type="button"
                                    class="eliminarMedicamento"
                                >
                                    Quitar
                                </button>
                            </div>

                            <div class="medicamento-grid">

                                {{-- MEDICAMENTO --}}
                                <div class="formula-field">
                                    <label>Medicamento</label>

                                    <div class="autocomplete">
                                        <input
                                            type="text"
                                            class="buscarMedicamento"
                                            placeholder="Buscar medicamento..."
                                            autocomplete="off"
                                            value="{{ $medicamentoSeleccionado
                                                ? $medicamentoSeleccionado->nombre
                                                : '' }}"
                                            required
                                        >

                                        <input
                                            type="hidden"
                                            class="idMedicamento"
                                            name="medicamentos[{{ $i }}][idMedicamento]"
                                            value="{{ $detalle['idMedicamento'] ?? '' }}"
                                        >

                                        <div class="autocomplete-results resultadosMedicamentos"></div>
                                    </div>

                                    <small class="selection-status estadoMedicamento">
                                        {{ $medicamentoSeleccionado ? 'Medicamento seleccionado' : '' }}
                                    </small>
                                </div>

                                {{-- DOSIS --}}
                                <div class="formula-field">
                                    <label>Dosis</label>

                                    <input
                                        type="text"
                                        name="medicamentos[{{ $i }}][dosis]"
                                        value="{{ $detalle['dosis'] ?? '' }}"
                                        placeholder="Ej. 500 mg o 5 ml"
                                        required
                                    >
                                </div>

                                {{-- FRECUENCIA --}}
                                <div class="formula-field">
                                    <label>Frecuencia</label>

                                    <input
                                        type="text"
                                        name="medicamentos[{{ $i }}][frecuencia]"
                                        value="{{ $detalle['frecuencia'] ?? '' }}"
                                        placeholder="Ej. Cada 8 horas"
                                        required
                                    >
                                </div>

                                {{-- DURACIÓN --}}
                                <div class="formula-field">
                                    <label>Duración</label>

                                    <input
                                        type="text"
                                        name="medicamentos[{{ $i }}][duracion]"
                                        value="{{ $detalle['duracion'] ?? '' }}"
                                        placeholder="Ej. 5 días"
                                        required
                                    >
                                </div>

                                {{-- CANTIDAD --}}
                                <div class="formula-field">
                                    <label>Cantidad a dispensar</label>

                                    <input
                                        type="number"
                                        name="medicamentos[{{ $i }}][cantidad]"
                                        value="{{ $detalle['cantidad'] ?? '' }}"
                                        min="1"
                                        step="1"
                                        placeholder="Ej. 15"
                                        required
                                    >

                                    <small>
                                        Cantidad que se entregará al paciente.
                                    </small>
                                </div>

                                {{-- UNIDAD --}}
                                <div class="formula-field">
                                    <label>Unidad de dispensación</label>

                                    <select
                                        name="medicamentos[{{ $i }}][unidad]"
                                        required
                                    >
                                        <option value="">Selecciona una unidad</option>

                                        @foreach($unidades as $valor => $etiqueta)
                                            <option
                                                value="{{ $valor }}"
                                                {{ ($detalle['unidad'] ?? 'unidad') === $valor ? 'selected' : '' }}
                                            >
                                                {{ $etiqueta }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <small>
                                        Ej. 15 tabletas, 1 frasco o 120 ml.
                                    </small>
                                </div>

                                {{-- INDICACIONES --}}
                                <div class="formula-field">
                                    <label>Indicaciones</label>

                                    <input
                                        type="text"
                                        name="medicamentos[{{ $i }}][indicaciones]"
                                        value="{{ $detalle['indicaciones'] ?? '' }}"
                                        placeholder="Indicaciones adicionales"
                                    >
                                </div>

                            </div>
                        </div>

                    @endforeach

                </div>

                <button
                    type="button"
                    id="agregarMedicamento"
                    class="formula-btn-secondary"
                >
                    + Agregar otro medicamento
                </button>

                <div class="formula-actions">

                    <button type="submit" class="formula-btn-primary">
                        {{ $editando ? 'Guardar cambios' : 'Registrar fórmula' }}
                    </button>

                    @if($editando)
                        <a
                            href="{{ route('doctor.formulas') }}"
                            class="btn-cancelar-edicion"
                        >
                            <span>✕</span>
                            Cancelar edición
                        </a>
                    @endif

                </div>
            </form>
        </section>

        {{-- DISPONIBILIDAD HU-027 --}}
        <section
            class="card formula-panel disponibilidad-panel"
            id="panelDisponibilidad"
            data-url-template="{{ route('doctor.medicamentos.disponibilidad', ['id' => '__ID__']) }}"
        >

            <div class="formula-panel-heading">
                <h2>Disponibilidad de medicamentos por sede</h2>

                <p>
                    Consulta las existencias de un medicamento en las diferentes sedes.
                </p>
            </div>

            <button
                type="button"
                id="toggleDisponibilidad"
                class="formula-btn-secondary"
                aria-expanded="false"
                aria-controls="contenidoDisponibilidad"
            >
                Consultar disponibilidad
            </button>

            <div id="contenidoDisponibilidad" hidden>

                <div class="formula-field disponibilidad-buscador">
                    <label for="buscarDisponibilidad">
                        Buscar medicamento
                    </label>

                    <div class="autocomplete">
                        <input
                            type="text"
                            id="buscarDisponibilidad"
                            placeholder="Escribe el nombre del medicamento..."
                            autocomplete="off"
                        >

                        <input
                            type="hidden"
                            id="idMedicamentoDisponibilidad"
                        >

                        <div
                            id="resultadosDisponibilidad"
                            class="autocomplete-results"
                        ></div>
                    </div>

                    <small
                        id="estadoBusquedaDisponibilidad"
                        class="selection-status"
                    >
                        Selecciona un medicamento para consultar sus existencias.
                    </small>
                </div>

                <div
                    id="resultadosDisponibilidadSedes"
                    class="disponibilidad-resultados"
                    aria-live="polite"
                ></div>

            </div>
        </section>

        {{-- HISTORIAL --}}
        <section class="card formula-panel">

            <div class="formula-panel-heading">
                <h2>Fórmulas registradas</h2>
                <p>Consulta y modifica las fórmulas que has registrado.</p>
            </div>

            <div class="formula-table-container">

                <table class="formula-table">

                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Fecha</th>
                            <th>Medicamentos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($formulas as $item)

                            <tr>

                                <td>
                                    {{ $item->paciente->nombre ?? 'Paciente no disponible' }}
                                    {{ $item->paciente->apellido ?? '' }}
                                </td>

                                <td>
                                    {{ $item->fechaFormula->format('d/m/Y') }}
                                </td>

                                <td>
                                    @foreach($item->medicamentos as $detalle)

                                        <div class="formula-medicine-summary">

                                            {{ $detalle->medicamento->nombre ?? 'Medicamento no disponible' }}

                                            <span>
                                                ({{ $detalle->cantidad }}
                                                {{ $unidades[$detalle->unidad ?? ''] ?? $detalle->unidad ?? 'Unidad no especificada' }})
                                            </span>

                                        </div>

                                    @endforeach
                                </td>

                                <td>
                                    <span class="formula-status">
                                        {{ ucfirst($item->estado) }}
                                    </span>
                                </td>

                                <td>
                                    <a
                                        href="{{ route('doctor.formulas.editar', $item->idFormula) }}"
                                        class="btn-modificar-formula"
                                    >
                                        <span>✎</span>
                                        Modificar
                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="formula-empty">
                                    Todavía no has registrado fórmulas médicas.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>

            </div>

            <div class="formula-pagination">
                {{ $formulas->links() }}
            </div>

        </section>

    </div>
</main>

{{-- PLANTILLA PARA AGREGAR MEDICAMENTOS --}}
<template id="plantillaMedicamento">

    <div class="medicamento-card">

        <div class="medicamento-card-heading">
            <strong>Medicamento</strong>

            <button
                type="button"
                class="eliminarMedicamento"
            >
                Quitar
            </button>
        </div>

        <div class="medicamento-grid">

            {{-- MEDICAMENTO --}}
            <div class="formula-field">
                <label>Medicamento</label>

                <div class="autocomplete">
                    <input
                        type="text"
                        class="buscarMedicamento"
                        placeholder="Buscar medicamento..."
                        autocomplete="off"
                        required
                    >

                    <input
                        type="hidden"
                        class="idMedicamento"
                        name="medicamentos[INDEX][idMedicamento]"
                    >

                    <div class="autocomplete-results resultadosMedicamentos"></div>
                </div>

                <small class="selection-status estadoMedicamento"></small>
            </div>

            {{-- DOSIS --}}
            <div class="formula-field">
                <label>Dosis</label>

                <input
                    type="text"
                    name="medicamentos[INDEX][dosis]"
                    placeholder="Ej. 500 mg o 5 ml"
                    required
                >
            </div>

            {{-- FRECUENCIA --}}
            <div class="formula-field">
                <label>Frecuencia</label>

                <input
                    type="text"
                    name="medicamentos[INDEX][frecuencia]"
                    placeholder="Ej. Cada 8 horas"
                    required
                >
            </div>

            {{-- DURACIÓN --}}
            <div class="formula-field">
                <label>Duración</label>

                <input
                    type="text"
                    name="medicamentos[INDEX][duracion]"
                    placeholder="Ej. 5 días"
                    required
                >
            </div>

            {{-- CANTIDAD --}}
            <div class="formula-field">
                <label>Cantidad a dispensar</label>

                <input
                    type="number"
                    name="medicamentos[INDEX][cantidad]"
                    min="1"
                    step="1"
                    placeholder="Ej. 15"
                    required
                >

                <small>
                    Cantidad que se entregará al paciente.
                </small>
            </div>

            {{-- UNIDAD --}}
            <div class="formula-field">
                <label>Unidad de dispensación</label>

                <select
                    name="medicamentos[INDEX][unidad]"
                    required
                >
                    <option value="">Selecciona una unidad</option>
                    <option value="tableta">Tabletas</option>
                    <option value="capsula">Cápsulas</option>
                    <option value="frasco">Frascos</option>
                    <option value="ampolla">Ampollas</option>
                    <option value="tubo">Tubos</option>
                    <option value="sobre">Sobres</option>
                    <option value="unidad">Unidades</option>
                    <option value="ml">Mililitros (ml)</option>
                    <option value="g">Gramos (g)</option>
                </select>

                <small>
                    Ej. 15 tabletas, 1 frasco o 120 ml.
                </small>
            </div>

            {{-- INDICACIONES --}}
            <div class="formula-field">
                <label>Indicaciones</label>

                <input
                    type="text"
                    name="medicamentos[INDEX][indicaciones]"
                    placeholder="Indicaciones adicionales"
                >
            </div>

        </div>
    </div>

</template>

</body>
</html>
