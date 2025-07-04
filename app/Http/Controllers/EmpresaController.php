<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmpresaController extends Controller
{
    /**
     * Registrar una nueva empresa.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'         => 'required|string|max:100|unique:empresas,nombre',
            'rfc'            => 'required|string|max:13|unique:empresas,rfc',
            'persona_moral'  => 'required|boolean',
            'password'       => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $empresa = Empresa::create([
            'nombre'         => $request->nombre,
            'rfc'            => $request->rfc,
            'persona_moral'  => $request->persona_moral,
            'password'       => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Empresa registrada correctamente',
            'empresa' => $empresa
        ], 201);
    }

    /**
     * Login de la empresa con nombre y contraseña.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'   => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $empresa = Empresa::where('nombre', $request->nombre)->first();

        if (!$empresa || !Hash::check($request->password, $empresa->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // Opcional: Generar token con Sanctum si lo usas
        // $token = $empresa->createToken('empresa-token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'empresa' => $empresa,
            // 'token' => $token // Descomenta si usas Sanctum
        ]);
    }
}
