
<div class="sidebar" id="sidebar">

    <button class="sidebar-toggle" id="sidebarToggle" type="button"
            aria-label="Contraer o expandir menú">
        ☰
    </button>

    {{-- INICIO --}}
    <a href="/home" class="menu-item">
        <span class="menu-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="m3 10 9-7 9 7"/>
                <path d="M5 9v12h14V9"/>
                <path d="M9 21v-7h6v7"/>
            </svg>
        </span>
        <span class="menu-text">Inicio</span>
    </a>

    {{-- MI CUENTA --}}
    <div class="sidebar-section">
        <span class="section-title">MI CUENTA</span>
    </div>

    <a href="/perfil" class="menu-item">
        <span class="menu-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 21v-2a8 8 0 0 1 16 0v2Z"/>
            </svg>
        </span>
        <span class="menu-text">Mi perfil</span>
    </a>

    <a href="/perfil/password" class="menu-item">
        <span class="menu-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <rect x="4" y="10" width="16" height="11" rx="2"/>
                <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                <path d="M12 14v3"/>
            </svg>
        </span>
        <span class="menu-text">Cambiar contraseña</span>
    </a>

    {{-- RECLAMOS: SOLO PACIENTE --}}
    @if(session('tipoUsuario') === 'paciente')
        <a href="{{ route('paciente.reclamos.index') }}" class="menu-item">
            <span class="menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3 3 20h18L12 3Z"/>
                    <path d="M12 9v5"/>
                    <path d="M12 17h.01"/>
                </svg>
            </span>
            <span class="menu-text">Mis reclamos</span>
        </a>
    @endif

    {{-- GESTIÓN MÉDICA --}}
    @if(in_array(session('tipoUsuario'), ['admin', 'entidad', 'doctor']))

        <div class="sidebar-section">
            <span class="section-title">GESTIÓN MÉDICA</span>
        </div>

        {{-- CONSULTA DE PACIENTES: ADMIN Y ENTIDAD --}}
        @if(in_array(session('tipoUsuario'), ['admin', 'entidad']))
            <a href="{{ route('pacientes.index') }}" class="menu-item">
                <span class="menu-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="10.5" cy="10.5" r="6.5"/>
                        <path d="m16 16 5 5"/>
                        <path d="M10.5 7v7M7 10.5h7"/>
                    </svg>
                </span>
                <span class="menu-text">Consulta de pacientes</span>
            </a>
        @endif

        {{-- CONSULTAR FÓRMULAS: SOLO ENTIDAD --}}
        @if(session('tipoUsuario') === 'entidad')
            <a href="{{ route('empresa.formulas.index') }}" class="menu-item">
                <span class="menu-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>
                        <path d="M14 2v6h6"/>
                        <path d="m8 15 2.5 2.5L16 12"/>
                    </svg>
                </span>
                <span class="menu-text">Consultar fórmulas</span>
            </a>

            {{-- ÓRDENES DE ENTREGA: SOLO ENTIDAD --}}
            <a href="{{ route('empresa.ordenes.index') }}" class="menu-item">
                <span class="menu-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="17" rx="2"/>
                        <path d="M8 2v4M16 2v4M3 10h18"/>
                        <path d="m8 15 2 2 5-5"/>
                    </svg>
                </span>
                <span class="menu-text">Órdenes de entrega</span>
            </a>
        @endif

        {{-- CONSULTA MÉDICA: DOCTOR Y ADMIN --}}
        @if(in_array(session('tipoUsuario'), ['doctor', 'admin']))
            <a href="{{ route('doctor.pacientes') }}" class="menu-item">
                <span class="menu-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="10.5" cy="10.5" r="6.5"/>
                        <path d="m16 16 5 5"/>
                        <path d="M10.5 7v7M7 10.5h7"/>
                    </svg>
                </span>
                <span class="menu-text">Consulta médica</span>
            </a>
        @endif

        {{-- FÓRMULAS MÉDICAS: DOCTOR --}}
        @if(session('tipoUsuario') === 'doctor')
            <a href="{{ route('doctor.formulas') }}" class="menu-item">
                <span class="menu-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>
                        <path d="M14 2v6h6"/>
                        <path d="M8 13h8M8 17h8"/>
                    </svg>
                </span>
                <span class="menu-text">Fórmulas médicas</span>
            </a>
        @endif

    @endif

    {{-- MEDICAMENTOS --}}
    <div class="menu-group">
        <div class="menu-title" onclick="toggleMedicamentos()">
            <span class="menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M10.5 3.5a5 5 0 0 1 7.1 0l2.9 2.9a5 5 0 0 1-7.1 7.1l-2.9-2.9a5 5 0 0 1 0-7.1Z"/>
                    <path d="m9 5 10 10"/>
                    <path d="M4.5 13.5a5 5 0 0 1 7.1 0l-1.1-1.1"/>
                    <path d="m10 12 2 2"/>
                    <path d="m4.5 13.5-1 1a5 5 0 0 0 7.1 7.1l1-1"/>
                </svg>
            </span>
            <span class="menu-text">Medicamentos</span>
            <span class="menu-arrow">▼</span>
        </div>

        <div id="submenuMedicamentos" class="submenu hidden">
            <a href="/medicamentos">Ver medicamentos</a>
        </div>
    </div>

    {{-- ADMINISTRACIÓN --}}
    @if(session('tipoUsuario') === 'admin')

        <div class="sidebar-section">
            <span class="section-title">ADMINISTRACIÓN</span>
        </div>

        {{-- GESTIÓN DE USUARIOS --}}
        <a href="{{ route('usuarios.index') }}" class="menu-item">
            <span class="menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="10" cy="7" r="4"/>
                    <path d="M20 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </span>
            <span class="menu-text">Gestión de usuarios</span>
        </a>

        {{-- GESTIÓN DE SEDES --}}
        <a href="{{ route('sedes.index') }}" class="menu-item">
            <span class="menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 21h18"/>
                    <path d="M5 21V5l7-3 7 3v16"/>
                    <path d="M9 9h.01M15 9h.01M9 13h.01M15 13h.01"/>
                    <path d="M10 21v-4h4v4"/>
                </svg>
            </span>
            <span class="menu-text">Gestión de sedes</span>
        </a>

        {{-- GESTIÓN DE EPS --}}
        <a href="{{ route('eps.index') }}" class="menu-item">
            <span class="menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 21h18"/>
                    <path d="M5 21V7l7-4 7 4v14"/>
                    <path d="M9 21v-5h6v5"/>
                    <path d="M9 10h.01M15 10h.01"/>
                    <path d="M12 8v4M10 10h4"/>
                </svg>
            </span>
            <span class="menu-text">Gestión de EPS</span>
        </a>

        {{-- CREAR PERSONAL --}}
        <a href="{{ route('administradores.crear') }}" class="menu-item">
            <span class="menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="9" cy="8" r="4"/>
                    <path d="M2 21v-2a7 7 0 0 1 14 0v2"/>
                    <path d="M19 8v6M16 11h6"/>
                </svg>
            </span>
            <span class="menu-text">Crear personal</span>
        </a>

    @endif

</div>
