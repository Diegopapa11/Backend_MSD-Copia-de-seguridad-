<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Empleados extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'empleados';

    protected $fillable = [
        'id_perfil',
        'id_permiso',
        'name',
        'email',
        'password',
        'ventas',
        'email_verified_at',
    ];

    protected $casts = [
        'ventas' => 'array',
        'email_verified_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function detalleCompra()
    {
        return $this->belongsTo(DetalleCompra::class, 'id_detalle_compras');
    }

    public function perfil()
    {
        return $this->belongsTo(Perfiles::class, 'id_perfil');
    }

    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'id_permiso');
    }
}
