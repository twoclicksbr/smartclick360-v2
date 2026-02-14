<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Landlord\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LandlordAuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'sometimes|string|max:255',
        ]);

        $user = User::where('email', $request->email)
            ->where('status', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->unauthorized('Credenciais inválidas');
        }

        $deviceName = $request->device_name ?? 'web';
        $token = $user->createToken($deviceName)->plainTextToken;

        $user->load('person');

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'person' => [
                    'id' => $user->person->id,
                    'first_name' => $user->person->first_name,
                    'surname' => $user->person->surname,
                ],
            ],
        ], 'Login realizado com sucesso');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logout realizado com sucesso');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('person');

        return $this->success([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'person' => [
                    'id' => $user->person->id,
                    'first_name' => $user->person->first_name,
                    'surname' => $user->person->surname,
                ],
            ],
        ]);
    }
}
