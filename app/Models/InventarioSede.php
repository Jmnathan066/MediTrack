<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioSede extends Model
{
    protected $table = 'inventario_sede';

    protected $primaryKey = 'idInventario';

    protected $fillable = [
        'idSede',
        'idMedicamento',
        'stock',
        'stockMinimo',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'stockMinimo' => 'integer',
        ];
    }

    public function sede()
    {
        return $this->belongsTo(
            Sede::class,
            'idSede',
            'idSede'
        );
    }

    public function medicamento()
    {
        return $this->belongsTo(
            Medicamento::class,
            'idMedicamento',
            'idMedicamento'
        );
    }
}