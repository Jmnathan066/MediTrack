
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Medicamento | MediTrack EPS</title>

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

            <h1>Editar Medicamento</h1>

            <?php if($errors->any()): ?>
                <div class="error">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form
                action="/medicamentos/actualizar/<?php echo e($medicamento->idMedicamento); ?>"
                method="POST">

                <?php echo csrf_field(); ?>

                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre"
                       value="<?php echo e(old('nombre', $medicamento->nombre)); ?>" required>

                <label for="categoria">Categoría</label>
                <input type="text" id="categoria" name="categoria"
                       value="<?php echo e(old('categoria', $medicamento->categoria)); ?>" required>

                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion"><?php echo e(old('descripcion', $medicamento->descripcion)); ?></textarea>

                <label for="fechaVencimiento">Fecha de vencimiento</label>
                <input type="date" id="fechaVencimiento"
                       name="fechaVencimiento"
                       value="<?php echo e(old('fechaVencimiento', $medicamento->fechaVencimiento)); ?>"
                       required>

                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock"
                       value="<?php echo e(old('stock', $medicamento->stock)); ?>"
                       min="0" required>

                <label for="stockMinimo">Stock mínimo</label>
                <input type="number" id="stockMinimo" name="stockMinimo"
                       value="<?php echo e(old('stockMinimo', $medicamento->stockMinimo)); ?>"
                       min="0" required>

                <label for="valor">Valor</label>
                <input type="number" id="valor" name="valor"
                       value="<?php echo e(old('valor', $medicamento->valor)); ?>"
                       step="0.01" min="0" required>

                <div class="acciones">
                    <button type="submit">Guardar Cambios</button>

                    <button type="button"
                            onclick="window.location.href='/medicamentos'">
                        Volver
                    </button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/medicamentos/editar.blade.php ENDPATH**/ ?>