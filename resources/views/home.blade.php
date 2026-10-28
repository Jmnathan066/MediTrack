<!DOCTYPE html>
<html>
<head>
    <title>Inicio</title>
    @vite([
        'resources/css/partials.css',
        'resources/css/home.css',
        'resources/js/partials.js',
        'resources/js/home.js'
    ])
</head>
<body>

@if(session('error'))
    <div class="alerta-error flotante">{{ session('error') }}</div>
@endif

@include('partials.sidebar')
@include('partials.topbar')

<div class="content">
    <h1>Bienvenido, {{ session('nombre') }}</h1>

    @php
        $rol = session('tipoUsuario');

        $descripciones = [
            'paciente' => 'Consulta medicamentos, revisa tus entregas y gestiona tus solicitudes.',
            'entidad' => 'Consulta pacientes, gestiona entregas y organiza los traslados entre sedes.',
            'doctor' => 'Consulta pacientes y gestiona observaciones y fórmulas médicas.',
            'admin' => 'Administra usuarios, medicamentos, inventario y supervisa el sistema.',
        ];

        $modulos = [
            'paciente' => [
                ['titulo' => 'Medicamentos', 'descripcion' => 'Consulta disponibilidad y busca medicamentos.', 'url' => '/medicamentos'],
                ['titulo' => 'Mis entregas', 'descripcion' => 'Consulta el estado de tus entregas.', 'url' => null],
                ['titulo' => 'Historial', 'descripcion' => 'Revisa el historial de tus entregas anteriores.', 'url' => null],
                ['titulo' => 'Reclamos', 'descripcion' => 'Registra y consulta tus reclamos.', 'url' => null],
            ],
            'entidad' => [
                ['titulo' => 'Pacientes', 'descripcion' => 'Consulta la información de los pacientes.', 'url' => null],
                ['titulo' => 'Fórmulas médicas', 'descripcion' => 'Verifica las fórmulas asociadas.', 'url' => null],
                ['titulo' => 'Entregas', 'descripcion' => 'Registra y consulta entregas de medicamentos.', 'url' => null],
                ['titulo' => 'Traslados', 'descripcion' => 'Gestiona traslados de inventario entre sedes.', 'url' => null],
                ['titulo' => 'Medicamentos', 'descripcion' => 'Consulta el catálogo de medicamentos.', 'url' => '/medicamentos'],
            ],
            'doctor' => [
                ['titulo' => 'Pacientes', 'descripcion' => 'Consulta la información de tus pacientes.', 'url' => null],
                ['titulo' => 'Observaciones', 'descripcion' => 'Registra y consulta observaciones médicas.', 'url' => null],
                ['titulo' => 'Fórmulas médicas', 'descripcion' => 'Gestiona las fórmulas de tus pacientes.', 'url' => null],
            ],
            'admin' => [
                ['titulo' => 'Usuarios', 'descripcion' => 'Crea y administra cuentas de usuarios, doctores y administradores.', 'url' => '/administradores/crear'],
                ['titulo' => 'Medicamentos', 'descripcion' => 'Consulta y gestiona los medicamentos.', 'url' => '/medicamentos'],
                ['titulo' => 'Inventario', 'descripcion' => 'Consulta las existencias y movimientos de inventario.', 'url' => null],
                ['titulo' => 'Reportes', 'descripcion' => 'Consulta reportes generales del sistema.', 'url' => null],
                ['titulo' => 'Auditoría', 'descripcion' => 'Consulta los registros de actividad del sistema.', 'url' => null],
            ],
        ];

        $opciones = $modulos[$rol] ?? [];
    @endphp

    <p class="panel-descripcion">
        {{ $descripciones[$rol] ?? 'Bienvenido al sistema.' }}
    </p>

    <h2 class="panel-section-title">Módulos disponibles</h2>

    <div class="panel-cards">
        @foreach($opciones as $modulo)
            @if($modulo['url'])
                <a href="{{ $modulo['url'] }}" class="panel-card">
                    <h3>{{ $modulo['titulo'] }}</h3>
                    <p>{{ $modulo['descripcion'] }}</p>
                </a>
            @else
                <div class="panel-card panel-card-pending">
                    <h3>{{ $modulo['titulo'] }}</h3>
                    <p>{{ $modulo['descripcion'] }}</p>
                    <span class="modulo-pendiente">-Próximamente-</span>
                </div>
            @endif
        @endforeach

        <a href="/perfil" class="panel-card">
            <h3>Mi perfil</h3>
            <p>Consulta y edita tu información personal.</p>
        </a>

        <a href="/perfil/password" class="panel-card">
            <h3>Seguridad</h3>
            <p>Cambia tu contraseña.</p>
        </a>
    </div>
</div>

</body>
</html>