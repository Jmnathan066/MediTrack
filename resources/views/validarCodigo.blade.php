<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar código | MediTrack EPS</title>
    @vite('resources/css/validarCodigo.css')
</head>
<body>

    <main class="verification-container">

        <section class="verification-card">

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

                <div class="verification-icon">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="32" height="32" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0"/>
                        <circle cx="12" cy="16" r="1"/>
                    </svg>
                </div>

                <h2>Verificar identidad</h2>

                <p class="descripcion">
                    Estamos un paso más cerca. Ingresa el código temporal
                    que enviamos a tu correo electrónico o teléfono para
                    continuar con la recuperación de tu cuenta.
                </p>

                @if(session('error'))
                    <div class="alert alert-error">
                        <span class="alert-icon">!</span>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        <span class="alert-icon">✓</span>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <span class="alert-icon">!</span>
                        <div>
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="/validar-codigo">
                    @csrf

                    <div class="form-group">
                        <label for="codigo">Código de verificación</label>

                        <div class="input-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="20" height="20" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor"
                                 stroke-width="1.8" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="16" rx="3"/>
                                <path d="M7 8h.01M12 8h.01M17 8h.01"/>
                                <path d="M7 12h.01M12 12h.01M17 12h.01"/>
                                <path d="M7 16h10"/>
                            </svg>

                            <input
                                type="text"
                                id="codigo"
                                name="codigo"
                                placeholder="Ingresa tu código temporal"
                                value="{{ old('codigo') }}"
                                autocomplete="one-time-code"
                                aria-describedby="codigo-ayuda"
                                required
                            >
                        </div>

                        <small id="codigo-ayuda">
                            Revisa el mensaje que recibiste e ingresa el código tal como aparece.
                        </small>
                    </div>

                    <button type="submit" class="submit-button">
                        Verificar código

                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="19" height="19" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="m5 12 4 4L19 6"/>
                        </svg>
                    </button>
                </form>

                <a href="{{ url('/recuperar') }}" class="back-link">
                    <span aria-hidden="true">←</span>
                    Volver a recuperar cuenta
                </a>

            </div>

            <footer class="card-footer">
                MediTrack EPS · Cuidamos de ti
            </footer>

        </section>

    </main>

</body>
</html>