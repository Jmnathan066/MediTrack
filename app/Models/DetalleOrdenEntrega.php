<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleOrdenEntrega extends Model
{
    protected $table = 'detalle_orden_entrega';
    protected $primaryKey = 'idDetalleOrden';

    protected $fillable = [
        'idOrden',
        'idDetalleFormula',
        'idLote',
        'cantidadEntregada',
    ];

    protected $casts = [
        'cantidadEntregada' => 'integer',
    ];

    public function orden(): BelongsTo
    {
        return $this->belongsTo(
            OrdenEntrega::class,
            'idOrden',
            'idOrden'
        );
    }

    public function detalleFormula(): BelongsTo
    {
        return $this->belongsTo(
            FormulaMedicamento::class,
            'idDetalleFormula',
            'idDetalle'
        );
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(
            LoteMedicamento::class,
            'idLote',
            'idLote'
        );
    }
}

