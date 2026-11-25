
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

            <form
                action="<?php echo e(route('medicamentos.guardar')); ?>"
                method="POST"
                id="form-medicamento"
                data-url-deteccion="<?php echo e(route('medicamentos.detectar')); ?>"
            >
                <?php echo csrf_field(); ?>

                <h2>Información del medicamento</h2>

                <label for="nombre">Nombre</label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    placeholder="Ej. Acetaminofén 500mg"
                    value="<?php echo e(old('nombre')); ?>"
                    autocomplete="off"
                    required
                >

                <div
                    id="mensaje-deteccion"
                    role="status"
                    aria-live="polite"
                    hidden
                ></div>

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
                    required
                ><?php echo e(old('descripcion')); ?></textarea>

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

                <hr>

                <h2>Información del lote</h2>

                <label for="codigoLote">Código del lote</label>
                <input
                    type="text"
                    id="codigoLote"
                    name="codigoLote"
                    placeholder="Ej. LOT-2026-001"
                    value="<?php echo e(old('codigoLote')); ?>"
                    required
                >

                <label for="fechaIngreso">Fecha de ingreso</label>
                <input
                    type="date"
                    id="fechaIngreso"
                    name="fechaIngreso"
                    value="<?php echo e(old('fechaIngreso', date('Y-m-d'))); ?>"
                    required
                >

                <label for="fechaVencimiento">Fecha de vencimiento</label>
                <input
                    type="date"
                    id="fechaVencimiento"
                    name="fechaVencimiento"
                    value="<?php echo e(old('fechaVencimiento')); ?>"
                    required
                >

                <label for="cantidad">Cantidad recibida</label>
                <input
                    type="number"
                    id="cantidad"
                    name="cantidad"
                    placeholder="Ej. 100"
                    value="<?php echo e(old('cantidad')); ?>"
                    min="0"
                    required
                >

                <label for="stockMinimo">Stock mínimo</label>
                <input
                    type="number"
                    id="stockMinimo"
                    name="stockMinimo"
                    placeholder="Ej. 10"
                    value="<?php echo e(old('stockMinimo', 10)); ?>"
                    min="0"
                    required
                >

                <div class="acciones">
                    <button type="submit">Guardar medicamento</button>

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