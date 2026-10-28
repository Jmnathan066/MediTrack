<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva contraseña | MediTrack EPS</title>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/nuevaPassword.css'); ?>
</head>
<body>

    <main class="password-container">

        <section class="password-card">

            <div class="brand-section">
                <div class="brand-icon">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="38" height="38" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z"/>
                        <path d="M12 8v8"/>
                        <path d="M8 12h8"/>
                    </svg>
                </div>

                <h1>MediTrack <span>EPS</span></h1>
                <p>Tu salud, siempre contigo.</p>
            </div>

            <div class="form-section">

                <div class="password-icon">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="32" height="32" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0"/>
                        <path d="m9 16 2 2 4-4"/>
                    </svg>
                </div>

                <h2>Crear nueva contraseña</h2>

                <p class="description">
                    Ya casi terminamos. Crea una contraseña segura
                    para recuperar el acceso a tu cuenta.
                </p>

                <?php if(session('error')): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">!</span>
                        <p><?php echo e(session('error')); ?></p>
                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">!</span>
                        <ul>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/nueva-password">
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label for="password">Nueva contraseña</label>

                        <div class="input-wrapper">
                            <svg class="field-icon"
                                 xmlns="http://www.w3.org/2000/svg"
                                 width="19" height="19" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor"
                                 stroke-width="1.8" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0"/>
                            </svg>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Crea una contraseña segura"
                                autocomplete="new-password"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-target="password"
                                aria-label="Mostrar contraseña"
                                aria-pressed="false"
                            >
                                <svg class="eye-icon"
                                     xmlns="http://www.w3.org/2000/svg"
                                     width="19" height="19" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor"
                                     stroke-width="1.8" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>

                        <small>Elige una contraseña que no utilices en otros sitios.</small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">
                            Confirmar nueva contraseña
                        </label>

                        <div class="input-wrapper">
                            <svg class="field-icon"
                                 xmlns="http://www.w3.org/2000/svg"
                                 width="19" height="19" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor"
                                 stroke-width="1.8" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0"/>
                            </svg>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Escribe nuevamente la contraseña"
                                autocomplete="new-password"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-target="password_confirmation"
                                aria-label="Mostrar confirmación de contraseña"
                                aria-pressed="false"
                            >
                                <svg class="eye-icon"
                                     xmlns="http://www.w3.org/2000/svg"
                                     width="19" height="19" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor"
                                     stroke-width="1.8" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="submit-button">
                        Actualizar contraseña

                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="19" height="19" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="m5 12 4 4L19 6"/>
                        </svg>
                    </button>
                </form>

                <a href="<?php echo e(url('/login')); ?>" class="back-link">
                    <span aria-hidden="true">←</span>
                    Volver al inicio de sesión
                </a>

            </div>

            <footer class="card-footer">
                MediTrack EPS · Cuidamos de ti
            </footer>

        </section>

    </main>

    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.target);
                const showing = input.type === 'text';

                input.type = showing ? 'password' : 'text';
                button.setAttribute('aria-pressed', String(!showing));
                button.setAttribute(
                    'aria-label',
                    showing ? 'Mostrar contraseña' : 'Ocultar contraseña'
                );
            });
        });
    </script>

</body>
</html><?php /**PATH C:\Users\davim\medicine\resources\views/nuevaPassword.blade.php ENDPATH**/ ?>