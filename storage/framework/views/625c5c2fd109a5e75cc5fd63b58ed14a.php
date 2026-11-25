
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de EPS | MediTrack EPS</title>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/eps.css',
        'resources/js/partials.js'
    ]); ?>
</head>
<body>

    <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="eps-layout">
        <div class="eps-header">
            <div>
                <h1>Gestión de EPS</h1>
                <p>Administra las entidades promotoras de salud registradas en MediTrack.</p>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="eps-alert eps-alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="eps-alert eps-alert-error">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="eps-alert eps-alert-error">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="eps-stats">
            <div class="eps-stat-card">
                <div class="eps-stat-info">
                    <span>Total de EPS</span>
                    <h2><?php echo e($eps->count()); ?></h2>
                </div>
            </div>

            <div class="eps-stat-card">
                <div class="eps-stat-info">
                    <span>EPS activas</span>
                    <h2><?php echo e($eps->where('estado', true)->count()); ?></h2>
                </div>
            </div>

            <div class="eps-stat-card">
                <div class="eps-stat-info">
                    <span>EPS inactivas</span>
                    <h2><?php echo e($eps->where('estado', false)->count()); ?></h2>
                </div>
            </div>
        </section>

        <section class="eps-content">

            <div class="eps-form-container">
                <div class="eps-section-header">
                    <h2><?php echo e($epsEditar ? 'Editar EPS' : 'Registrar EPS'); ?></h2>
                    <p>
                        <?php echo e($epsEditar
                            ? 'Actualiza la información de la entidad.'
                            : 'Completa los datos para registrar una nueva entidad.'); ?>

                    </p>
                </div>

                <form
                    action="<?php echo e($epsEditar ? route('eps.update', $epsEditar->idEPS) : route('eps.store')); ?>"
                    method="POST"
                    class="eps-form"
                >
                    <?php echo csrf_field(); ?>

                    <?php if($epsEditar): ?>
                        <?php echo method_field('PUT'); ?>
                    <?php endif; ?>

                    <div class="eps-form-group">
                        <label for="nombre">Nombre de la EPS</label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="<?php echo e(old('nombre', $epsEditar->nombre ?? '')); ?>"
                            placeholder="Ej. Nombre de la entidad"
                            maxlength="150"
                            required
                        >
                    </div>

                    <div class="eps-form-group">
                        <label for="nit">NIT</label>
                        <input
                            type="text"
                            id="nit"
                            name="nit"
                            value="<?php echo e(old('nit', $epsEditar->nit ?? '')); ?>"
                            placeholder="Ingrese el NIT"
                            maxlength="30"
                            required
                        >
                    </div>

                    <div class="eps-form-actions">
                        <button type="submit" class="eps-btn eps-btn-primary">
                            <?php echo e($epsEditar ? 'Guardar cambios' : 'Registrar EPS'); ?>

                        </button>

                        <?php if($epsEditar): ?>
                            <a href="<?php echo e(route('eps.index')); ?>" class="eps-btn eps-btn-secondary">
                                Cancelar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="eps-table-container">
                <div class="eps-section-header">
                    <h2>EPS registradas</h2>
                    <p>Consulta y administra las entidades existentes.</p>
                </div>

                <div class="eps-table-wrapper">
                    <table class="eps-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>NIT</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $eps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($entidad->idEPS); ?></td>
                                    <td><?php echo e($entidad->nombre); ?></td>
                                    <td><?php echo e($entidad->nit); ?></td>
                                    <td>
                                        <?php if($entidad->estado): ?>
                                            <span class="status-active">Activa</span>
                                        <?php else: ?>
                                            <span class="status-inactive">Inactiva</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="eps-actions">
                                            <a
                                                href="<?php echo e(route('eps.edit', $entidad->idEPS)); ?>"
                                                class="eps-btn eps-btn-edit"
                                            >
                                                Editar
                                            </a>

                                            <form
                                                action="<?php echo e(route('eps.estado', $entidad->idEPS)); ?>"
                                                method="POST"
                                                onsubmit="return confirm('¿Estás seguro de cambiar el estado de esta EPS?');"
                                            >
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>

                                                <button
                                                    type="submit"
                                                    class="eps-btn <?php echo e($entidad->estado ? 'eps-btn-danger' : 'eps-btn-success'); ?>"
                                                >
                                                    <?php echo e($entidad->estado ? 'Inactivar' : 'Activar'); ?>

                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="eps-empty">
                                        No hay EPS registradas todavía.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
    </main>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/eps/eps.blade.php ENDPATH**/ ?>