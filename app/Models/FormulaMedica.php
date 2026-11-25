<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormulaMedica extends Model
{
    protected $table = 'formulas_medicas';
    protected $primaryKey = 'idFormula';

    protected $fillable = [
        'idPaciente',
        'idDoctor',
        'fechaFormula',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'fechaFormula' => 'date',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idPaciente', 'id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idDoctor', 'id');
    }

    public function medicamentos(): HasMany
    {
        return $this->hasMany(
            FormulaMedicamento::class,
            'idFormula',
            'idFormula'
        );
    }
}
