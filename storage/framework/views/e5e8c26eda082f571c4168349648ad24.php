<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de sedes | MediTrack EPS</title>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/sedes.css',
        'resources/js/partials.js'
    ]); ?>
</head>
<body>

    <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="sedes-layout">
        <?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <main class="sedes-main">

            
            <header class="sedes-header">
                <div>
                    <span class="sedes-eyebrow">ADMINISTRACIÓN</span>
                    <h1>Gestión de sedes</h1>
                    <p>
                        Registra, consulta y administra las sedes
                        de MediTrack EPS desde un solo lugar.
                    </p>
                </div>

                <div class="sedes-header-icon">🏥</div>
            </header>

            
            <?php if(session('success')): ?>
                <div class="sedes-alert sedes-alert-success" role="status">
                    <span>✓</span>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            
            <?php if(session('error')): ?>
                <div class="sedes-alert sedes-alert-error" role="alert">
                    <span>!</span>
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            
            <?php if($errors->any()): ?>
                <div class="sedes-alert sedes-alert-error" role="alert">
                    <div>
                        <strong>No se pudo completar la operación.</strong>
                        <ul>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            
            <section class="sedes-stats">

                <div class="sedes-stat-card">
                    <div class="sedes-stat-icon">🏢</div>
                    <div>
                        <span class="sedes-stat-label">Total de sedes</span>
                        <strong><?php echo e($sedes->count()); ?></strong>
                    </div>
                </div>

                <div class="sedes-stat-card">
                    <div class="sedes-stat-icon active-icon">✓</div>
                    <div>
                        <span class="sedes-stat-label">Sedes activas</span>
                        <strong><?php echo e($sedes->where('estado', true)->count()); ?></strong>
                    </div>
                </div>

                <div class="sedes-stat-card">
                    <div class="sedes-stat-icon inactive-icon">○</div>
                    <div>
                        <span class="sedes-stat-label">Sedes inactivas</span>
                        <strong><?php echo e($sedes->where('estado', false)->count()); ?></strong>
                    </div>
                </div>

            </section>

            
            <section class="sedes-content">

                
                <div class="sedes-form-panel">

                    <div class="sedes-section-heading">
                        <div>
                            <h2>
                                <?php echo e($sedeEditar
                                    ? 'Editar sede'
                                    : 'Registrar nueva sede'); ?>

                            </h2>

                            <p>
                                <?php echo e($sedeEditar
                                    ? 'Modifica el nombre o la dirección de esta sede.'
                                    : 'Completa los datos de la sede que deseas agregar.'); ?>

                            </p>
                        </div>

                        <span class="sedes-heading-icon">
                            <?php echo e($sedeEditar ? '✎' : '＋'); ?>

                        </span>
                    </div>

                    <form
                        action="<?php echo e($sedeEditar
                            ? route('sedes.update', $sedeEditar->idSede)
                            : route('sedes.store')); ?>"
                        method="POST"
                    >
                        <?php echo csrf_field(); ?>

                        <?php if($sedeEditar): ?>
                            <?php echo method_field('PUT'); ?>
                        <?php endif; ?>

                        <div class="sedes-field">
                            <label for="nombre">Nombre de la sede</label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="<?php echo e(old('nombre', $sedeEditar?->nombre)); ?>"
                                placeholder="Ej. Sede Yumbo"
                                maxlength="150"
                                required
                            >

                            <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="sedes-field-error">
                                    <?php echo e($message); ?>

                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="sedes-field">
                            <label for="direccion">Dirección</label>

                            <input
                                type="text"
                                id="direccion"
                                name="direccion"
                                value="<?php echo e(old('direccion', $sedeEditar?->direccion)); ?>"
                                placeholder="Ej. Calle 10 # 5-20"
                                maxlength="255"
                                required
                            >

                            <?php $__errorArgs = ['direccion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="sedes-field-error">
                                    <?php echo e($message); ?>

                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <button type="submit" class="sedes-submit">
                            <?php echo e($sedeEditar
                                ? 'Guardar cambios'
                                : '＋ Registrar sede'); ?>

                        </button>

                        <?php if($sedeEditar): ?>
                            <a
                                href="<?php echo e(route('sedes.index')); ?>"
                                class="sedes-cancel-link"
                            >
                                Cancelar edición
                            </a>
                        <?php endif; ?>

                    </form>

                    <div class="sedes-form-note">
                        <span>ℹ</span>
                        <p>
                            Cada sede tiene su propio inventario de medicamentos.
                        </p>
                    </div>

                </div>

                
                <div class="sedes-list-panel">

                    <div class="sedes-section-heading">
                        <div>
                            <h2>Sedes registradas</h2>
                            <p>
                                Consulta y administra las ubicaciones
                                disponibles en el sistema.
                            </p>
                        </div>

                        <span class="sedes-count">
                            <?php echo e($sedes->count()); ?>

                        </span>
                    </div>

                    <?php if($sedes->isNotEmpty()): ?>

                        <div class="sedes-table-wrapper">
                            <table class="sedes-table">

                                <thead>
                                    <tr>
                                        <th>Sede</th>
                                        <th>Dirección</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php $__currentLoopData = $sedes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sede): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>

                                            
                                            <td>
                                                <div class="sedes-name-cell">
                                                    <span class="sedes-building">🏥</span>

                                                    <div>
                                                        <strong>
                                                            <?php echo e($sede->nombre); ?>

                                                        </strong>

                                                        <small>
                                                            Código #<?php echo e($sede->idSede); ?>

                                                        </small>
                                                    </div>
                                                </div>
                                            </td>

                                            
                                            <td>
                                                <?php echo e($sede->direccion); ?>

                                            </td>

                                            
                                            <td>
                                                <?php if($sede->estado): ?>
                                                    <span class="sedes-status status-active">
                                                        Activa
                                                    </span>
                                                <?php else: ?>
                                                    <span class="sedes-status status-inactive">
                                                        Inactiva
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            
                                            <td>
                                                <div class="sedes-actions">

                                                    
                                                    <a
                                                        href="<?php echo e(route('sedes.edit', $sede->idSede)); ?>"
                                                        class="sedes-action-btn"
                                                    >
                                                        Editar
                                                    </a>

                                                    
                                                    <form
                                                        action="<?php echo e(route('sedes.estado', $sede->idSede)); ?>"
                                                        method="POST"
                                                        onsubmit="return confirm('¿Deseas <?php echo e($sede->estado ? 'inactivar' : 'activar'); ?> esta sede?');"
                                                    >
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>

                                                        <button
                                                            type="submit"
                                                            class="sedes-action-btn"
                                                        >
                                                            <?php echo e($sede->estado
                                                                ? 'Inactivar'
                                                                : 'Activar'); ?>

                                                        </button>
                                                    </form>

                                                    
                                                    <form
                                                        action="<?php echo e(route('sedes.destroy', $sede->idSede)); ?>"
                                                        method="POST"
                                                        onsubmit="return confirm('¿Estás seguro de eliminar esta sede? Esta acción no se puede deshacer.');"
                                                    >
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>

                                                        <button
                                                            type="submit"
                                                            class="sedes-action-btn sedes-action-delete"
                                                        >
                                                            Eliminar
                                                        </button>
                                                    </form>

                                                </div>
                                            </td>

                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>

                            </table>
                        </div>

                    <?php else: ?>

                        <div class="sedes-empty">
                            <div class="sedes-empty-icon">🏢</div>

                            <h3>Aún no hay sedes registradas</h3>

                            <p>
                                Registra la primera sede usando el formulario
                                para comenzar a organizar los inventarios.
                            </p>
                        </div>

                    <?php endif; ?>

                </div>

            </section>

            
            <footer class="sedes-footer">
                MediTrack EPS · Administración de sedes
            </footer>

        </main>
    </div>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/sedes/sedes.blade.php ENDPATH**/ ?>