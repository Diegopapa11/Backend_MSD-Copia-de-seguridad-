<?php


namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ProductosController extends Controller
{
    /**
     * Mostrar lista de todos los productos
     */
    public function index(Request $request)
    {
        try {
            // Obtener id_empresa del query string (por ejemplo: /api/P-index?id_empresa=3)
            $idEmpresa = $request->query('id_empresa');

            if (!$idEmpresa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Se requiere id_empresa para filtrar productos',
                ], 400);
            }

            $productos = Productos::where('id_empresa', $idEmpresa)->get();

            return response()->json([
                'success' => true,
                'data' => $productos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los productos',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    /**
     * Mostrar un producto específico
     */
    public function show($id)
    {
        try {
            $producto = Productos::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $producto
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Crear un nuevo producto
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_empresa' => 'required|string|exists:empresas,id'
        ], [
            'name.required' => 'El nombre del producto es obligatorio.',
            'price.required' => 'El precio del producto es obligatorio.',
            'stock.required' => 'El stock del producto existente es obligatorio.',
            'id-empresa.required' => 'El id de la empresa es obligatorio.',
            'id-empresa.exists' => 'La empresa seleccionada no existe en el sistema.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $validatedData = $validator->validated();

            $empresa = Empresa::where('nombre', $validatedData['id_empresa'])->first();

            if (!$empresa || !$empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener la empresa del usuario autenticado',
                ], 403);
            }

            $data = $request->only(['name', 'price', 'stock', 'description']);
            $data['id_empresa'] = $empresa->id;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('productos', $imageName, 'public');
                $data['image'] = $imagePath;
            }

            $producto = Productos::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente',
                'data' => $producto
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    /**
     * Actualizar un producto existente
     */
    public function update(Request $request, $id)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_empresa' => 'required|string|exists:empresas,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $producto = Productos::findOrFail($id);
            $data = $request->only(['name', 'price', 'stock', 'description']);

            // Manejo de imagen si se proporciona una nueva
            if ($request->hasFile('image')) {
                // Eliminar imagen anterior si existe
                if ($producto->image && Storage::disk('public')->exists($producto->image)) {
                    Storage::disk('public')->delete($producto->image);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('productos', $imageName, 'public');
                $data['image'] = $imagePath;
            }

            $producto->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado exitosamente',
                'data' => $producto->fresh()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un producto
     */
    public function destroy($id)
    {
        try {
            $producto = Productos::findOrFail($id);

            // Eliminar imagen asociada si existe
            if ($producto->image && Storage::disk('public')->exists($producto->image)) {
                Storage::disk('public')->delete($producto->image);
            }

            $producto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar productos por nombre
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetro de búsqueda requerido'
            ], 400);
        }

        try {
            $productos = Productos::where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->get();

            return response()->json([
                'success' => true,
                'data' => $productos
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
