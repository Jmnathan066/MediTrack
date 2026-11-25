<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    'nombre',
    'apellido',
    'email',
    'telefono',
    'direccion',
    'documento',
    'empresa',
    'idEPS',
    'tipoUsuario',
    'estado',
    'codigo',
    'password',
];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    public function eps()
{
    return $this->belongsTo(Eps::class, 'idEPS', 'idEPS');
}


public function observacionesComoPaciente()
{
    return $this->hasMany(
        ObservacionMedica::class,
        'idPaciente',
        'id'
    );
}

public function observacionesComoDoctor()
{
    return $this->hasMany(
        ObservacionMedica::class,
        'idDoctor',
        'id'
    );
}

}
