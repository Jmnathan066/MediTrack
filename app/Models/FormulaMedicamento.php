<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormulaMedicamento extends Model
{
    protected $table = 'formula_medicamentos';

    protected $primaryKey = 'idDetalle';

    protected $fillable = [
        'idFormula',
        'idMedicamento',
        'dosis',
        'frecuencia',
        'duracion',
        'cantidad',
        'unidad',
        'indicaciones',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function formula(): BelongsTo
    {
        return $this->belongsTo(
            FormulaMedica::class,
            'idFormula',
            'idFormula'
        );
    }

    public function medicamento(): BelongsTo
    {
        return $this->belongsTo(
            Medicamento::class,
            'idMedicamento',
            'idMedicamento'
        );
    }
}
