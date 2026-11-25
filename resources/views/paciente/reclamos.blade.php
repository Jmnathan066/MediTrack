
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mis reclamos | MediTrack EPS</title>

    @vite([
        'resources/css/partials.css',
        'resources/css/reclamos.css',
        'resources/js/partials.js'
    ])
</head>
<body>

    @include('partials.sidebar')
    @include('partials.topbar')

    <main class="main-content">
        <div class="reclamos-container">

            <div class="reclamos-header">
                <div>
                    <h1>Reclamos de medicamentos</h1>
                    <p>Registra un reclamo sobre un medicamento que hayas recibido.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="reclamo-card">
                <h2>Registrar un nuevo reclamo</h2>

                @if($entregas->isEmpty())
                    <div class="empty-state">
                        <p>Aún no tienes medicamentos entregados para registrar un reclamo.</p>
                    </div>
                @else
                    <form action="{{ route('paciente.reclamos.guardar') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="idDetalleOrden">Medicamento recibido</label>

                            <select name="idDetalleOrden" id="idDetalleOrden" required>
                                <option value="">Selecciona un medicamento</option>

                                @foreach($entregas as $entrega)
                                    <option
                                        value="{{ $entrega->idDetalleOrden }}"
                                        {{ old('idDetalleOrden') == $entrega->idDetalleOrden ? 'selected' : '' }}
                                    >
                                        {{ $entrega->detalleFormula?->medicamento?->nombre ?? 'Medicamento' }}
                                        — Cantidad recibida: {{ $entrega->cantidadEntregada }}
                                        — Entrega #{{ $entrega->idDetalleOrden }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="motivo">Motivo del reclamo</label>

                            <select name="motivo" id="motivo" required>
                                <option value="">Selecciona el motivo</option>
                                <option value="Medicamento incorrecto" {{ old('motivo') == 'Medicamento incorrecto' ? 'selected' : '' }}>Medicamento incorrecto</option>
                                <option value="Cantidad incompleta" {{ old('motivo') == 'Cantidad incompleta' ? 'selected' : '' }}>Cantidad incompleta</option>
                                <option value="Medicamento vencido" {{ old('motivo') == 'Medicamento vencido' ? 'selected' : '' }}>Medicamento vencido</option>
                                <option value="Medicamento en mal estado" {{ old('motivo') == 'Medicamento en mal estado' ? 'selected' : '' }}>Medicamento en mal estado</option>
                                <option value="Problemas con el empaque" {{ old('motivo') == 'Problemas con el empaque' ? 'selected' : '' }}>Problemas con el empaque</option>
                                <option value="Otro" {{ old('motivo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción del problema</label>

                            <textarea
                                name="descripcion"
                                id="descripcion"
                                rows="5"
                                maxlength="2000"
                                placeholder="Describe detalladamente lo sucedido..."
                                required
                            >{{ old('descripcion') }}</textarea>
                        </div>

                        <button type="submit" class="btn-reclamo">
                            Registrar reclamo
                        </button>
                    </form>
                @endif
            </section>

            <section class="reclamos-historial">
                <h2>Historial de mis reclamos</h2>

                @forelse($reclamos as $reclamo)
                    <article class="reclamo-item">
                        <div class="reclamo-item-header">
                            <div>
                                <h3>{{ $reclamo->detalleOrden?->detalleFormula?->medicamento?->nombre ?? 'Medicamento' }}</h3>
                                <span>{{ $reclamo->created_at?->format('d/m/Y H:i') }}</span>
                            </div>

                            <span class="estado-reclamo estado-{{ $reclamo->estado }}">
                                {{ ucfirst($reclamo->estado) }}
                            </span>
                        </div>

                        <p><strong>Motivo:</strong> {{ $reclamo->motivo }}</p>
                        <p><strong>Descripción:</strong> {{ $reclamo->descripcion }}</p>
                    </article>
                @empty
                    <div class="empty-state">
                        <p>Todavía no has registrado reclamos.</p>
                    </div>
                @endforelse
            </section>

        </div>
    </main>

</body>
</html>
