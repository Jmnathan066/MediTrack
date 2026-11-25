<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventarioSede extends Model
{
    protected $table = 'inventario_sede';

    protected $primaryKey = 'idInventario';

    protected $fillable = [
        'idSede',
        'idMedicamento',
        'stock',
        'stockMinimo',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'stockMinimo' => 'integer',
            'estado' => 'boolean',
        ];
    }

    /**
     * El inventario pertenece a una sede.
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(
            Sede::class,
            'idSede',
            'idSede'
        );
    }

    /**
     * El inventario pertenece a un medicamento.
     */
    public function medicamento(): BelongsTo
    {
        return $this->belongsTo(
            Medicamento::class,
            'idMedicamento',
            'idMedicamento'
        );
    }

    /**
     * Un inventario puede contener varios lotes.
     */
    public function lotes(): HasMany
    {
        return $this->hasMany(
            LoteMedicamento::class,
            'idInventario',
            'idInventario'
        );
    }
}
