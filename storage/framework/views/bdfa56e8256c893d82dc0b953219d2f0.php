
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Medicamento | MediTrack EPS</title>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/medic/medicamento.css',
        'resources/js/partials.js',
        'resources/js/medic/medicamento.js'
    ]); ?>
</head>
<body class="medicamento-page">

    <?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content medicamento-main">
        <div class="container">

            <h1>Registrar Medicamento</h1>

            <?php if($errors->any()): ?>
                <div class="error">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('medicamentos.guardar')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <label for="nombre">Nombre</label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    placeholder="Ej. Acetaminofén"
                    value="<?php echo e(old('nombre')); ?>"
                    required
                >

                <label for="categoria">Categoría</label>
                <input
                    type="text"
                    id="categoria"
                    name="categoria"
                    placeholder="Ej. Analgésicos"
                    value="<?php echo e(old('categoria')); ?>"
                    required
                >

                <label for="descripcion">Descripción</label>
                <textarea
                    id="descripcion"
                    name="descripcion"
                    placeholder="Describe el medicamento, su uso o sus características..."
                ><?php echo e(old('descripcion')); ?></textarea>

                <label for="fechaVencimiento">Fecha de vencimiento</label>
                <input
                    type="date"
                    id="fechaVencimiento"
                    name="fechaVencimiento"
                    value="<?php echo e(old('fechaVencimiento')); ?>"
                    required
                >

                <label for="stock">Stock</label>
                <input
                    type="number"
                    id="stock"
                    name="stock"
                    placeholder="Ej. 100"
                    value="<?php echo e(old('stock')); ?>"
                    min="0"
                    required
                >

                <label for="stockMinimo">Stock mínimo</label>
                <input
                    type="number"
                    id="stockMinimo"
                    name="stockMinimo"
                    placeholder="Ej. 10"
                    value="<?php echo e(old('stockMinimo')); ?>"
                    min="0"
                    required
                >

                <label for="valor">Valor</label>
                <input
                    type="number"
                    id="valor"
                    name="valor"
                    placeholder="Ej. 2500.00"
                    value="<?php echo e(old('valor')); ?>"
                    step="0.01"
                    min="0"
                    required
                >

                <div class="acciones">
                    <button type="submit">Guardar</button>

                    <button
                        type="button"
                        onclick="window.location.href='/medicamentos'"
                    >
                        Volver
                    </button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/medicamentos/crear.blade.php ENDPATH**/ ?>