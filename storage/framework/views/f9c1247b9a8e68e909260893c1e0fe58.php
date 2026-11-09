
<!DOCTYPE html>
<html>
<head>
    <title>Cambiar contraseña</title>
    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/perfil/password.css',
        'resources/js/partials.js',
        'resources/js/password.js'
    ]); ?>
</head>
<body>

<?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="content">
    <h1>Cambiar contraseña</h1>

    <?php if(session('success')): ?>
        <div class="alerta-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alerta-error">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/perfil/password" method="POST" class="form-password">
        <?php echo csrf_field(); ?>

        <label>Contraseña actual</label>
        <div class="password-wrapper">
            <input type="password" name="password_actual" required>
            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m3 3 18 18"/>
                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                    <path d="M9.9 5.2A11 11 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8"/>
                    <path d="M6.6 6.6C3.6 8.5 2 12 2 12s3.6 7 10 7c1.4 0 2.7-.3 3.8-.8"/>
                </svg>
            </button>
        </div>

        <label>Nueva contraseña</label>
        <div class="password-wrapper">
            <input type="password" name="password_nueva" required minlength="6">
            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m3 3 18 18"/>
                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                    <path d="M9.9 5.2A11 11 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8"/>
                    <path d="M6.6 6.6C3.6 8.5 2 12 2 12s3.6 7 10 7c1.4 0 2.7-.3 3.8-.8"/>
                </svg>
            </button>
        </div>
        <small>Mínimo 6 caracteres, con al menos una letra y un número.</small>

        <label>Confirmar nueva contraseña</label>
        <div class="password-wrapper">
            <input type="password" name="password_nueva_confirmation" required minlength="6">
            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m3 3 18 18"/>
                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                    <path d="M9.9 5.2A11 11 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8"/>
                    <path d="M6.6 6.6C3.6 8.5 2 12 2 12s3.6 7 10 7c1.4 0 2.7-.3 3.8-.8"/>
                </svg>
            </button>
        </div>

        <button type="submit">Actualizar contraseña</button>
    </form>
</div>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/cambiar-password.blade.php ENDPATH**/ ?>