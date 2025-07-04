<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permiso = Permiso::all();
        return response()->json($permiso); //se retorna como json
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'clave_permiso' => 'required|string|max:10',
            'decription' => 'required|string|max:255',
        ]);

        Permiso::create($request->all());

        return redirect()->route('permiso.index')
            ->with('success', 'Permiso creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $permiso = Permiso::all();
        return response()->json(['status' => true, 'data' => $permiso]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permiso $permiso)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'clave_permiso' => 'required|string|max:10',
            'decription' => 'required|string|max:255',
        ]);

        $permiso->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Permiso actualizado correctamente.',
            'data' => $permiso->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permiso $permiso)
    {
        try {
            $permiso->delete();

            return response()->json([
                'status' => true,
                'message' => 'Permiso eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar permiso',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}