<?php

namespace App\Http\Controllers;

use App\Models\PerfilxPermiso;
use Illuminate\Http\Request;

class PerfilXPermisoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perfilxPermiso = PerfilxPermiso::all();
        //return view('perfiles.index', compact('perfiles')); se retorna a su vista de productos
        return response()->json($perfilxPermiso); //se retorna como json
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'id_perfil' => 'required|string|max:255',
            'id_permiso' => 'required|string|max:255',
        ]);

        PerfilxPermiso::create($request->all());

        return redirect()->route('perfilxpermiso.index')
            ->with('success', 'PerfilxPermiso creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $perfilxPermiso = PerfilxPermiso::all();
        return response()->json(['status' => true, 'data' => $perfilxPermiso]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PerfilxPermiso $perfilxPermiso)
    {
        $request->validate([
            'id_perfil' => 'required|string|max:255',
            'id_permiso' => 'required|string|max:255',
        ]);

        $perfilxPermiso->update($request->all());

        return redirect()->route('perfilxpermiso.index')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $perfilxPermiso = PerfilxPermiso::findOrFail($id); // Encuentra al usuario por su ID
        $perfilxPermiso->delete(); // Elimina al usuario de la base de datos

        return response()->json([
            'status' => true,
            'message' => 'Usuario eliminado correctamente',
        ]);
    }
}