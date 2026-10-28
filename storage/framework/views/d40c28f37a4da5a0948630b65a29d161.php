<?php
    $sedesDisponibles = \App\Models\Sede::where('estado', true)
        ->orderBy('nombre')
        ->get();

    $sedeSeleccionada = session(
        'idSede',
        $sedesDisponibles->first()?->idSede
    );
?>

<div class="topbar">
    <div class="logo">MediTrack EPS</div>

    <div class="profile-container">

        <form
            action="<?php echo e(route('sedes.cambiar')); ?>"
            method="POST"
            class="sede-selector"
        >
            <?php echo csrf_field(); ?>

            <label for="idSede">Sede:</label>

            <select
                name="idSede"
                id="idSede"
                onchange="this.form.submit()"
                aria-label="Seleccionar sede"
            >
                <?php $__empty_1 = true; $__currentLoopData = $sedesDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sede): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <option
                        value="<?php echo e($sede->idSede); ?>"
                        <?php if($sedeSeleccionada == $sede->idSede): echo 'selected'; endif; ?>
                    >
                        <?php echo e($sede->nombre); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <option value="">No hay sedes disponibles</option>
                <?php endif; ?>
            </select>
        </form>

        <button
            type="button"
            class="profile"
            onclick="toggleMenu()"
            aria-label="Abrir menú de usuario"
            aria-expanded="false"
            id="profileButton"
        >
            <?php echo e(session('nombre')[0] ?? 'U'); ?>

        </button>

        <div id="menu" class="dropdown hidden">
            <div class="dropdown-header">
                <div class="avatar-large">
                    <?php echo e(session('nombre')[0] ?? 'U'); ?>

                </div>

                <p>
                    <?php echo e(session('nombre')); ?> <?php echo e(session('apellido')); ?>

                </p>

                <p>Rol: <?php echo e(session('tipoUsuario')); ?></p>
            </div>

            <a href="/perfil" class="dropdown-link profile-menu-btn">
                <span>👤</span>
                <span>Mi perfil</span>
            </a>

            <div class="theme-option">
                <span id="themeLabel">Modo oscuro</span>

                <button
                    type="button"
                    id="themeToggle"
                    class="theme-toggle"
                    role="switch"
                    aria-checked="false"
                    aria-label="Activar o desactivar modo oscuro"
                >
                    <span class="theme-toggle-knob"></span>
                </button>
            </div>

            <a href="/logout" class="logout-btn">Cerrar sesión</a>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\davim\medicine\resources\views/partials/topbar.blade.php ENDPATH**/ ?>