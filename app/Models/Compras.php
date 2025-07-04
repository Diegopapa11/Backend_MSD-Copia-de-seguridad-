<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compras extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_empleado',
        'id_detalle_compras',
        'fecha_compra',
        'estado',
        'metodo_pago_id',
    ];

}
