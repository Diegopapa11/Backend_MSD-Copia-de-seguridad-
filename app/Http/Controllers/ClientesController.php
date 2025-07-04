<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ClientesController extends Controller
{
    /**
     * Obtener todos los clientes con paginación opcional
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');

            $query = Clientes::query();

            if ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            }

            $clientes = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $clientes->items(),
                'pagination' => [
                    'current_page' => $clientes->currentPage(),
                    'last_page' => $clientes->lastPage(),
                    'per_page' => $clientes->perPage(),
                    'total' => $clientes->total(),
                    'from' => $clientes->firstItem(),
                    'to' => $clientes->lastItem()
                ],
                'message' => 'Clientes obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener clientes',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Crear un nuevo cliente
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|min:2',
                'email' => 'required|email|unique:clientes,email|max:255',
                'compras' => 'required|numeric|min:0|max:999999.99',
                'antiguedad' => 'required|integer|min:0|max:100'
            ], [
                'name.required' => 'El nombre es obligatorio',
                'name.min' => 'El nombre debe tener al menos 2 caracteres',
                'email.required' => 'El email es obligatorio',
                'email.email' => 'El formato del email no es válido',
                'email.unique' => 'Este email ya está registrado',
                'compras.required' => 'El monto de compras es obligatorio',
                'compras.numeric' => 'El monto de compras debe ser un número',
                'antiguedad.required' => 'La antigüedad es obligatoria',
                'antiguedad.integer' => 'La antigüedad debe ser un número entero'
            ]);

            $cliente = Clientes::create($validatedData);

            return response()->json([
                'success' => true,
                'data' => $cliente,
                'message' => 'Cliente creado correctamente'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
                'status_code' => 422
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Obtener un cliente específico
     */
    public function show($id): JsonResponse
    {
        try {
            $cliente = Clientes::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $cliente,
                'message' => 'Cliente encontrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ], 404);
        }
    }

    /**
     * Actualizar un cliente existente
     *
     * @param Request $request
     * @param string|int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $cliente = Clientes::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255|min:2',
                'email' => 'sometimes|required|email|unique:clientes,email,' . $id . '|max:255',
                'compras' => 'sometimes|required|numeric|min:0|max:999999.99',
                'antiguedad' => 'sometimes|required|integer|min:0|max:100'
            ], [
                'name.min' => 'El nombre debe tener al menos 2 caracteres',
                'email.email' => 'El formato del email no es válido',
                'email.unique' => 'Este email ya está registrado',
                'compras.numeric' => 'El monto de compras debe ser un número',
                'antiguedad.integer' => 'La antigüedad debe ser un número entero'
            ]);

            $cliente->update($validatedData);

            return response()->json([
                'success' => true,
                'data' => $cliente->fresh(),
                'message' => 'Cliente actualizado correctamente'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
                'status_code' => 422
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado',
                'status_code' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Eliminar un cliente
     */
    public function destroy($id): JsonResponse
    {
        try {
            $cliente = Clientes::findOrFail($id);
            $cliente->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar cliente: ' . $e->getMessage()
            ], 500);
        }
    }
}
