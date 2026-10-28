<!DOCTYPE html>
<html>
<head>
    <title>Cambiar contraseña</title>
      <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/perfil/password.css',
        'resources/js/partials.js'
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
        <input type="password" name="password_actual" required>

        <label>Nueva contraseña</label>
        <input type="password" name="password_nueva" required minlength="6">
        <small>Mínimo 6 caracteres, con al menos una letra y un número.</small>

        <label>Confirmar nueva contraseña</label>
        <input type="password" name="password_nueva_confirmation" required minlength="6">

        <button type="submit">Actualizar contraseña</button>
    </form>
</div>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/cambiar-password.blade.php ENDPATH**/ ?>