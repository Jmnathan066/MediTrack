<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenEntrega extends Model
{
    protected $table = 'ordenes_entrega';
    protected $primaryKey = 'idOrden';

    protected $fillable = [
        'idPaciente',
        'idFormula',
        'idUsuario',
        'idSede',
        'fechaEntrega',
        'estado',
    ];

    protected $casts = [
        'fechaEntrega' => 'datetime',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idPaciente', 'id');
    }

    public function formula(): BelongsTo
    {
        return $this->belongsTo(
            FormulaMedica::class,
            'idFormula',
            'idFormula'
        );
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsuario', 'id');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'idSede', 'idSede');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(
            DetalleOrdenEntrega::class,
            'idOrden',
            'idOrden'
        );
    }
}
