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
        if (auth()->check()) {
            $idEmpleado = auth()->user()->id;

            $detallesCompra = $request->input('detallecompra');
            $idCliente = $request->input('id_cliente'); 
            $total = $request->input('total');
            $idProducto = $request->input('id_producto');
            $idMetodoPago = $request->input('metodo_pago_id'); // <-- Captura método pago

            // Validar que id_metodo_pago exista en la tabla de métodos de pago (opcional)
            /*
            if (!\App\Models\MetodoPago::find($idMetodoPago)) {
                return response()->json(['error' => 'Método de pago inválido'], 422);
            }
            */

            // Obtener id_empresa del empleado autenticado para guardarlo en compras y detalle_compras
            $empleado = \App\Models\Empleados::find($idEmpleado);
            $idEmpresa = $empleado ? $empleado->id_empresa : null;

            // Crear compra con id_empresa y método de pago
            $compra = Compras::create([
                'id_empleado' => $idEmpleado,
                'id_producto' => $idProducto,
                'total' => $total,
                'estado' => 1,
                'id_empresa' => $idEmpresa,
                'metodo_pago_id' => $idMetodoPago,  // <-- Guardar método pago aquí
            ]);

            $compraId = $compra->id;

            // Guardar detalles de compra con id_empresa, id_compra, etc.
            foreach ($detallesCompra as $detalle) {
                DetalleCompras::create([
                    'id_producto' => $detalle['id_producto'],
                    'id_compra' => $compraId,
                    'cantidad' => $detalle['cantidad'],
                    'precio' => $detalle['precio'],
                    'id_empresa' => $idEmpresa,
                    'id_cliente' => $idCliente
                ]);

                // Actualizar stock por cada detalle
                $producto = \App\Models\Productos::find($detalle['id_producto']);
                if ($producto) {
                    $producto->stock = max(0, $producto->stock - $detalle['cantidad']);
                    $producto->save();
                }
            }

            // Guardar compra en JSON de cliente
            if ($idCliente) {
                $cliente = \App\Models\Clientes::find($idCliente);
                if ($cliente) {
                    $comprasAnteriores = $cliente->compras ? json_decode($cliente->compras, true) : [];
                    $nuevaCompra = [
                        'id_compra' => $compraId,
                        'fecha' => now()->toDateTimeString(),
                        'productos' => $detallesCompra,
                        'total' => $total,
                        'id_metodo_pago' => $idMetodoPago, // también lo guardas aquí si quieres
                    ];
                    $comprasAnteriores[] = $nuevaCompra;
                    $cliente->compras = json_encode($comprasAnteriores);
                    $cliente->save();
                }
            }

            // Guardar compra en JSON de empleado
            if ($idEmpleado) {
                $empleado = \App\Models\Empleados::find($idEmpleado);
                if ($empleado) {
                    $comprasAnteriores = $empleado->ventas ? json_decode($empleado->ventas, true) : [];
                    $nuevaCompra = [
                        'id_cliente' => $idCliente,
                        'id_compra' => $compraId,
                        'fecha' => now()->toDateTimeString(),
                        'productos' => $detallesCompra,
                        'total' => $total,
                        'id_metodo_pago' => $idMetodoPago,
                    ];
                    $comprasAnteriores[] = $nuevaCompra;
                    $empleado->ventas = json_encode($comprasAnteriores);
                    $empleado->save();
                }
            }

            return response()->json(['id_compra' => $compraId]);
        } else {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
    }


//API DE ACTULIZAR ESTADO DE COMPRA (1=>2)
    public function actualizarEstadoCompra(Request $request)
    {
        $idCompra = $request->input('id_compra');
        $nuevoEstado = $request->input('estado');

        if (!auth()->check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $empleado = auth()->user();
        $idEmpresa = $empleado->id_empresa;

        // Buscar la compra
        $compra = Compras::with('detalles')->find($idCompra);

        if (!$compra) {
            return response()->json(['mensaje' => 'Compra no encontrada'], 404);
        }

        // Verificar que la compra pertenece a la misma empresa del empleado
        if ($compra->id_empresa !== $idEmpresa) {
            return response()->json(['mensaje' => 'No tienes permiso para modificar esta compra'], 403);
        }

        // Verificar si ya no está en proceso
        if ($compra->estado != 1) {
            return response()->json(['mensaje' => 'La compra ya no está en proceso'], 400);
        }

        // Actualizar estado
        $compra->estado = $nuevoEstado;
        $compra->save();

        // Si el estado es 2 (finalizada), registrar historial en cliente
        if ($nuevoEstado == 2 && $compra->id_cliente) {
            $cliente = \App\Models\Clientes::find($compra->id_cliente);

            if ($cliente) {
                $historial = $cliente->compras ?? [];
                if (is_string($historial)) {
                    $historial = json_decode($historial, true);
                }

                foreach ($compra->detalles as $detalle) {
                    $historial[] = [
                        'id_producto' => $detalle->id_producto,
                        'cantidad' => $detalle->cantidad,
                        'precio' => $detalle->precio
                    ];
                }

                $cliente->compras = $historial;
                $cliente->save();
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

    public function reporteComprasConNombres(Request $request)
    {
        $idEmpresa = $request->query('id_empresa'); // Obtener el ID de la empresa desde la URL

        // Traer solo las compras de esa empresa con relaciones: empleado, producto, metodoPago
        $compras = Compras::with(['empleado', 'producto', 'metodoPago'])
            ->when($idEmpresa, function ($query, $idEmpresa) {
                $query->where('id_empresa', $idEmpresa);
            })
            ->get();

        $result = $compras->map(function ($compra) {
            return [
                'id' => $compra->id,
                'empleado' => $compra->empleado->name ?? 'N/A',
                'producto' => $compra->producto->name ?? 'N/A',
                'metodo_pago' => $compra->metodoPago->nombre ?? 'N/A',
                'total' => $compra->total,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }



}
