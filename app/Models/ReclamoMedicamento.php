<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReclamoMedicamento extends Model
{
    protected $table = 'reclamos_medicamentos';

    protected $primaryKey = 'idReclamo';

    protected $fillable = [
        'idPaciente',
        'idDetalleOrden',
        'motivo',
        'descripcion',
        'estado',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idPaciente', 'id');
    }

    public function detalleOrden(): BelongsTo
    {
        return $this->belongsTo(
            DetalleOrdenEntrega::class,
            'idDetalleOrden',
            'idDetalleOrden'
        );
    }
}
