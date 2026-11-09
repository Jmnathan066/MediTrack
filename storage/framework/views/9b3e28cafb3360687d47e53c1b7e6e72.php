
<!DOCTYPE html>
<html>
<head>
    <title>Medicamentos</title>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/medic/medicamento.css',
        'resources/js/partials.js',
        'resources/js/medic/medicamento.js'
    ]); ?>
</head>
<body>

<?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="main-content">

    <div class="container">

        <h1>Listado de Medicamentos</h1>

        <form method="GET" action="/medicamentos" class="busqueda-form">

            <input
                type="text"
                name="busqueda"
                placeholder="Buscar por ID, nombre o categoría"
                value="<?php echo e($busqueda ?? ''); ?>">

            <button type="submit">
                Buscar
            </button>

        </form>

        <br>

        <?php if(in_array(session('tipoUsuario'), ['admin', 'entidad'])): ?>
            <button onclick="window.location.href='/medicamentos/crear'">
                Registrar Medicamento
            </button>
        <?php endif; ?>

        <br><br>

        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Valor</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>

            <?php $__empty_1 = true; $__currentLoopData = $medicamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $medicamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                <tr class="<?php echo e(!$medicamento->estado ? 'medicamento-inactivo' : ''); ?>">

                    <td><?php echo e($medicamento->idMedicamento); ?></td>
                    <td><?php echo e($medicamento->nombre); ?></td>
                    <td><?php echo e($medicamento->categoria); ?></td>
                    <td><?php echo e($medicamento->stock); ?></td>
                    <td>$<?php echo e($medicamento->valor); ?></td>

                    <td>
                        <?php if($medicamento->estado): ?>
                            <span class="estado-medicamento estado-activo">
                                Activo
                            </span>
                        <?php else: ?>
                            <span class="estado-medicamento estado-inactivo">
                                Inactivo
                            </span>
                        <?php endif; ?>
                    </td>

                    <td>

                        <button onclick="toggleDetalle(<?php echo e($medicamento->idMedicamento); ?>)">
                            Ver
                        </button>

                        <?php if(in_array(session('tipoUsuario'), ['admin', 'entidad'])): ?>
                            <button
                                onclick="window.location.href='/medicamentos/editar/<?php echo e($medicamento->idMedicamento); ?>'">
                                Editar
                            </button>
                        <?php endif; ?>

                        <?php if(session('tipoUsuario') === 'admin'): ?>

                            <button
                                class="<?php echo e($medicamento->estado ? 'btn-inactivar' : 'btn-reactivar'); ?>"
                                onclick="window.location.href='/medicamentos/eliminar/<?php echo e($medicamento->idMedicamento); ?>'">

                                <?php echo e($medicamento->estado ? 'Inactivar' : 'Reactivar'); ?>


                            </button>

                        <?php endif; ?>

                    </td>
                </tr>

                <tr id="detalle-<?php echo e($medicamento->idMedicamento); ?>"
                    class="detalle hidden <?php echo e(!$medicamento->estado ? 'medicamento-inactivo' : ''); ?>">

                    <td colspan="7">

                        <strong>Descripción:</strong>
                        <?php echo e($medicamento->descripcion); ?>


                        <br><br>

                        <strong>Fecha de vencimiento:</strong>
                        <?php echo e($medicamento->fechaVencimiento); ?>


                        <br><br>

                        <strong>Stock mínimo:</strong>
                        <?php echo e($medicamento->stockMinimo); ?>


                    </td>

                </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                <tr>
                    <td colspan="7">
                        No existen medicamentos registrados.
                    </td>
                </tr>

            <?php endif; ?>

        </table>

    </div>

</div>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/medicamentos/lista.blade.php ENDPATH**/ ?>