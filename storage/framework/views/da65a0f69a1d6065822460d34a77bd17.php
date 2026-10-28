<!DOCTYPE html>
<html>
<head>
    <title>Mi perfil</title>
    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/perfil/perfil.css',
        'resources/js/partials.js'
    ]); ?>
</head>
<body>

<?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="content">
    <h1>Mi perfil</h1>

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

    <form action="/perfil" method="POST" class="form-perfil">
        <?php echo csrf_field(); ?>

        <label>Nombre</label>
        <input type="text" name="nombre" value="<?php echo e(old('nombre', $usuario->nombre)); ?>" required>

        <label>Apellido</label>
        <input type="text" name="apellido" value="<?php echo e(old('apellido', $usuario->apellido)); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo e(old('email', $usuario->email)); ?>" required>

        <label>Teléfono</label>
        <input type="text" name="telefono" value="<?php echo e(old('telefono', $usuario->telefono)); ?>" required>

        <label>Dirección</label>
        <input type="text" name="direccion" value="<?php echo e(old('direccion', $usuario->direccion)); ?>" required>

        <label>Documento</label>
        <input type="text" value="<?php echo e($usuario->documento); ?>" disabled>
        <small>El documento no se puede modificar.</small>

        <label>Código de usuario</label>
        <input type="text" value="<?php echo e($usuario->codigo); ?>" disabled>

        <button type="submit">Guardar cambios</button>
    </form>
</div>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/perfil.blade.php ENDPATH**/ ?>