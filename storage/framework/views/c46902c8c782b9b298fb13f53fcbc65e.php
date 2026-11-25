
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Validar fórmulas | MediTrack EPS</title>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/validar-formulas.css',
        'resources/js/partials.js'
    ]); ?>
</head>
<body>

<?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="content validar-formulas-content">

    <div class="validar-formulas-page">

        
        <header class="vf-header">
            <div>
                <h1>Validar fórmulas</h1>
                <p>
                    Consulta las fórmulas médicas de los pacientes
                    antes de realizar la entrega de medicamentos.
                </p>
            </div>
        </header>

        
        <?php if(session('success')): ?>
            <div class="vf-alert vf-alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="vf-alert vf-alert-danger">
                <strong>No se pudo realizar la consulta:</strong>

                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        
        <section class="vf-panel">

            <div class="vf-panel-heading">
                <div class="vf-heading-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg>
                </div>

                <div>
                    <h2>Buscar paciente</h2>
                    <p>
                        Ingresa el documento para consultar sus fórmulas médicas.
                    </p>
                </div>
            </div>

           
            <form
                action="<?php echo e(route('empresa.formulas.index')); ?>"
                method="GET"
                class="vf-search-form"
            >
                <div class="vf-search-field">
                    <label for="busqueda">Documento o nombre del paciente</label>

                    <input
                        type="text"
                        id="busqueda"
                        name="busqueda"
                        value="<?php echo e($busqueda); ?>"
                        placeholder="Ej. 1234567890 o Juan Pérez"
                        maxlength="100"
                        autocomplete="off"
                        required
                    >
                </div>

                <button type="submit" class="vf-btn-search">
                    <svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg>

                    Buscar paciente
                </button>

                <?php if($busqueda !== ''): ?>
                    <a
                        href="<?php echo e(route('empresa.formulas.index')); ?>"
                        class="vf-btn-clear"
                    >
                        Limpiar búsqueda
                    </a>
                <?php endif; ?>
            </form>


        </section>

      

<?php if($busqueda !== ''): ?>

    
    <?php if($pacientes->isNotEmpty()): ?>

        <section class="vf-panel">

            <div class="vf-section-title">
                <div>
                    <h2>Seleccionar paciente</h2>
                    <p>
                        Encontramos varios pacientes. Verifica el documento
                        y selecciona a la persona correcta.
                    </p>
                </div>

                <span class="vf-count">
                    <?php echo e($pacientes->count()); ?>

                    <?php echo e($pacientes->count() === 1 ? 'resultado' : 'resultados'); ?>

                </span>
            </div>

            <?php $__currentLoopData = $pacientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resultado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <form
                    action="<?php echo e(route('empresa.formulas.index')); ?>"
                    method="GET"
                    class="vf-patient-result"
                >
                    <input
                        type="hidden"
                        name="busqueda"
                        value="<?php echo e($busqueda); ?>"
                    >

                    <input
                        type="hidden"
                        name="paciente_id"
                        value="<?php echo e($resultado->id); ?>"
                    >

                    <div class="vf-patient-data">
                        <span>Nombre completo</span>
                        <strong>
                            <?php echo e($resultado->nombre); ?>

                            <?php echo e($resultado->apellido); ?>

                        </strong>
                    </div>

                    <div class="vf-patient-data">
                        <span>Documento</span>
                        <strong><?php echo e($resultado->documento); ?></strong>
                    </div>

                    <button type="submit" class="vf-btn-search">
                        Consultar fórmulas
                    </button>
                </form>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </section>

    
    <?php elseif($paciente): ?>

        <section class="vf-panel vf-patient-panel">

            <div class="vf-section-title">
                <div>
                    <h2>Información del paciente</h2>
                    <p>Identificación del paciente encontrado.</p>
                </div>

                <span class="vf-verified">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="m5 12 4 4L19 6"></path>
                    </svg>
                    Paciente encontrado
                </span>
            </div>

            <div class="vf-patient-grid">

                <div class="vf-patient-data">
                    <span>Nombre completo</span>
                    <strong>
                        <?php echo e($paciente->nombre); ?>

                        <?php echo e($paciente->apellido); ?>

                    </strong>
                </div>

                <div class="vf-patient-data">
                    <span>Documento</span>
                    <strong><?php echo e($paciente->documento); ?></strong>
                </div>

                <div class="vf-patient-data">
                    <span>Correo electrónico</span>
                    <strong>
                        <?php echo e($paciente->email ?: 'No registrado'); ?>

                    </strong>
                </div>

                <div class="vf-patient-data">
                    <span>Teléfono</span>
                    <strong>
                        <?php echo e($paciente->telefono ?: 'No registrado'); ?>

                    </strong>
                </div>

            </div>

        </section>

        
        <section class="vf-panel">

            <div class="vf-section-title">
                <div>
                    <h2>Fórmulas médicas</h2>
                    <p>Fórmulas registradas para el paciente.</p>
                </div>

                <span class="vf-count">
                    <?php echo e($formulas->count()); ?>

                    <?php echo e($formulas->count() === 1 ? 'fórmula' : 'fórmulas'); ?>

                </span>
            </div>

            <?php $__empty_1 = true; $__currentLoopData = $formulas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $formula): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                <article class="vf-formula-card">

                    <div class="vf-formula-header">

                        <div class="vf-formula-info">
                            <span class="vf-formula-label">
                                Fórmula médica #<?php echo e($formula->idFormula); ?>

                            </span>

                            <h3>
                                <?php echo e($formula->fechaFormula
                                    ? $formula->fechaFormula->format('d/m/Y')
                                    : 'Fecha no disponible'); ?>

                            </h3>

                            <p>
                                Médico:
                                <strong>
                                    <?php if($formula->doctor): ?>
                                        <?php echo e($formula->doctor->nombre); ?>

                                        <?php echo e($formula->doctor->apellido); ?>

                                    <?php else: ?>
                                        No disponible
                                    <?php endif; ?>
                                </strong>
                            </p>
                        </div>

                        <span class="vf-status
                            <?php echo e(strtolower($formula->estado) === 'activa'
                                ? 'vf-status-active'
                                : 'vf-status-other'); ?>">
                            <?php echo e(ucfirst($formula->estado)); ?>

                        </span>

                    </div>

                    <?php if($formula->observaciones): ?>
                        <div class="vf-observations">
                            <strong>Observaciones generales</strong>
                            <p><?php echo e($formula->observaciones); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="vf-medicines-heading">
                        <h4>Medicamentos prescritos</h4>
                        <span>
                            <?php echo e($formula->medicamentos->count()); ?>

                            <?php echo e($formula->medicamentos->count() === 1
                                ? 'medicamento'
                                : 'medicamentos'); ?>

                        </span>
                    </div>

                    <?php $__empty_2 = true; $__currentLoopData = $formula->medicamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>

                        <div class="vf-medicine-card">

                            <div class="vf-medicine-name">
                                <div class="vf-medicine-icon">
                                    <svg viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor"
                                         stroke-width="1.8"
                                         stroke-linecap="round"
                                         stroke-linejoin="round">
                                        <path d="m10 14 4-4"></path>
                                        <path d="M7 17a5 5 0 0 1 0-7l3-3a5 5 0 0 1 7 7l-3 3a5 5 0 0 1-7 0Z"></path>
                                    </svg>
                                </div>

                                <div>
                                    <span>Medicamento</span>
                                    <strong>
                                        <?php echo e($detalle->medicamento->nombre
                                            ?? 'Medicamento no disponible'); ?>

                                    </strong>
                                </div>
                            </div>

                            <div class="vf-medicine-details">

                                <div>
                                    <span>Dosis</span>
                                    <strong><?php echo e($detalle->dosis); ?></strong>
                                </div>

                                <div>
                                    <span>Frecuencia</span>
                                    <strong><?php echo e($detalle->frecuencia); ?></strong>
                                </div>

                                <div>
                                    <span>Duración</span>
                                    <strong><?php echo e($detalle->duracion); ?></strong>
                                </div>

                                <div>
                                    <span>Cantidad</span>
                                    <strong><?php echo e($detalle->cantidad); ?></strong>
                                </div>

                                <div class="vf-indications">
                                    <span>Indicaciones</span>
                                    <strong>
                                        <?php echo e($detalle->indicaciones
                                            ?: 'Sin indicaciones adicionales'); ?>

                                    </strong>
                                </div>

                            </div>

                        </div>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>

                        <div class="vf-empty-medicines">
                            Esta fórmula no tiene medicamentos registrados.
                        </div>

                    <?php endif; ?>

                </article>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                <div class="vf-empty">
                    <div class="vf-empty-icon">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.7"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path>
                            <path d="M14 2v6h6"></path>
                            <path d="M9 13h6"></path>
                            <path d="M9 17h6"></path>
                        </svg>
                    </div>

                    <h3>Sin fórmulas médicas</h3>

                    <p>
                        El paciente fue encontrado, pero todavía
                        no tiene fórmulas médicas registradas.
                    </p>
                </div>

            <?php endif; ?>

        </section>

    
    <?php else: ?>

        <section class="vf-panel">

            <div class="vf-empty">
                <div class="vf-empty-icon vf-empty-icon-danger">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.7"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m16.5 16.5 4 4"></path>
                        <path d="M8 8l6 6"></path>
                        <path d="m14 8-6 6"></path>
                    </svg>
                </div>

                <h3>Paciente no encontrado</h3>

                <p>
                    No encontramos un paciente con el documento o nombre
                    <strong><?php echo e($busqueda); ?></strong>
                    asociado a tu EPS.
                </p>

                <span class="vf-empty-hint">
                    Verifica los datos e intenta nuevamente.
                </span>
            </div>

        </section>

    <?php endif; ?>

<?php else: ?>

    
    <section class="vf-panel vf-initial-state">

        <div class="vf-empty">
            <div class="vf-empty-icon">
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.7"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 3h6"></path>
                    <path d="M10 3v5l-5 9a3 3 0 0 0 3 4h8a3 3 0 0 0 3-4l-5-9V3"></path>
                    <path d="M8 15h8"></path>
                </svg>
            </div>

            <h3>Consulta de fórmulas médicas</h3>

            <p>
                Busca un paciente por su número de documento,
                nombre o apellido para consultar sus fórmulas médicas.
            </p>
        </div>

    </section>

<?php endif; ?>


    </div>

</main>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/empresa/validar-formulas.blade.php ENDPATH**/ ?>