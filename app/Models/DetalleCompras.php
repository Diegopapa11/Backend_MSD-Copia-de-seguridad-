<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompras extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_compra',
        'id_cliente',
        'cantidad',
        'precio',
    ];

}
