<?php
namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    public function index(Request $request)
    {
        // Obtener id_empresa, puede venir en query o token/autenticación
        $idEmpresa = $request->query('id_empresa');
        
        if (!$idEmpresa) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere id_empresa para listar los métodos de pago.'
            ], 400);
        }
        
        $metodosPago = MetodoPago::where('id_empresa', $idEmpresa)->get();
        return response()->json([
            'success' => true,
            'data' => $metodosPago
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'empresa_name' => 'required|exists:empresas,nombre',
        ], [
            'nombre.required' => 'El nombre del metodo pago es obligatorio.',
            'descripcion.required' => 'La descripción del metodo pago es obligatorio.',
            'empresa_name.required' => 'El nombre de la empresa es obligatorio.',
            'empresa_name.exists' => 'La empresa seleccionada no existe en el sistema.',
        ]);

         $empresa = \App\Models\Empresa::where('nombre', $validated['empresa_name'])->first();

        if (!$empresa) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa no encontrada.'
            ], 404);
        }

        // Crear el método de pago con el id_empresa
        $metodoPago = \App\Models\MetodoPago::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'id_empresa' => $empresa->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $metodoPago,
            'message' => 'Método de pago creado correctamente.'
        ], 201);
    }

    public function show($id)
    {
        $metodoPago = MetodoPago::find($id);

        if (!$metodoPago) {
            return response()->json([
                'success' => false,
                'message' => 'Método de pago no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $metodoPago,
        ]);
    }

    /**
     * Actualizar un método de pago existente.
     */
    public function update(Request $request, $id)
    {
        $metodoPago = MetodoPago::find($id);

        if (!$metodoPago) {
            return response()->json([
                'success' => false,
                'message' => 'Método de pago no encontrado',
            ], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:100|unique:metodos_pagos,nombre,' . $id,
            'descripcion' => 'nullable|string|max:255',
        ]);

        $metodoPago->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return response()->json([
            'success' => true,
            'data' => $metodoPago,
            'message' => 'Método de pago actualizado correctamente',
        ]);
    }

    /**
     * Eliminar un método de pago.
     */
    public function destroy($id)
    {
        $metodoPago = MetodoPago::find($id);

        if (!$metodoPago) {
            return response()->json([
                'success' => false,
                'message' => 'Método de pago no encontrado',
            ], 404);
        }

        $metodoPago->delete();

        return response()->json([
            'success' => true,
            'message' => 'Método de pago eliminado correctamente',
        ]);
    }
}
