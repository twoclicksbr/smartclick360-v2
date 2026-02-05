<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // Criar token de acesso
        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'status' => 200,
            'endpoint' => $request->method() . ' ' . $request->path(),
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'person' => $user->person,
                ],
                'token' => $token,
            ],
            'message' => 'Login realizado com sucesso',
        ]);
    }

    /**
     * Register
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenant.users,email',
            'password' => 'required|string|min:6|confirmed',
            'birthdate' => 'nullable|date',
        ]);

        // Criar pessoa
        $person = Person::create([
            'name' => $request->name,
            'birthdate' => $request->birthdate,
            'order' => 0,
        ]);

        // Criar usuário
        $user = User::create([
            'person_id' => $person->id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Criar token de acesso
        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'status' => 201,
            'endpoint' => $request->method() . ' ' . $request->path(),
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'person' => $person,
                ],
                'token' => $token,
            ],
            'message' => 'Registro criado com sucesso',
        ], 201);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'status' => 200,
            'endpoint' => $request->method() . ' ' . $request->path(),
            'success' => true,
            'message' => 'Logout realizado com sucesso',
        ]);
    }

    /**
     * User autenticado
     */
    public function me(Request $request)
    {
        return response()->json([
            'status' => 200,
            'endpoint' => $request->method() . ' ' . $request->path(),
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $request->user()->id,
                    'email' => $request->user()->email,
                    'person' => $request->user()->person,
                ],
            ],
        ]);
    }
}
