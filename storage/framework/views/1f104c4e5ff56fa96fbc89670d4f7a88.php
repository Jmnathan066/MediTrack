<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar cuenta | MediTrack EPS</title>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/recuperar.css'); ?>
</head>
<body>

    <main class="recovery-container">

        <section class="recovery-card">

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

                <div class="recovery-icon">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="30" height="30" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0"/>
                        <path d="M12 15v3"/>
                    </svg>
                </div>

                <h2>Recuperar cuenta</h2>

                <p class="description">
                    No te preocupes, te ayudaremos a recuperar el acceso
                    a tu cuenta. Ingresa tus datos para continuar.
                </p>

                <?php if(session('error')): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">!</span>
                        <p><?php echo e(session('error')); ?></p>
                    </div>
                <?php endif; ?>

                <?php if(session('success')): ?>
                    <div class="alert alert-success">
                        <span class="alert-icon">✓</span>
                        <p><?php echo e(session('success')); ?></p>
                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">!</span>
                        <div>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p><?php echo e($error); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/recuperar">
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label for="documento">Número de documento</label>
                        <div class="input-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="19" height="19" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor"
                                 stroke-width="1.8" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <circle cx="9" cy="10" r="2"/>
                                <path d="M6 15c.8-1.5 4.2-1.5 5 0"/>
                                <path d="M14 9h4"/>
                                <path d="M14 13h4"/>
                            </svg>

                            <input
                                type="text"
                                id="documento"
                                name="documento"
                                placeholder="Ingresa tu documento"
                                value="<?php echo e(old('documento')); ?>"
                                autocomplete="username"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contacto">Correo electrónico o teléfono</label>
                        <div class="input-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="19" height="19" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor"
                                 stroke-width="1.8" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path d="m3 7 9 6 9-6"/>
                            </svg>

                            <input
                                type="text"
                                id="contacto"
                                name="contacto"
                                placeholder="Correo o teléfono registrado"
                                value="<?php echo e(old('contacto')); ?>"
                                autocomplete="email"
                                required
                            >
                        </div>
                        <small>Usa los datos asociados a tu cuenta.</small>
                    </div>

                    <button type="submit" class="submit-button">
                        Enviar código
                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="19" height="19" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M5 12h14"/>
                            <path d="m12 5 7 7-7 7"/>
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

</body>
</html><?php /**PATH C:\Users\davim\medicine\resources\views/recuperar.blade.php ENDPATH**/ ?>