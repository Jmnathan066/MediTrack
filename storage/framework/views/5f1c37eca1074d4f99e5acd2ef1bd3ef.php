
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Órdenes de entrega | MediTrack EPS</title>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/ordenes-entrega.css',
        'resources/js/partials.js'
    ]); ?>
</head>
<body>

<?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="main-content content">
    <?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="oe-container">

        <div class="oe-header">
            <div>
                <h1>Órdenes de entrega</h1>
                <p>Gestiona la entrega de medicamentos prescritos y actualiza el inventario de cada sede.</p>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="oe-alert oe-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="oe-alert oe-error">
                <strong>No se pudo completar la operación.</strong>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="oe-alert oe-error">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        
        <section class="oe-card">
            <div class="oe-section-title">
                <div class="oe-icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <div>
                    <h2>Sede de entrega</h2>
                    <p>Selecciona dónde se realizará la entrega de los medicamentos.</p>
                </div>
            </div>

            <label for="idSede">Sede</label>
            <select id="idSede" class="oe-input">
                <option value="">Selecciona una sede</option>

                <?php $__currentLoopData = $sedes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sede): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($sede->idSede); ?>">
                        <?php echo e($sede->nombre); ?> - <?php echo e($sede->direccion); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <small>El inventario descontado corresponderá a la sede seleccionada.</small>
        </section>

        
        <section class="oe-card">
            <div class="oe-section-title">
                <div class="oe-icon">
                    <i class="fas fa-user-search"></i>
                </div>
                <div>
                    <h2>Buscar paciente</h2>
                    <p>Busca por número de documento, nombre o apellido.</p>
                </div>
            </div>

            <form method="GET" action="<?php echo e(route('empresa.ordenes.index')); ?>" class="oe-search">
                <input
                    type="text"
                    name="busqueda"
                    value="<?php echo e($busqueda); ?>"
                    placeholder="Documento o nombre del paciente..."
                    class="oe-input"
                    required
                >

                <button type="submit" class="oe-button">
                    <i class="fas fa-search"></i>
                    Buscar
                </button>
            </form>

            <?php if($pacientes->isNotEmpty()): ?>
                <div class="oe-results">
                    <h3>Pacientes encontrados</h3>

                    <?php $__currentLoopData = $pacientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resultado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <form method="GET" action="<?php echo e(route('empresa.ordenes.index')); ?>">
                            <input type="hidden" name="busqueda" value="<?php echo e($busqueda); ?>">
                            <input type="hidden" name="paciente_id" value="<?php echo e($resultado->id); ?>">

                            <button type="submit" class="oe-patient-result">
                                <span>
                                    <strong><?php echo e($resultado->nombre); ?> <?php echo e($resultado->apellido); ?></strong>
                                    <small>Documento: <?php echo e($resultado->documento); ?></small>
                                </span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php elseif($busqueda !== '' && !$paciente): ?>
                <div class="oe-empty">
                    No se encontró ningún paciente con esos datos en tu EPS.
                </div>
            <?php endif; ?>

            <?php if($paciente): ?>
                <div class="oe-patient">
                    <div class="oe-patient-avatar">
                        <i class="fas fa-user"></i>
                    </div>

                    <div>
                        <span>Paciente seleccionado</span>
                        <h3><?php echo e($paciente->nombre); ?> <?php echo e($paciente->apellido); ?></h3>
                        <p>Documento: <?php echo e($paciente->documento); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        
        <?php if($paciente): ?>
            <section class="oe-card">
                <div class="oe-section-title">
                    <div class="oe-icon">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <div>
                        <h2>Fórmulas médicas activas</h2>
                        <p>Selecciona la fórmula correspondiente a la entrega.</p>
                    </div>
                </div>

                <?php $__empty_1 = true; $__currentLoopData = $formulas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $formula): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="oe-formula">
                        <div class="oe-formula-info">
                            <div>
                                <strong>Fórmula #<?php echo e($formula->idFormula); ?></strong>
                                <span><?php echo e($formula->fechaFormula); ?></span>
                            </div>

                            <div>
                                <small>Doctor</small>
                                <strong>
                                    <?php echo e($formula->doctor->nombre ?? ''); ?>

                                    <?php echo e($formula->doctor->apellido ?? ''); ?>

                                </strong>
                            </div>

                            <span class="oe-status">Activa</span>
                        </div>

                        <div class="oe-formula-actions">
                            <a
                                href="<?php echo e(route('empresa.ordenes.index', [
                                    'busqueda' => $busqueda,
                                    'paciente_id' => $paciente->id,
                                    'idFormula' => $formula->idFormula
                                ])); ?>"
                                class="oe-button oe-button-secondary"
                            >
                                Ver medicamentos
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="oe-empty">
                        Este paciente no tiene fórmulas médicas activas.
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        
        <?php if($formulaSeleccionada): ?>
            <section class="oe-card">
                <div class="oe-section-title">
                    <div class="oe-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div>
                        <h2>Medicamentos de la fórmula #<?php echo e($formulaSeleccionada->idFormula); ?></h2>
                        <p>Indica cuánto medicamento vas a entregar en esta operación.</p>
                    </div>
                </div>

                <form
                    method="POST"
                    action="<?php echo e(route('empresa.ordenes.guardar')); ?>"
                    id="oe-delivery-form"
                >
                    <?php echo csrf_field(); ?>

                    <input type="hidden" name="idFormula" value="<?php echo e($formulaSeleccionada->idFormula); ?>">
                    <input type="hidden" name="idSede" id="oe-hidden-sede">

                    <div class="oe-medicines">
                        <?php $__currentLoopData = $formulaSeleccionada->medicamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="oe-medicine">
                                <div class="oe-medicine-heading">
                                    <h3><?php echo e($detalle->medicamento->nombre ?? 'Medicamento'); ?></h3>

                                    <?php if($detalle->cantidadPendiente > 0): ?>
                                        <span class="oe-pending">Pendiente</span>
                                    <?php else: ?>
                                        <span class="oe-complete">Entregado</span>
                                    <?php endif; ?>
                                </div>

                                <div class="oe-medicine-info">
                                    <p><strong>Dosis:</strong> <?php echo e($detalle->dosis); ?></p>
                                    <p><strong>Frecuencia:</strong> <?php echo e($detalle->frecuencia); ?></p>
                                    <p><strong>Duración:</strong> <?php echo e($detalle->duracion); ?></p>
                                    <p><strong>Indicaciones:</strong> <?php echo e($detalle->indicaciones ?: 'Sin indicaciones adicionales'); ?></p>
                                </div>

                                <div class="oe-quantity-info">
                                    <div>
                                        <span>Prescrito</span>
                                        <strong><?php echo e($detalle->cantidad); ?></strong>
                                    </div>

                                    <div>
                                        <span>Entregado</span>
                                        <strong><?php echo e($detalle->cantidadEntregada); ?></strong>
                                    </div>

                                    <div>
                                        <span>Pendiente</span>
                                        <strong><?php echo e($detalle->cantidadPendiente); ?></strong>
                                    </div>
                                </div>

                                <?php if($detalle->cantidadPendiente > 0): ?>
                                    <div class="oe-delivery-input">
                                        <label for="cantidad-<?php echo e($detalle->idDetalle); ?>">
                                            Cantidad que entregar ahora
                                        </label>

                                        <input
                                            type="number"
                                            id="cantidad-<?php echo e($detalle->idDetalle); ?>"
                                            name="cantidades[<?php echo e($detalle->idDetalle); ?>]"
                                            class="oe-input"
                                            min="1"
                                            max="<?php echo e($detalle->cantidadPendiente); ?>"
                                            value="<?php echo e(old('cantidades.'.$detalle->idDetalle)); ?>"
                                            placeholder="Máximo <?php echo e($detalle->cantidadPendiente); ?>"
                                        >
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="oe-submit">
                        <button type="submit" class="oe-button" id="oe-submit-button">
                            <i class="fas fa-check-circle"></i>
                            Registrar entrega
                        </button>
                    </div>
                </form>
            </section>
        <?php endif; ?>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sede = document.getElementById('idSede');
    const sedeOculta = document.getElementById('oe-hidden-sede');
    const formulario = document.getElementById('oe-delivery-form');

    function sincronizarSede() {
        if (sede && sedeOculta) {
            sedeOculta.value = sede.value;
        }
    }

    if (sede) {
        sede.addEventListener('change', sincronizarSede);
        sincronizarSede();
    }

    if (formulario) {
        formulario.addEventListener('submit', function (event) {
            sincronizarSede();

            if (!sede || !sede.value) {
                event.preventDefault();
                alert('Selecciona la sede desde donde realizarás la entrega.');
                return;
            }

            const cantidades = formulario.querySelectorAll(
                'input[name^="cantidades["]'
            );

            let hayCantidad = false;

            cantidades.forEach(function (input) {
                if (input.value.trim() !== '' && Number(input.value) > 0) {
                    hayCantidad = true;
                }
            });

            if (!hayCantidad) {
                event.preventDefault();
                alert('Ingresa al menos una cantidad para registrar la entrega.');
            }
        });
    }
});
</script>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/empresa/ordenes-entrega.blade.php ENDPATH**/ ?>