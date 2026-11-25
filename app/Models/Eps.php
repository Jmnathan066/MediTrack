<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Eps extends Model
{
    protected $table = 'eps';

    protected $primaryKey = 'idEPS';

    protected $fillable = [
        'nombre',
        'nit',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'idEPS', 'idEPS');
    }
}
