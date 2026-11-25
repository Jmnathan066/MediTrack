<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicamento extends Model
{
    protected $table = 'medicamentos';

    protected $primaryKey = 'idMedicamento';

    protected $fillable = [
        'nombre',
        'categoria',
        'descripcion',
        'fechaVencimiento',
        'stock',
        'stockMinimo',
        'valor',
        'idSede',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Un medicamento puede tener inventarios en diferentes sedes.
     */
    public function inventarios(): HasMany
    {
        return $this->hasMany(
            InventarioSede::class,
            'idMedicamento',
            'idMedicamento'
        );
    }
}
