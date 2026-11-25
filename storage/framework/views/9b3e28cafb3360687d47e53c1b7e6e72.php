
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos | MediTrack EPS</title>

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

            <?php if(session('success')): ?>
                <div class="success">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="error">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form method="GET" action="/medicamentos" class="busqueda-form">
                <input
                    type="text"
                    name="busqueda"
                    placeholder="Buscar por ID, nombre o categoría"
                    value="<?php echo e($busqueda ?? ''); ?>"
                >

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

            <?php
                $tipoUsuario = session('tipoUsuario');
                $esPaciente = $tipoUsuario === 'paciente';
                $puedeGestionar = in_array($tipoUsuario, ['admin', 'entidad']);
                $totalColumnas = $esPaciente ? 6 : 7;
            ?>

            <table border="1">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Valor</th>

                        <?php if(!$esPaciente): ?>
                            <th>Estado</th>
                        <?php endif; ?>

                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php $__empty_1 = true; $__currentLoopData = $medicamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $medicamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <?php
                            $inventario = $medicamento->inventarios->first();

                            $estadoActivo = $inventario
                                ? $inventario->estado
                                : false;

                            $stockTotal = $inventario
                                ? $inventario->lotes
                                    ->where('estado', true)
                                    ->sum('stockDisponible')
                                : 0;

                            $stockMinimo = $inventario
                                ? $inventario->stockMinimo
                                : 0;
                        ?>

                        <tr class="<?php echo e(!$estadoActivo ? 'medicamento-inactivo' : ''); ?>">

                            <td><?php echo e($medicamento->idMedicamento); ?></td>

                            <td><?php echo e($medicamento->nombre); ?></td>

                            <td><?php echo e($medicamento->categoria); ?></td>

                            <td><?php echo e($stockTotal); ?></td>

                            <td>$<?php echo e(number_format($medicamento->valor, 2, ',', '.')); ?></td>

                            <?php if(!$esPaciente): ?>
                                <td>
                                    <?php if($estadoActivo): ?>
                                        <span class="estado-medicamento estado-activo">
                                            Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="estado-medicamento estado-inactivo">
                                            Inactivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>

                            <td>

                                
                                <button
                                    type="button"
                                    onclick="toggleDetalle(<?php echo e($medicamento->idMedicamento); ?>)"
                                >
                                    Ver
                                </button>

                                
                                <?php if($puedeGestionar): ?>
                                    <button
                                        type="button"
                                        onclick="window.location.href='/medicamentos/editar/<?php echo e($medicamento->idMedicamento); ?>'"
                                    >
                                        Editar
                                    </button>
                                <?php endif; ?>

                                
                                <?php if($puedeGestionar): ?>
                                    <button
                                        type="button"
                                        class="btn-lotes"
                                        onclick="toggleAccionesLotes(<?php echo e($medicamento->idMedicamento); ?>)"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="m12 2 9 5-9 5-9-5 9-5Z"/>
                                            <path d="m3 12 9 5 9-5"/>
                                            <path d="m3 17 9 5 9-5"/>
                                        </svg>
                                        Lotes
                                    </button>
                                <?php endif; ?>

                                
                                <?php if($tipoUsuario === 'admin'): ?>
                                    <button
                                        type="button"
                                        class="<?php echo e($estadoActivo ? 'btn-inactivar' : 'btn-reactivar'); ?>"
                                        onclick="window.location.href='/medicamentos/eliminar/<?php echo e($medicamento->idMedicamento); ?>'"
                                    >
                                        <?php echo e($estadoActivo ? 'Inactivar' : 'Reactivar'); ?>

                                    </button>
                                <?php endif; ?>

                            </td>

                        </tr>

                        
                        <tr
                            id="detalle-<?php echo e($medicamento->idMedicamento); ?>"
                            class="detalle hidden <?php echo e(!$estadoActivo ? 'medicamento-inactivo' : ''); ?>"
                        >

                            <td colspan="<?php echo e($totalColumnas); ?>">

                                <strong>Descripción:</strong>
                                <?php echo e($medicamento->descripcion); ?>


                                <br><br>

                                <strong>Stock mínimo:</strong>
                                <?php echo e($stockMinimo); ?>


                                <br><br>

                                <strong>Stock total en esta sede:</strong>
                                <?php echo e($stockTotal); ?>


                                <?php if(!$esPaciente): ?>
                                    <br><br>

                                    <strong>Estado del inventario:</strong>

                                    <?php if($estadoActivo): ?>
                                        <span class="estado-medicamento estado-activo">
                                            Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="estado-medicamento estado-inactivo">
                                            Inactivo
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>

                            </td>

                        </tr>

                        
                        <?php if($puedeGestionar): ?>

                            <tr
                                id="acciones-lotes-<?php echo e($medicamento->idMedicamento); ?>"
                                class="detalle hidden"
                            >

                                <td colspan="<?php echo e($totalColumnas); ?>">

                                    <strong>
                                        Lotes registrados de <?php echo e($medicamento->nombre); ?>:
                                    </strong>

                                    <?php if($inventario && $inventario->lotes->count() > 0): ?>

                                        <table border="1" width="100%">

                                            <thead>
                                                <tr>
                                                    <th>Código del lote</th>
                                                    <th>Fecha de ingreso</th>
                                                    <th>Fecha de vencimiento</th>
                                                    <th>Cantidad inicial</th>
                                                    <th>Stock disponible</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                <?php $__currentLoopData = $inventario->lotes->sortBy('fechaVencimiento'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                    <tr class="<?php echo e(!$lote->estado ? 'medicamento-inactivo' : ''); ?>">

                                                        <td><?php echo e($lote->codigoLote); ?></td>

                                                        <td>
                                                            <?php echo e(\Carbon\Carbon::parse($lote->fechaIngreso)->format('d/m/Y')); ?>

                                                        </td>

                                                        <td>
                                                            <?php echo e(\Carbon\Carbon::parse($lote->fechaVencimiento)->format('d/m/Y')); ?>

                                                        </td>

                                                        <td><?php echo e($lote->cantidadInicial); ?></td>

                                                        <td>
                                                            <?php echo e($lote->estado ? $lote->stockDisponible : 0); ?>

                                                        </td>

                                                        <td>
                                                            <?php if($lote->estado): ?>
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
                                                            <form
                                                                method="POST"
                                                                action="<?php echo e(route('medicamentos.lotes.estado', [
                                                                    'idMedicamento' => $medicamento->idMedicamento,
                                                                    'idLote' => $lote->idLote
                                                                ])); ?>"
                                                                onsubmit="return confirm('¿Estás seguro de que deseas <?php echo e($lote->estado ? 'inactivar' : 'reactivar'); ?> este lote?');"
                                                            >
                                                                <?php echo csrf_field(); ?>

                                                                <button
                                                                    type="submit"
                                                                    class="<?php echo e($lote->estado ? 'btn-inactivar' : 'btn-reactivar'); ?>"
                                                                >
                                                                    <?php echo e($lote->estado ? 'Inactivar' : 'Reactivar'); ?>

                                                                </button>
                                                            </form>
                                                        </td>

                                                    </tr>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                            </tbody>

                                        </table>

                                    <?php else: ?>

                                        <p>No hay lotes registrados para este medicamento.</p>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endif; ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                        <tr>
                            <td colspan="<?php echo e($totalColumnas); ?>">
                                No existen medicamentos registrados en esta sede.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>
    </div>

    <script>
        function toggleAccionesLotes(id) {
            const fila = document.getElementById('acciones-lotes-' + id);

            if (fila) {
                fila.classList.toggle('hidden');
            }
        }
    </script>

</body>

</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/medicamentos/lista.blade.php ENDPATH**/ ?>