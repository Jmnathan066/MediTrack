
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
                <p>Consulta los pacientes asociados a tu EPS y registra sus observaciones médicas.</p>
            </div>
        </header>

        
        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <strong>No se pudo realizar la operación:</strong>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        
        <section class="pacientes-resumen">
            <div class="resumen-card">
                <div class="resumen-icono">👥</div>
                <div>
                    <span>Pacientes de tu EPS</span>
                    <strong><?php echo e($pacientes->total()); ?></strong>
                    <small>Pacientes asociados a tu entidad</small>
                </div>
            </div>
        </section>

        
        <section class="pacientes-panel">

            <div class="panel-heading">
                <h2>Buscar pacientes</h2>
                <p>Busca por nombre, apellido o número de documento.</p>
            </div>

            <form
                action="<?php echo e(route('doctor.pacientes')); ?>"
                method="GET"
                class="pacientes-search"
            >

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
                    <button type="submit" class="btn-buscar">
                        Buscar
                    </button>

                    <a href="<?php echo e(route('doctor.pacientes')); ?>" class="btn-limpiar">
                        Ver todos
                    </a>
                </div>

            </form>
        </section>

        
        <section class="pacientes-panel resultados-panel">

            <div class="resultados-header">
                <div>
                    <h2>
                        <?php echo e($busqueda !== '' ? 'Resultados de búsqueda' : 'Pacientes registrados'); ?>

                    </h2>

                    <p>
                        <?php if($busqueda !== ''): ?>
                            Resultados para <strong><?php echo e($busqueda); ?></strong>
                        <?php else: ?>
                            Pacientes pertenecientes a tu EPS.
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
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php $__currentLoopData = $pacientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paciente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <?php
                                    $historialPaciente = $observacionesPorPaciente->get(
                                        $paciente->id,
                                        collect()
                                    );
                                ?>

                                <tr>
                                    <td>
                                        <div class="nombre-paciente">
                                            <span class="avatar-paciente">
                                                <?php echo e(strtoupper(substr($paciente->nombre ?? 'P', 0, 1))); ?>

                                            </span>

                                            <span>
                                                <?php echo e($paciente->nombre); ?>

                                                <?php echo e($paciente->apellido); ?>

                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?php echo e($paciente->documento ?: 'No registrado'); ?>

                                    </td>

                                    <td>
                                        <?php echo e($paciente->email ?: 'No registrado'); ?>

                                    </td>

                                    <td>
                                        <?php echo e($paciente->telefono ?: 'No registrado'); ?>

                                    </td>

                                    <td>
                                        <?php if($paciente->estado): ?>
                                            <span class="estado-paciente estado-activo">
                                                Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="estado-paciente estado-inactivo">
                                                Desactivado
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="acciones-celda">

                                        
                                        <details class="acciones-paciente">
                                            <summary class="btn-buscar">
                                                Registrar observación
                                            </summary>

                                            <div class="formulario-observacion">

                                                <form
                                                    action="<?php echo e(route('doctor.observaciones.guardar')); ?>"
                                                    method="POST"
                                                >
                                                    <?php echo csrf_field(); ?>

                                                    <input
                                                        type="hidden"
                                                        name="idPaciente"
                                                        value="<?php echo e($paciente->id); ?>"
                                                    >

                                                    <label for="observacion-<?php echo e($paciente->id); ?>">
                                                        Observación médica
                                                    </label>

                                                    <textarea
                                                        id="observacion-<?php echo e($paciente->id); ?>"
                                                        name="observacion"
                                                        rows="5"
                                                        maxlength="5000"
                                                        required
                                                        placeholder="Escribe la observación médica del paciente..."
                                                    ><?php echo e(old('idPaciente') == $paciente->id ? old('observacion') : ''); ?></textarea>

                                                    <button
                                                        type="submit"
                                                        class="btn-buscar"
                                                    >
                                                        Guardar observación
                                                    </button>
                                                </form>

                                            </div>
                                        </details>

                                        
                                        <details class="acciones-paciente historial-paciente">

                                            <summary class="btn-limpiar">
                                                Historial de observaciones
                                                (<?php echo e($historialPaciente->count()); ?>)
                                            </summary>

                                            <div class="historial-observaciones">

                                                <?php $__empty_1 = true; $__currentLoopData = $historialPaciente; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $observacion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                                    <article class="observacion-registro">

                                                        <div class="observacion-registro-header">
                                                            <strong>
                                                                Observación médica
                                                            </strong>
                                                        </div>

                                                        <p>
                                                            <?php echo e($observacion->observacion); ?>

                                                        </p>

                                                        <small>
                                                            <strong>Doctor:</strong>
                                                            <?php echo e($observacion->doctor_nombre ?? 'No disponible'); ?>

                                                            <?php echo e($observacion->doctor_apellido ?? ''); ?>

                                                        </small>

                                                        <small>
                                                            <strong>Fecha:</strong>

                                                            <?php echo e($observacion->created_at
                                                                ? \Carbon\Carbon::parse($observacion->created_at)->format('d/m/Y H:i')
                                                                : 'Sin fecha'); ?>

                                                        </small>

                                                    </article>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                                                    <div class="sin-observaciones">
                                                        <span>📋</span>
                                                        <p>
                                                            Este paciente todavía no tiene observaciones médicas registradas.
                                                        </p>
                                                    </div>

                                                <?php endif; ?>

                                            </div>

                                        </details>

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
                            No hay pacientes asociados a tu EPS que coincidan con el filtro seleccionado.
                        <?php endif; ?>
                    </p>

                    <a href="<?php echo e(route('doctor.pacientes')); ?>" class="btn-limpiar">
                        Ver todos los pacientes
                    </a>
                </div>

            <?php endif; ?>

        </section>

    </main>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/doctor/consultaPacientes.blade.php ENDPATH**/ ?>