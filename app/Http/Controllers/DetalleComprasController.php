<?php

namespace App\Http\Controllers;

use App\Models\DetalleCompras;
use Illuminate\Http\Request;

class DetalleComprasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detalle_compras = DetalleCompras::all();
        //return view('users.index', compact('users')); retorna en una vista
        return response()->json($detalle_compras); //se retorna como json
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valida los datos del request
        $request->validate([
            'id_compra' => 'required|exists:compras,id',
            'cantidad' => 'required|numeric',
            'precio' => 'required|numeric',
        ]);

        // Crea un nuevo detalle de compra con los datos proporcionados
        $detalleCompra = DetalleCompras::create([
            'id_compra' => $request->input('id_compra'),
            'cantidad' => $request->input('cantidad'),
            'precio' => $request->input('precio'),
        ]);

        // Retorna una respuesta con el detalle de compra creado
        return response()->json(['status' => true, 'data' => $detalleCompra], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
        $detalle_compras = DetalleCompras::all();
        return response()->json(['status' => true, 'data' => $detalle_compras]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DetalleCompras $detalle_compras)
    {
        $detalle_compras = DetalleCompras::findOrFail($id); //lanzará una excepción ModelNotFoundException si no encuentra el modelo
        $detalle_compras->update($request->all());
        return redirect()->route('detallecompras.index')->with('success', 'DetalleCompra actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $detalle_compras = DetalleCompras::findOrFail($id); // Encuentra al usuario por su ID
        $detalle_compras->delete(); // Elimina al usuario de la base de datos

        return response()->json([
            'status' => true,
            'message' => 'Usuario eliminado correctamente',
        ]);
    }
}
