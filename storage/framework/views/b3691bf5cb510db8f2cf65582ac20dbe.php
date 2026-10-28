<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de pacientes | MediTrack EPS</title>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/pacientes.css',
        'resources/js/partials.js'
    ]); ?>
</head>
<body>
    <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="pacientes-page">
        <header class="pacientes-header">
            <div>
                <h1>Consulta de pacientes</h1>
                <p>Consulta y encuentra pacientes registrados en MediTrack EPS.</p>
            </div>
        </header>

        
        <section class="pacientes-resumen">
            <div class="resumen-card">
                <div class="resumen-icono">👥</div>
                <div>
                    <span>Total de pacientes</span>
                    <strong><?php echo e($pacientes->total()); ?></strong>
                    <small>Usuarios con rol paciente</small>
                </div>
            </div>
        </section>

        
        <section class="pacientes-panel">
            <div class="panel-heading">
                <h2>Buscar pacientes</h2>
                <p>Busca por nombre, apellido o número de documento.</p>
            </div>

            <form action="<?php echo e(route('pacientes.index')); ?>" method="GET"
                  class="pacientes-search">

                <div class="campo-busqueda">
                    <label for="busqueda">Nombre o documento del paciente</label>
                    <input
                        type="text"
                        id="busqueda"
                        name="busqueda"
                        value="<?php echo e($busqueda); ?>"
                        placeholder="Ej. María García o número de documento"
                    >
                </div>

                <div class="campo-estado">
                    <label for="estado">Estado de la cuenta</label>
                    <select id="estado" name="estado">
                        <option value="todos" <?php echo e($estado === 'todos' ? 'selected' : ''); ?>>
                            Todos los pacientes
                        </option>
                        <option value="activos" <?php echo e($estado === 'activos' ? 'selected' : ''); ?>>
                            Solo cuentas activas
                        </option>
                        <option value="inactivos" <?php echo e($estado === 'inactivos' ? 'selected' : ''); ?>>
                            Solo cuentas desactivadas
                        </option>
                    </select>
                </div>

                <div class="acciones-busqueda">
                    <button type="submit" class="btn-buscar">Buscar</button>
                    <a href="<?php echo e(route('pacientes.index')); ?>" class="btn-limpiar">
                        Ver todos
                    </a>
                </div>
            </form>
        </section>

        
        <section class="pacientes-panel resultados-panel">
            <div class="resultados-header">
                <div>
                    <h2><?php echo e($busqueda !== '' ? 'Resultados de búsqueda' : 'Pacientes registrados'); ?></h2>
                    <p>
                        <?php if($busqueda !== ''): ?>
                            Resultados para <strong><?php echo e($busqueda); ?></strong>
                        <?php else: ?>
                            Listado de pacientes registrados en el sistema.
                        <?php endif; ?>
                    </p>
                </div>

                <span class="contador-resultados">
                    <?php echo e($pacientes->total()); ?>

                    <?php echo e($pacientes->total() === 1 ? 'paciente' : 'pacientes'); ?>

                </span>
            </div>

            <?php if($pacientes->isNotEmpty()): ?>
                <div class="tabla-contenedor">
                    <table class="tabla-pacientes">
                        <thead>
                            <tr>
                                <th>Nombre completo</th>
                                <th>Documento</th>
                                <th>Correo electrónico</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $pacientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paciente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="nombre-paciente">
                                            <span class="avatar-paciente">
                                                <?php echo e(strtoupper(substr($paciente->nombre ?? 'P', 0, 1))); ?>

                                            </span>
                                            <span>
                                                <?php echo e($paciente->nombre); ?> <?php echo e($paciente->apellido); ?>

                                            </span>
                                        </div>
                                    </td>
                                    <td><?php echo e($paciente->documento ?: 'No registrado'); ?></td>
                                    <td><?php echo e($paciente->email ?: 'No registrado'); ?></td>
                                    <td><?php echo e($paciente->telefono ?: 'No registrado'); ?></td>
                                    <td>
                                        <?php if($paciente->estado): ?>
                                            <span class="estado-paciente estado-activo">Activo</span>
                                        <?php else: ?>
                                            <span class="estado-paciente estado-inactivo">Desactivado</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <div class="paginacion-pacientes">
                    <?php echo e($pacientes->appends(request()->query())->links()); ?>

                </div>
            <?php else: ?>
                <div class="sin-resultados">
                    <div class="sin-resultados-icono">🔎</div>
                    <h3>No se encontraron pacientes</h3>
                    <p>
                        <?php if($busqueda !== ''): ?>
                            No hay pacientes que coincidan con
                            <strong><?php echo e($busqueda); ?></strong>.
                            Intenta con otro nombre o documento.
                        <?php else: ?>
                            Todavía no hay pacientes que coincidan con el filtro seleccionado.
                        <?php endif; ?>
                    </p>
                    <a href="<?php echo e(route('pacientes.index')); ?>" class="btn-limpiar">
                        Ver todos los pacientes
                    </a>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/pacientes/Pacientes.blade.php ENDPATH**/ ?>