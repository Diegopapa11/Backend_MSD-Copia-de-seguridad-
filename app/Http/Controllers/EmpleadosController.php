<?php

namespace App\Http\Controllers;

use App\Models\Empleados;
use App\Models\Compras;
use App\Models\DetalleCompra;
use App\Models\Perfil;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmpleadosController extends Controller
{
    /**
     * Listar todos los empleados.
     */
    public function index()
    {
        return response()->json(Empleados::all());
    }

    /**
     * Registrar un nuevo empleado con perfil, permiso fijo (id = 2) y ventas desde Compras.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:80|unique:empleados,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $empleado = Empleados::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // Crear perfil asociado al empleado
        Perfil::create([
            'empleado_id' => $empleado->id,
            // otros campos del perfil si es necesario, como 'telefono' => $request->telefono
        ]);

        // Asignar permiso con id = 2 (relación many-to-many)
        $empleado->permisos()->attach(2);

        // Generar ventas desde la tabla Compras (solo para mostrar)
        $ventas = $this->generarVentasDesdeCompras($empleado->id);

        return response()->json([
            'empleado' => $empleado,
            'ventas' => $ventas
        ], 201);
    }

    /**
     * Mostrar un empleado específico.
     */
    public function show(Empleados $empleado)
    {
        return response()->json($empleado);
    }

    /**
     * Actualizar los datos de un empleado y mostrar sus ventas.
     */
    public function update(Request $request, Empleados $empleado)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:100',
            'email'    => 'sometimes|required|email|max:80|unique:empleados,email,' . $empleado->id,
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $empleado->update([
            'name'     => $request->name ?? $empleado->name,
            'email'    => $request->email ?? $empleado->email,
            'password' => $request->filled('password')
                ? Hash::make($request->password)
                : $empleado->password,
        ]);

        $ventas = $this->generarVentasDesdeCompras($empleado->id);

        return response()->json([
            'empleado' => $empleado,
            'ventas' => $ventas
        ]);
    }

    /**
     * Eliminar un empleado.
     */
    public function destroy(Empleados $empleado)
    {
        if ($empleado->perfil) {
            $empleado->perfil()->delete();
        }
        $empleado->delete();
        return response()->json(['message' => 'Empleado eliminado correctamente']);
    }

    /**
     * Generar el array de ventas desde la tabla Compras.
     */
    private function generarVentasDesdeCompras($empleadoId)
    {
        return Compras::where('id_empleado', $empleadoId)
            ->get()
            ->map(function ($compra) {
                return [
                    'id_detalle_compras' => $compra->id_detalle_compras,
                    'fecha_compra'       => optional($compra->fecha_compra)->format('Y-m-d'),
                    'estado'             => $compra->estado,
                    'metodo_pago_id'     => $compra->metodo_pago_id,
                ];
            })
            ->toArray();
    }
}
