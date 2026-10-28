<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear usuario | MediTrack EPS</title>

    <?php echo app('Illuminate\Foundation\Vite')([
    'resources/css/partials.css',
    'resources/css/administradores.css',
    'resources/js/partials.js',
    'resources/js/administradores.js'
    ]); ?>
</head>

<body>
    <?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php
        $rolSeleccionado = old(
            'tipoUsuario',
            session('rolCreado', 'admin')
        );

        if (!in_array($rolSeleccionado, ['admin', 'doctor'], true)) {
            $rolSeleccionado = 'admin';
        }

        $esDoctor = $rolSeleccionado === 'doctor';
    ?>

    <main class="admin-page">
        <div class="admin-container">

            
            <header class="admin-heading">
                <h1 id="adminPageTitle">
                    <?php echo e($esDoctor ? 'Crear doctor' : 'Crear administrador'); ?>

                </h1>

                <p id="adminPageDescription">
                    <?php echo e($esDoctor
                        ? 'Registra nuevos profesionales médicos en MediTrack EPS.'
                        : 'Gestiona el acceso de nuevos administradores a MediTrack EPS.'); ?>

                </p>
            </header>

            
            <div class="admin-role-selector" aria-label="Tipo de usuario">
                <button
                    type="button"
                    class="admin-role-option <?php echo e(!$esDoctor ? 'active' : ''); ?>"
                    data-role="admin"
                    aria-pressed="<?php echo e(!$esDoctor ? 'true' : 'false'); ?>"
                >
                    Crear administrador
                </button>

                <button
                    type="button"
                    class="admin-role-option <?php echo e($esDoctor ? 'active' : ''); ?>"
                    data-role="doctor"
                    aria-pressed="<?php echo e($esDoctor ? 'true' : 'false'); ?>"
                >
                    Crear doctor
                </button>
            </div>

            
            <?php if(session('success')): ?>
                <div class="admin-alert admin-alert-success" role="status">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            
            <?php if(session('error')): ?>
                <div class="admin-alert admin-alert-error" role="alert">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            
            <?php if($errors->any()): ?>
                <div class="admin-alert admin-alert-error" role="alert">
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            
            <section class="admin-card">

                <h2 id="adminCardTitle">
                    <?php echo e($esDoctor ? 'Información del doctor' : 'Información del administrador'); ?>

                </h2>

                <p class="description" id="adminCardDescription">
                    <?php echo e($esDoctor
                        ? 'Completa los datos para registrar un profesional médico.'
                        : 'Completa los datos para registrar una nueva cuenta administrativa.'); ?>

                </p>

                <form
                    method="POST"
                    action="<?php echo e(route('usuarios.guardar')); ?>"
                >
                    <?php echo csrf_field(); ?>

                    
                    <input
                        type="hidden"
                        name="tipoUsuario"
                        id="tipoUsuario"
                        value="<?php echo e($rolSeleccionado); ?>"
                    >

                    <div class="admin-form-grid admin-create-grid">

                        
                        <div class="admin-field">
                            <label for="nombre">Nombre</label>
                            <input
                                id="nombre"
                                type="text"
                                name="nombre"
                                value="<?php echo e(old('nombre')); ?>"
                                maxlength="255"
                                placeholder="Nombre"
                                autocomplete="given-name"
                                required
                            >
                        </div>

                        <div class="admin-field">
                            <label for="apellido">Apellido</label>
                            <input
                                id="apellido"
                                type="text"
                                name="apellido"
                                value="<?php echo e(old('apellido')); ?>"
                                maxlength="255"
                                placeholder="Apellido"
                                autocomplete="family-name"
                                required
                            >
                        </div>

                        
                        <div class="admin-field">
                            <label for="email">Correo electrónico</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="<?php echo e(old('email')); ?>"
                                maxlength="255"
                                placeholder="correo@ejemplo.com"
                                autocomplete="email"
                                required
                            >
                        </div>

                        <div class="admin-field">
                            <label for="documento">Documento</label>
                            <input
                                id="documento"
                                type="text"
                                name="documento"
                                value="<?php echo e(old('documento')); ?>"
                                placeholder="Número de documento"
                                autocomplete="off"
                                required
                            >
                        </div>

                        
                        <div class="admin-field">
                            <label for="telefono">Teléfono</label>
                            <input
                                id="telefono"
                                type="tel"
                                name="telefono"
                                value="<?php echo e(old('telefono')); ?>"
                                pattern="[0-9]{7,15}"
                                placeholder="Entre 7 y 15 dígitos"
                                autocomplete="tel"
                                required
                            >
                        </div>

                        <div class="admin-field">
                            <label for="empresa">Empresa (opcional)</label>
                            <input
                                id="empresa"
                                type="text"
                                name="empresa"
                                value="<?php echo e(old('empresa')); ?>"
                                maxlength="255"
                                placeholder="Nombre de la empresa"
                                autocomplete="organization"
                            >
                        </div>

                        
                        <div class="admin-field direccion-field">
                            <label for="direccion">Dirección</label>
                            <input
                                id="direccion"
                                type="text"
                                name="direccion"
                                value="<?php echo e(old('direccion')); ?>"
                                maxlength="255"
                                placeholder="Dirección de residencia"
                                autocomplete="street-address"
                                required
                            >
                        </div>

                        
                        <div class="admin-field password-field">
                            <label for="password">Contraseña</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Ingrese una contraseña"
                                autocomplete="new-password"
                                required
                            >
                            <small>
                                Mínimo 6 caracteres, con al menos una letra y un número.
                            </small>
                        </div>

                        <div class="admin-field password-confirmation-field">
                            <label for="password_confirmation">
                                Repetir contraseña
                            </label>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                placeholder="Repita la contraseña"
                                autocomplete="new-password"
                                required
                            >
                        </div>

                    </div>

                    
                    <div class="admin-actions">
                        <a
                            href="<?php echo e(url('/home')); ?>"
                            class="admin-button admin-button-secondary"
                        >
                            Volver al inicio
                        </a>

                        <button
                            type="submit"
                            class="admin-button admin-button-primary"
                            id="adminSubmitButton"
                        >
                            <?php echo e($esDoctor ? 'Crear doctor' : 'Crear administrador'); ?>

                        </button>
                    </div>

                </form>
            </section>

        </div>
    </main>
</body>
</html>
<?php /**PATH C:\Users\davim\medicine\resources\views/administradores/crear.blade.php ENDPATH**/ ?>