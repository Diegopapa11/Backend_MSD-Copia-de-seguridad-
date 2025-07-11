<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;

class EmpresaController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'password' => 'required|string',
        ]);

        $empresa = Empresa::where('nombre', $request->nombre)->first();

        if (!$empresa || !Hash::check($request->password, $empresa->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }
        $token = $empresa->createToken('empresa-token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'empresa' => [
                'id' => $empresa->id,
                'nombre' => $empresa->nombre,
                'rfc' => $empresa->rfc,
                'persona_moral' => $empresa->persona_moral,
                'token' => $token // si usas Sanctum
            ]
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:empresas,nombre',
            'rfc' => 'required|string|unique:empresas,rfc',
            'persona_moral' => 'required|boolean',
            'password' => 'required|string|min:6|confirmed',  // El campo password_confirmation debe llegar también
        ]);

        $empresa = Empresa::create([
            'nombre' => $validated['nombre'],
            'rfc' => $validated['rfc'],
            'persona_moral' => $validated['persona_moral'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Empresa registrada con éxito',
            'empresa' => $empresa
        ], 201);
    }
}
