
<?php
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fórmulas médicas | MediTrack EPS</title>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/formulas.css',
        'resources/js/partials.js',
        'resources/js/formulas.js'
    ]); ?>
</head>
<body>

<?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script type="application/json" id="datosFormula">
    <?php echo json_encode([
        'pacientes' => $pacientesJson, 'medicamentos' => $medicamentosJson, 'indiceInicial' => count($detallesFormulario)
    ]) ?>
</script>

<main class="content formulas-content">
    <div class="formulas-page">

        
        <header class="formulas-header">
            <div>
                <h1>Fórmulas médicas</h1>
                <p>Registra y administra las fórmulas médicas de tus pacientes.</p>
            </div>
        </header>

        
        <?php if(session('success')): ?>
            <div class="formula-alert formula-alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="formula-alert formula-alert-danger">
                <strong>Revisa los siguientes errores:</strong>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        
        <section class="card formula-panel">

            <div class="formula-panel-heading">
                <h2>
                    <?php echo e($editando ? 'Modificar fórmula médica' : 'Nueva fórmula médica'); ?>

                </h2>

                <p>
                    Completa los datos del paciente y los medicamentos prescritos.
                </p>
            </div>

            <form
                id="formularioFormula"
                method="POST"
                action="<?php echo e($editando
                    ? route('doctor.formulas.actualizar', $formula->idFormula)
                    : route('doctor.formulas.guardar')); ?>"
            >
                <?php echo csrf_field(); ?>

                <?php if($editando): ?>
                    <?php echo method_field('PUT'); ?>
                <?php endif; ?>

                <div class="formula-grid">

                    
                    <div class="formula-field patient-field">
                        <label for="buscarPaciente">Paciente</label>

                        <div class="autocomplete">
                            <input
                                type="text"
                                id="buscarPaciente"
                                placeholder="Buscar por nombre, apellido o documento..."
                                autocomplete="off"
                                value="<?php echo e($pacienteSeleccionado
                                    ? $pacienteSeleccionado->nombre . ' ' . $pacienteSeleccionado->apellido
                                    : ''); ?>"
                                required
                            >

                            <input
                                type="hidden"
                                name="idPaciente"
                                id="idPaciente"
                                value="<?php echo e($idPacienteSeleccionado); ?>"
                            >

                            <div
                                id="resultadosPacientes"
                                class="autocomplete-results"
                            ></div>
                        </div>

                        <small id="estadoPaciente" class="selection-status">
                            <?php echo e($pacienteSeleccionado ? 'Paciente seleccionado' : ''); ?>

                        </small>
                    </div>

                    
                    <div class="formula-field date-field">
                        <label for="fechaFormula">Fecha de la fórmula</label>

                        <input
                            type="date"
                            id="fechaFormula"
                            name="fechaFormula"
                            value="<?php echo e(old(
                                'fechaFormula',
                                isset($formula)
                                    ? $formula->fechaFormula->format('Y-m-d')
                                    : now()->format('Y-m-d')
                            )); ?>"
                            required
                        >
                    </div>

                </div>

                
                <div class="formula-field observations-field">
                    <label for="observaciones">Observaciones generales</label>

                    <textarea
                        id="observaciones"
                        name="observaciones"
                        rows="3"
                        placeholder="Observaciones de la fórmula"
                    ><?php echo e(old('observaciones', $formula->observaciones ?? '')); ?></textarea>
                </div>

                <div class="formula-separator"></div>

                
                <div class="medicamentos-heading">
                    <div>
                        <h3>Medicamentos prescritos</h3>
                        <p>Agrega uno o varios medicamentos a esta fórmula.</p>
                    </div>
                </div>

                <div id="listaMedicamentos">

                    <?php $__currentLoopData = $detallesFormulario; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <?php
                            $medicamentoSeleccionado = $medicamentos->firstWhere(
                                'idMedicamento',
                                $detalle['idMedicamento'] ?? null
                            );
                        ?>

                        <div class="medicamento-card">

                            
                            <?php if(!empty($detalle['idDetalle'])): ?>
                                <input
                                    type="hidden"
                                    name="medicamentos[<?php echo e($i); ?>][idDetalle]"
                                    value="<?php echo e($detalle['idDetalle']); ?>"
                                >
                            <?php endif; ?>

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

                                
                                <div class="formula-field">
                                    <label>Medicamento</label>

                                    <div class="autocomplete">
                                        <input
                                            type="text"
                                            class="buscarMedicamento"
                                            placeholder="Buscar medicamento..."
                                            autocomplete="off"
                                            value="<?php echo e($medicamentoSeleccionado
                                                ? $medicamentoSeleccionado->nombre
                                                : ''); ?>"
                                            required
                                        >

                                        <input
                                            type="hidden"
                                            class="idMedicamento"
                                            name="medicamentos[<?php echo e($i); ?>][idMedicamento]"
                                            value="<?php echo e($detalle['idMedicamento'] ?? ''); ?>"
                                        >

                                        <div class="autocomplete-results resultadosMedicamentos"></div>
                                    </div>

                                    <small class="selection-status estadoMedicamento">
                                        <?php echo e($medicamentoSeleccionado ? 'Medicamento seleccionado' : ''); ?>

                                    </small>
                                </div>

                                
                                <div class="formula-field">
                                    <label>Dosis</label>

                                    <input
                                        type="text"
                                        name="medicamentos[<?php echo e($i); ?>][dosis]"
                                        value="<?php echo e($detalle['dosis'] ?? ''); ?>"
                                        placeholder="Ej. 500 mg o 5 ml"
                                        required
                                    >
                                </div>

                                
                                <div class="formula-field">
                                    <label>Frecuencia</label>

                                    <input
                                        type="text"
                                        name="medicamentos[<?php echo e($i); ?>][frecuencia]"
                                        value="<?php echo e($detalle['frecuencia'] ?? ''); ?>"
                                        placeholder="Ej. Cada 8 horas"
                                        required
                                    >
                                </div>

                                
                                <div class="formula-field">
                                    <label>Duración</label>

                                    <input
                                        type="text"
                                        name="medicamentos[<?php echo e($i); ?>][duracion]"
                                        value="<?php echo e($detalle['duracion'] ?? ''); ?>"
                                        placeholder="Ej. 5 días"
                                        required
                                    >
                                </div>

                                
                                <div class="formula-field">
                                    <label>Cantidad a dispensar</label>

                                    <input
                                        type="number"
                                        name="medicamentos[<?php echo e($i); ?>][cantidad]"
                                        value="<?php echo e($detalle['cantidad'] ?? ''); ?>"
                                        min="1"
                                        step="1"
                                        placeholder="Ej. 15"
                                        required
                                    >

                                    <small>
                                        Cantidad que se entregará al paciente.
                                    </small>
                                </div>

                                
                                <div class="formula-field">
                                    <label>Unidad de dispensación</label>

                                    <select
                                        name="medicamentos[<?php echo e($i); ?>][unidad]"
                                        required
                                    >
                                        <option value="">Selecciona una unidad</option>

                                        <?php $__currentLoopData = $unidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $etiqueta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option
                                                value="<?php echo e($valor); ?>"
                                                <?php echo e(($detalle['unidad'] ?? 'unidad') === $valor ? 'selected' : ''); ?>

                                            >
                                                <?php echo e($etiqueta); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>

                                    <small>
                                        Ej. 15 tabletas, 1 frasco o 120 ml.
                                    </small>
                                </div>

                                
                                <div class="formula-field">
                                    <label>Indicaciones</label>

                                    <input
                                        type="text"
                                        name="medicamentos[<?php echo e($i); ?>][indicaciones]"
                                        value="<?php echo e($detalle['indicaciones'] ?? ''); ?>"
                                        placeholder="Indicaciones adicionales"
                                    >
                                </div>

                            </div>
                        </div>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
                        <?php echo e($editando ? 'Guardar cambios' : 'Registrar fórmula'); ?>

                    </button>

                    <?php if($editando): ?>
                        <a
                            href="<?php echo e(route('doctor.formulas')); ?>"
                            class="btn-cancelar-edicion"
                        >
                            <span>✕</span>
                            Cancelar edición
                        </a>
                    <?php endif; ?>

                </div>
            </form>
        </section>

        
        <section
            class="card formula-panel disponibilidad-panel"
            id="panelDisponibilidad"
            data-url-template="<?php echo e(route('doctor.medicamentos.disponibilidad', ['id' => '__ID__'])); ?>"
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

                        <?php $__empty_1 = true; $__currentLoopData = $formulas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                            <tr>

                                <td>
                                    <?php echo e($item->paciente->nombre ?? 'Paciente no disponible'); ?>

                                    <?php echo e($item->paciente->apellido ?? ''); ?>

                                </td>

                                <td>
                                    <?php echo e($item->fechaFormula->format('d/m/Y')); ?>

                                </td>

                                <td>
                                    <?php $__currentLoopData = $item->medicamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <div class="formula-medicine-summary">

                                            <?php echo e($detalle->medicamento->nombre ?? 'Medicamento no disponible'); ?>


                                            <span>
                                                (<?php echo e($detalle->cantidad); ?>

                                                <?php echo e($unidades[$detalle->unidad ?? ''] ?? $detalle->unidad ?? 'Unidad no especificada'); ?>)
                                            </span>

                                        </div>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>

                                <td>
                                    <span class="formula-status">
                                        <?php echo e(ucfirst($item->estado)); ?>

                                    </span>
                                </td>

                                <td>
                                    <a
                                        href="<?php echo e(route('doctor.formulas.editar', $item->idFormula)); ?>"
                                        class="btn-modificar-formula"
                                    >
                                        <span>✎</span>
                                        Modificar
                                    </a>
                                </td>

                            </tr>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                            <tr>
                                <td colspan="5" class="formula-empty">
                                    Todavía no has registrado fórmulas médicas.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>
                </table>

            </div>

            <div class="formula-pagination">
                <?php echo e($formulas->links()); ?>

            </div>

        </section>

    </div>
</main>


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

            
            <div class="formula-field">
                <label>Dosis</label>

                <input
                    type="text"
                    name="medicamentos[INDEX][dosis]"
                    placeholder="Ej. 500 mg o 5 ml"
                    required
                >
            </div>

            
            <div class="formula-field">
                <label>Frecuencia</label>

                <input
                    type="text"
                    name="medicamentos[INDEX][frecuencia]"
                    placeholder="Ej. Cada 8 horas"
                    required
                >
            </div>

            
            <div class="formula-field">
                <label>Duración</label>

                <input
                    type="text"
                    name="medicamentos[INDEX][duracion]"
                    placeholder="Ej. 5 días"
                    required
                >
            </div>

            
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
<?php /**PATH C:\Users\davim\medicine\resources\views/doctor/formulas.blade.php ENDPATH**/ ?>