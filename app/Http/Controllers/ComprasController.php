<?php

namespace App\Http\Controllers;

use App\Models\Compras;
use Illuminate\Http\Request;
use App\Models\DetalleCompras;
use App\Models\User;

class ComprasController extends Controller
{
//API DE CREAR COMPRA
    public function crearCompra(Request $request)
    {
        // Paso 1: Verificar si el usuario está autenticado
        if (auth()->check()) {
            // Obtener el ID del usuario autenticado
            $usuarioId = auth()->user()->id;

            // Paso 2: Solicitud de Información de Detalle (?)
            $detallesCompra = $request->input('detallecompra');
            
            // Paso 3: Almacenamiento en la Tabla de Compra
            $compra = Compras::create([
                'id_usuario' => $usuarioId,
                'id_producto' => $request->input('id_producto'),
                'total' => $request->input('total'),
                'estado' => 1, // Por defecto
            ]);
            

            // Paso 4: Obtención del ID de Compra
            $compraId = $compra->id;

            // Paso 5: Almacenamiento en la Tabla de Detalle
            foreach ($detallesCompra as $detalle) {
                DetalleCompras::create([
                    'id_producto' => $detalle['id_producto'],
                    'id_compra' => $compraId,
                    'cantidad' => $detalle['cantidad'],
                    'precio' => $detalle['precio'],
                ]);
            }

            // Paso 6: Respuesta
            return response()->json(['id_compra' => $compraId]);
        } else {
            // Manejar el caso en el que el usuario no está autenticado
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
    }

//API DE ACTULIZAR ESTADO DE COMPRA (1=>2)
    public function actualizarEstadoCompra(Request $request)
    {
        $idCompra = $request->input('id_compra');
        $estado = $request->input('estado');

        $compras = Compras::find($idCompra);

        if (!$compras) {
            return response()->json(['mensaje' => 'Compra no encontrada'], 404);
        }

        // Verificar si la compra ya está en proceso (estado diferente de 1 - En proceso)
        if ($compras->estado != 1) {
            return response()->json(['mensaje' => 'La compra ya no está en proceso, no se pueden agregar más productos'], 400);
        }

        // Actualizar estado de la compra
        $compras->estado = $estado;
        $compras->save();

        // Si la compra es exitosa, realizar acciones adicionales
        if ($estado == 2) {
                // Recorrer los detalles de la compra
                foreach ($compras->detalles as $detalle) {
                    // Registrar en la tabla de usuario el nuevo producto comprado
                    $usuario = User::find($compras->id_usuario);
                    $datos = $usuario->datos ?? []; // Si $usuario->datos es null, inicializa $datos como un array vacío
                    array_push($datos, ['id_producto' => $detalle->id_producto]);
                    $usuario->datos = $datos;
                    $usuario->save();
                }
            }

        return response()->json(['mensaje' => 'Estado de compra actualizado']);
    }

//CRUD
    public function index()
    {
        //almacenar en variable todo y regresar en json
        $compras = Compras::all();
        return response()->json($compras);
    }

    public function store(Request $request)
    {
        //reglas de campo, se agrgega aquí los ABCC (create)
        /* $request->validate([
            'id_producto' => 'required',
            'id_usuario' => 'required',
            'total' => 'required',
            'estado' => 1,
        ]);

        Compras::create($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.'); */

    }

    public function show()
    {
        $compras = Compras::all();
        return response()->json(['compra' => $compras]);
    }

}
