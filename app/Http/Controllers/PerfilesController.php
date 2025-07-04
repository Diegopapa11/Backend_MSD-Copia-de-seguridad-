<?php

namespace App\Http\Controllers;

use App\Models\Perfiles;
use Illuminate\Http\Request;

class PerfilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perfil = Perfiles::all();
        //return view('perfiles.index', compact('perfiles')); se retorna a su vista de productos
        return response()->json($perfil); //se retorna como json
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_empleado' => 'required|int|exists:empleados,id',
            'foto_perfil' => 'required|string|max:100'
        ]);

        Perfiles::create($request->all());

        return redirect()->route('perfil.index')
            ->with('success', 'Perfil creado correctamente.');
    }


    /**
     * Display the specified resource.
     */
    public function show()
    {
        $perfil = Perfiles::all();
        return response()->json(['perfil' => $perfil]);    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Perfiles $perfil)
    {
        $request->validate([
            'producto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $perfil->update($request->all());

        return redirect()->route('perfil.index')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $perfil = Perfiles::findOrFail($id); // Encuentra al usuario por su ID
        $perfil->delete(); // Elimina al usuario de la base de datos

        return response()->json([
            'status' => true,
            'message' => 'Usuario eliminado correctamente',
        ]);
    }

       /**
     * Remove the specified resource from storage.
     */
    public function showPerfil()
    {
        
        // Paso 1: Verificar si el usuario está autenticado
        if (auth()->check()) {
            // Obtener el ID del usuario autenticado
            $empleadoId = auth()->user()->id;

            
            $perfil = Perfiles::where('id_empleado',$empleadoId)->first();
            //return view('perfiles.index', compact('perfiles')); se retorna a su vista de productos
            return response()->json($perfil); //se retorna como json
            
        }
        else {
            // Manejar el caso en el que el usuario no está autenticado
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

    }

}
