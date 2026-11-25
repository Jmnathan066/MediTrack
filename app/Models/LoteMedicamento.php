<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteMedicamento extends Model
{
    protected $table = 'lotes_medicamento';

    protected $primaryKey = 'idLote';

    protected $fillable = [
        'idInventario',
        'codigoLote',
        'fechaIngreso',
        'fechaVencimiento',
        'cantidadInicial',
        'stockDisponible',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fechaIngreso' => 'date',
            'fechaVencimiento' => 'date',
            'cantidadInicial' => 'integer',
            'stockDisponible' => 'integer',
            'estado' => 'boolean',
        ];
    }

    /**
     * El lote pertenece a un inventario de una sede.
     */
    public function inventario()
    {
        return $this->belongsTo(
            InventarioSede::class,
            'idInventario',
            'idInventario'
        );
    }
}
