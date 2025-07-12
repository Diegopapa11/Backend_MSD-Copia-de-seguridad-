<?php

namespace App\Http\Controllers;

use App\Models\Empleados;
use App\Models\Perfil_x_Permiso;
use App\Models\Perfiles;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

    public function showRegisterForm()
    {
        return view('auth.register');
    }


    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:empleados,email',
            'password' => 'required|string|min:8',
            'empresa_name' => 'required|string|exists:empresas,nombre',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'empresa_name.required' => 'El nombre de la empresa es obligatorio.',
            'empresa_name.exists' => 'La empresa seleccionada no existe en el sistema.',
        ]);

        // Buscar empresa por nombre
        $empresa = Empresa::where('nombre', $validatedData['empresa_name'])->first();

        if (!$empresa) {
            return response()->json(['error' => 'Empresa no encontrada.'], 404);
        }

        // Crear el empleado sin id_perfil aún
        $user = Empleados::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'id_permiso' => 2,
            'id_empresa' => $empresa->id, // Asignar empresa
        ]);

        // Crear el perfil
        $perfil = Perfiles::create([
            'id_empleado' => $user->id,
            'id_empresa' => $user->id_empresa,
            'foto_perfil' => null,
        ]);

        // Asignar el perfil al empleado
        $user->id_perfil = $perfil->id;
        $user->save();

        // Registrar relación perfil-permiso
        Perfil_x_Permiso::create([
            'id_perfil' => $perfil->id,
            'id_permisos' => $user->id_permiso,
        ]);

        // Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'perfil' => $perfil,
            'empresa' => $empresa->nombre,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }







    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-zA-Z]/', 
                'regex:/\d/', 
                'regex:/[\W_]/',
            ],
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe incluir al menos una letra, un número y un carácter especial.',
        ]);

        if (!Auth::attempt($validatedData)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        $perfilController = new PerfilesController();
        $perfil = $perfilController->showPerfil();

        // Obtener información de empresa y permiso
        $empresa = $user->empresa ? $user->empresa->nombre : null;
        $permiso = $user->permiso ? $user->permiso->nombre : null;

        return response()->json([
            'success' => true,
            'user' => $user,
            'perfil' => $perfil,
            'empresa' => $empresa,
            'permiso' => $permiso,
            'access_token' => $token,
            'ventas' => $user->ventas,
            'token_type' => 'Bearer',
        ]);
    }


    public function logout()
    {
        if (auth()->check()) {
            auth()->user()->token()->revoke();
            return response()->json(['message' => 'Sesión cerrada con éxito']);
        } else {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
    }


}
