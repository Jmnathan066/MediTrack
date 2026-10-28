
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de usuarios | MediTrack EPS</title>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/partials.css',
        'resources/css/usuarios.css',
        'resources/js/partials.js'
    ]); ?>
</head>
<body>

    <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="usuarios-page">

        <header class="usuarios-header">
            <div>
                <p class="usuarios-eyebrow">ADMINISTRACIÓN</p>
                <h1>Gestión de usuarios</h1>
                <p class="usuarios-subtitle">
                    Consulta las cuentas registradas y administra su acceso al sistema.
                </p>
            </div>
        </header>

        <?php if(session('success')): ?>
            <div class="usuarios-alerta usuarios-alerta-success" role="status">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="usuarios-alerta usuarios-alerta-error" role="alert">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <section class="usuarios-resumen">
            <article class="usuarios-stat">
                <span>Total de usuarios</span>
                <strong><?php echo e($totalUsuarios); ?></strong>
            </article>

            <article class="usuarios-stat">
                <span>Cuentas activas</span>
                <strong><?php echo e($usuariosActivos); ?></strong>
            </article>

            <article class="usuarios-stat">
                <span>Cuentas desactivadas</span>
                <strong><?php echo e($usuariosInactivos); ?></strong>
            </article>
        </section>

        <section class="usuarios-panel">
            <div class="usuarios-panel-heading">
                <div>
                    <h2>Usuarios registrados</h2>
                    <p>Busca por datos del usuario o filtra los resultados por rol.</p>
                </div>
            </div>

            <form
                method="GET"
                action="<?php echo e(route('usuarios.index')); ?>"
                class="usuarios-filtros"
            >
                <div class="usuarios-campo usuarios-busqueda">
                    <label for="buscar">Buscar usuario</label>
                    <input
                        type="search"
                        id="buscar"
                        name="buscar"
                        value="<?php echo e($busqueda); ?>"
                        placeholder="Código, nombre, apellido o correo"
                    >
                </div>

                <div class="usuarios-campo usuarios-rol">
                    <label for="rol">Filtrar por rol</label>
                    <select name="rol" id="rol">
                        <option value="" <?php echo e($rol === '' ? 'selected' : ''); ?>>
                            Todos los roles
                        </option>
                        <option value="admin" <?php echo e($rol === 'admin' ? 'selected' : ''); ?>>
                            Administrador
                        </option>
                        <option value="paciente" <?php echo e($rol === 'paciente' ? 'selected' : ''); ?>>
                            Paciente
                        </option>
                        <option value="entidad" <?php echo e($rol === 'entidad' ? 'selected' : ''); ?>>
                            Entidad
                        </option>
                        <option value="doctor" <?php echo e($rol === 'doctor' ? 'selected' : ''); ?>>
                            Doctor
                        </option>
                    </select>
                </div>

                <div class="usuarios-acciones-filtro">
                    <button type="submit" class="usuarios-btn usuarios-btn-primary">
                        Buscar
                    </button>

                    <a
                        href="<?php echo e(route('usuarios.index')); ?>"
                        class="usuarios-btn usuarios-btn-secondary"
                    >
                        Limpiar
                    </a>
                </div>
            </form>

            <div class="usuarios-tabla-contenedor">
                <table class="usuarios-tabla">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Código</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <div class="usuarios-identidad">
                                        <strong>
                                            <?php echo e($usuario->nombre); ?> <?php echo e($usuario->apellido); ?>

                                        </strong>
                                        <span><?php echo e($usuario->email); ?></span>
                                    </div>
                                </td>

                                <td><?php echo e($usuario->codigo ?? 'Sin código'); ?></td>

                                <td>
                                    <span class="usuarios-rol-badge">
                                        <?php switch($usuario->tipoUsuario):
                                            case ('admin'): ?>
                                                Administrador
                                                <?php break; ?>
                                            <?php case ('paciente'): ?>
                                                Paciente
                                                <?php break; ?>
                                            <?php case ('entidad'): ?>
                                                Entidad
                                                <?php break; ?>
                                            <?php case ('doctor'): ?>
                                                Doctor
                                                <?php break; ?>
                                            <?php default: ?>
                                                <?php echo e($usuario->tipoUsuario); ?>

                                        <?php endswitch; ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="usuarios-estado <?php echo e($usuario->estado ? 'estado-activo' : 'estado-inactivo'); ?>">
                                        <?php echo e($usuario->estado ? 'Activo' : 'Desactivado'); ?>

                                    </span>
                                </td>

                                <td>
                                    <?php if((int) $usuario->id === (int) session('user_id')): ?>
                                        <span class="usuarios-propia-cuenta">
                                            Tu cuenta
                                        </span>
                                    <?php else: ?>
                                        <form
                                            method="POST"
                                            action="<?php echo e(route('usuarios.estado', $usuario)); ?>"
                                            onsubmit="return confirm('¿Seguro que deseas <?php echo e($usuario->estado ? 'desactivar' : 'activar'); ?> esta cuenta?');"
                                        >
                                            <?php echo csrf_field(); ?>

                                            <button
                                                type="submit"
                                                class="usuarios-btn-estado <?php echo e($usuario->estado ? 'btn-desactivar' : 'btn-activar'); ?>"
                                            >
                                                <?php echo e($usuario->estado ? 'Desactivar' : 'Activar'); ?>

                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="usuarios-vacio">
                                    No se encontraron usuarios con esos filtros.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="usuarios-paginacion">
                <span>
                    Mostrando <?php echo e($usuarios->firstItem() ?? 0); ?>

                    a <?php echo e($usuarios->lastItem() ?? 0); ?>

                    de <?php echo e($usuarios->total()); ?> usuarios
                </span>

                <?php echo e($usuarios->links()); ?>

            </div>
        </section>
    </main>

</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/administradores/usuarios.blade.php ENDPATH**/ ?>