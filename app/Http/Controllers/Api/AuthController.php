<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // Since we don't use Sanctum/Passport for now, we just return a success response
            // The desktop app just needs a 200 status and an access_token field
            return response()->json([
                'access_token' => 'dummy_token_' . bin2hex(random_bytes(16)),
                'token_type' => 'bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ], 200);
        }

        return response()->json([
            'detail' => 'Email atau password anda salah'
        ], 401);
    }
}
