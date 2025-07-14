<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompras extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_empresa',
        'id_compra',
        'id_cliente',
        'cantidad',
        'precio',
    ];

    public function compra() {
        return $this->belongsTo(Compras::class, 'id_compra');
    }

    public function producto() {
        return $this->belongsTo(Productos::class, 'id_producto');
    }

    public function empleado() {
        return $this->belongsTo(Empleados::class, 'id_empleado');
    }

    public function cliente() {
        return $this->belongsTo(Clientes::class, 'id_cliente');
    }

    public function empresa() {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }


}
