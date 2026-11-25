<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservacionMedica extends Model
{
    protected $table = 'observaciones_medicas';

    protected $primaryKey = 'idObservacion';

    protected $fillable = [
        'idPaciente',
        'idDoctor',
        'observacion',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idPaciente', 'id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idDoctor', 'id');
    }
}
