
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | MediTrack EPS</title>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/styleLogin.css'); ?>
</head>
<body>

<main class="login-page">

    <section class="brand-panel">
        <div class="brand-content">

            <a href="/" class="brand-logo">
                <div class="logo-icon">
                    <svg viewBox="0 0 48 48" fill="none" aria-hidden="true">
                        <rect width="48" height="48" rx="15" fill="white" fill-opacity=".16"/>
                        <path d="M24 12V36M12 24H36" stroke="white" stroke-width="5" stroke-linecap="round"/>
                    </svg>
                </div>

                <div class="brand-name">
                    <span class="brand-title">MediTrack</span>
                    <span class="brand-subtitle">EPS</span>
                </div>
            </a>

            <div class="brand-message">
                <span class="eyebrow">¡QUÉ BUENO TENERTE AQUÍ!</span>

                <h1>
                    Tu espacio para
                    <span>cuidar la salud.</span>
                </h1>

                <p>
                    Organiza y consulta la información de salud
                    de una forma más fácil y rápida.
                </p>
            </div>

            <div class="brand-footer">
                <span class="status-dot"></span>
                <span>Todo lo que necesitas, en un solo lugar.</span>
            </div>

        </div>

        <div class="decoration decoration-one"></div>
        <div class="decoration decoration-two"></div>
        <div class="decoration decoration-three"></div>
    </section>


    <section class="form-panel">
        <div class="login-box">

            <a href="/" class="mobile-brand">
                <div class="mobile-logo-icon">
                    <svg viewBox="0 0 48 48" fill="none" aria-hidden="true">
                        <path d="M24 12V36M12 24H36" stroke="white" stroke-width="5" stroke-linecap="round"/>
                    </svg>
                </div>
                <span>MediTrack <small>EPS</small></span>
            </a>

            <div class="form-heading">
                <span class="form-eyebrow">¡HOLA DE NUEVO!</span>
                <h2>Iniciar sesión</h2>
                <p>Ingresa tus datos para entrar a tu cuenta.</p>
            </div>


            <?php if($errors->any()): ?>
                <div class="alert alert-error" role="alert">
                    <span class="alert-icon">!</span>
                    <div>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><?php echo e($error); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-error" role="alert">
                    <span class="alert-icon">!</span>
                    <p><?php echo e(session('error')); ?></p>
                </div>
            <?php endif; ?>

            <?php if(session('success')): ?>
                <div class="alert alert-success" role="status">
                    <span class="alert-icon">✓</span>
                    <p><?php echo e(session('success')); ?></p>
                </div>
            <?php endif; ?>

            <?php if(session('nuevo_codigo')): ?>
                <div class="alert alert-success" role="status">
                    <span class="alert-icon">✓</span>
                    <p><?php echo e(session('nuevo_codigo')); ?></p>
                </div>
            <?php endif; ?>


            <form method="POST" action="/login" class="login-form">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="user">Documento de identidad</label>

                    <div class="input-wrapper">
                        <span class="input-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <rect x="3" y="5" width="18" height="14" rx="3"
                                      stroke="currentColor" stroke-width="1.7"/>
                                <circle cx="8" cy="11" r="2"
                                        stroke="currentColor" stroke-width="1.7"/>
                                <path d="M5.5 16C6.1 14.5 7 14 8 14C9 14 10 14.5 10.5 16M13 10H18M13 14H18"
                                      stroke="currentColor" stroke-width="1.7"
                                      stroke-linecap="round"/>
                            </svg>
                        </span>

                        <input
                            type="text"
                            id="user"
                            name="user"
                            placeholder="Ingresa tu documento"
                            value="<?php echo e(old('user')); ?>"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                </div>


                <div class="form-group">
                    <label for="pass">Contraseña o código</label>

                    <div class="input-wrapper">
                        <span class="input-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <rect x="4" y="10" width="16" height="11" rx="3"
                                      stroke="currentColor" stroke-width="1.7"/>
                                <path d="M8 10V7A4 4 0 0 1 16 7V10"
                                      stroke="currentColor" stroke-width="1.7"
                                      stroke-linecap="round"/>
                                <circle cx="12" cy="15" r="1.2" fill="currentColor"/>
                                <path d="M12 16V18" stroke="currentColor"
                                      stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </span>

                        <input
                            type="password"
                            id="pass"
                            name="pass"
                            placeholder="Ingresa tu contraseña o código"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            id="togglePassword"
                            aria-label="Mostrar contraseña"
                            aria-pressed="false"
                        >
                            <svg id="eyeIcon" viewBox="0 0 24 24" fill="none"
                                 aria-hidden="true">
                                <path d="M2.5 12S6 5.5 12 5.5S21.5 12 21.5 12S18 18.5 12 18.5S2.5 12 2.5 12Z"
                                      stroke="currentColor" stroke-width="1.7"
                                      stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="3"
                                        stroke="currentColor" stroke-width="1.7"/>
                            </svg>
                        </button>
                    </div>
                </div>


                <div class="forgot-password">
                    <a href="/recuperar">¿Olvidaste tu contraseña?</a>
                </div>


                <button type="submit" class="login-button">
                    <span>Ingresar a mi cuenta</span>

                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12H19M12 5L19 12L12 19"
                              stroke="currentColor" stroke-width="2"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

            </form>


            <div class="register-section">
                <span>¿Todavía no tienes una cuenta?</span>
                <a href="/registrarse">Crear una cuenta</a>
            </div>

            <div class="security-note">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3L19 6V11C19 15.5 16.2 19 12 21C7.8 19 5 15.5 5 11V6L12 3Z"
                          stroke="currentColor" stroke-width="1.7"
                          stroke-linejoin="round"/>
                    <path d="M9 12L11 14L15 10"
                          stroke="currentColor" stroke-width="1.7"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Tu cuenta está protegida.</span>
            </div>

        </div>
    </section>

</main>


<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('pass');
    const eyeIcon = document.getElementById('eyeIcon');

    const eyeOpen = `
        <path d="M2.5 12S6 5.5 12 5.5S21.5 12 21.5 12S18 18.5 12 18.5S2.5 12 2.5 12Z"
              stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
        <circle cx="12" cy="12" r="3"
                stroke="currentColor" stroke-width="1.7"/>
    `;

    const eyeClosed = `
        <path d="M3 3L21 21" stroke="currentColor" stroke-width="1.7"
              stroke-linecap="round"/>
        <path d="M10.6 5.8C11.1 5.6 11.5 5.5 12 5.5C18 5.5 21.5 12 21.5 12
                 S20.3 14.2 18.3 16M6.2 6.7C3.8 8.4 2.5 12 2.5 12
                 S6 18.5 12 18.5C13.5 18.5 14.8 18.1 16 17.4"
              stroke="currentColor" stroke-width="1.7"
              stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M9.9 9.9A3 3 0 0 0 14.1 14.1"
              stroke="currentColor" stroke-width="1.7"
              stroke-linecap="round"/>
    `;

    togglePassword.addEventListener('click', function () {
        const isPassword = passwordInput.type === 'password';

        passwordInput.type = isPassword ? 'text' : 'password';
        eyeIcon.innerHTML = isPassword ? eyeClosed : eyeOpen;

        togglePassword.setAttribute('aria-pressed', String(isPassword));
        togglePassword.setAttribute(
            'aria-label',
            isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'
        );
    });
</script>

</body>
</html>

<?php /**PATH C:\Users\davim\medicine\resources\views/login.blade.php ENDPATH**/ ?>