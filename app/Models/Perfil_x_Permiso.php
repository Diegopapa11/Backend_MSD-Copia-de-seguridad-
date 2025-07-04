<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfil_x_Permiso extends Model
{
    use HasFactory;
        protected $fillable = [
        'id_perfil',
        'id_permisos',
    ];

}
