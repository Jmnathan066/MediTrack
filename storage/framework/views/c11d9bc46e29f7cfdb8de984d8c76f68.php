
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Medicamento | MediTrack EPS</title>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/medic/medicamento.css',
        'resources/js/partials.js'
    ]); ?>
</head>
<body class="medicamento-page">

    <?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content medicamento-main">
        <div class="container">

            <h1>Eliminar Medicamento</h1>

            <p>Está a punto de eliminar:</p>

            <p>
                <strong><?php echo e($medicamento->nombre); ?></strong>
            </p>

            <p>
                ID:
                <strong><?php echo e($medicamento->idMedicamento); ?></strong>
            </p>

            <p>Para confirmar, escriba el ID del medicamento.</p>

            <?php if($errors->any()): ?>
                <div class="error">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form
                action="/medicamentos/eliminar/<?php echo e($medicamento->idMedicamento); ?>"
                method="POST">

                <?php echo csrf_field(); ?>

                <label for="confirmacion">ID de confirmación</label>
                <input
                    type="number"
                    id="confirmacion"
                    name="confirmacion"
                    placeholder="Ingrese el ID"
                    required>

                <div class="acciones">
                    <button type="button"
                            onclick="window.location.href='/medicamentos'">
                        Cancelar
                    </button>

                    <button type="submit">Eliminar</button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/medicamentos/eliminar.blade.php ENDPATH**/ ?>