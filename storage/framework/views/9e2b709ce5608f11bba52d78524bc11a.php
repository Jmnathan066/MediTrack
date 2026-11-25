
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mis reclamos | MediTrack EPS</title>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/reclamos.css',
        'resources/js/partials.js'
    ]); ?>
</head>
<body>

    <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="main-content">
        <div class="reclamos-container">

            <div class="reclamos-header">
                <div>
                    <h1>Reclamos de medicamentos</h1>
                    <p>Registra un reclamo sobre un medicamento que hayas recibido.</p>
                </div>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-error">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <section class="reclamo-card">
                <h2>Registrar un nuevo reclamo</h2>

                <?php if($entregas->isEmpty()): ?>
                    <div class="empty-state">
                        <p>Aún no tienes medicamentos entregados para registrar un reclamo.</p>
                    </div>
                <?php else: ?>
                    <form action="<?php echo e(route('paciente.reclamos.guardar')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="form-group">
                            <label for="idDetalleOrden">Medicamento recibido</label>

                            <select name="idDetalleOrden" id="idDetalleOrden" required>
                                <option value="">Selecciona un medicamento</option>

                                <?php $__currentLoopData = $entregas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entrega): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option
                                        value="<?php echo e($entrega->idDetalleOrden); ?>"
                                        <?php echo e(old('idDetalleOrden') == $entrega->idDetalleOrden ? 'selected' : ''); ?>

                                    >
                                        <?php echo e($entrega->detalleFormula?->medicamento?->nombre ?? 'Medicamento'); ?>

                                        — Cantidad recibida: <?php echo e($entrega->cantidadEntregada); ?>

                                        — Entrega #<?php echo e($entrega->idDetalleOrden); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="motivo">Motivo del reclamo</label>

                            <select name="motivo" id="motivo" required>
                                <option value="">Selecciona el motivo</option>
                                <option value="Medicamento incorrecto" <?php echo e(old('motivo') == 'Medicamento incorrecto' ? 'selected' : ''); ?>>Medicamento incorrecto</option>
                                <option value="Cantidad incompleta" <?php echo e(old('motivo') == 'Cantidad incompleta' ? 'selected' : ''); ?>>Cantidad incompleta</option>
                                <option value="Medicamento vencido" <?php echo e(old('motivo') == 'Medicamento vencido' ? 'selected' : ''); ?>>Medicamento vencido</option>
                                <option value="Medicamento en mal estado" <?php echo e(old('motivo') == 'Medicamento en mal estado' ? 'selected' : ''); ?>>Medicamento en mal estado</option>
                                <option value="Problemas con el empaque" <?php echo e(old('motivo') == 'Problemas con el empaque' ? 'selected' : ''); ?>>Problemas con el empaque</option>
                                <option value="Otro" <?php echo e(old('motivo') == 'Otro' ? 'selected' : ''); ?>>Otro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción del problema</label>

                            <textarea
                                name="descripcion"
                                id="descripcion"
                                rows="5"
                                maxlength="2000"
                                placeholder="Describe detalladamente lo sucedido..."
                                required
                            ><?php echo e(old('descripcion')); ?></textarea>
                        </div>

                        <button type="submit" class="btn-reclamo">
                            Registrar reclamo
                        </button>
                    </form>
                <?php endif; ?>
            </section>

            <section class="reclamos-historial">
                <h2>Historial de mis reclamos</h2>

                <?php $__empty_1 = true; $__currentLoopData = $reclamos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reclamo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <article class="reclamo-item">
                        <div class="reclamo-item-header">
                            <div>
                                <h3><?php echo e($reclamo->detalleOrden?->detalleFormula?->medicamento?->nombre ?? 'Medicamento'); ?></h3>
                                <span><?php echo e($reclamo->created_at?->format('d/m/Y H:i')); ?></span>
                            </div>

                            <span class="estado-reclamo estado-<?php echo e($reclamo->estado); ?>">
                                <?php echo e(ucfirst($reclamo->estado)); ?>

                            </span>
                        </div>

                        <p><strong>Motivo:</strong> <?php echo e($reclamo->motivo); ?></p>
                        <p><strong>Descripción:</strong> <?php echo e($reclamo->descripcion); ?></p>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state">
                        <p>Todavía no has registrado reclamos.</p>
                    </div>
                <?php endif; ?>
            </section>

        </div>
    </main>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/paciente/reclamos.blade.php ENDPATH**/ ?>