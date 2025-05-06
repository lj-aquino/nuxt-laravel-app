<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('auth-token')->plainTextToken;
                
                return response()->json([
                    'data' => [
                        'user' => $user,
                        'token' => $token
                    ],
                    'error' => null
                ]);
            }

            return response()->json([
                'data' => null,
                'error' => ['message' => 'Invalid credentials']
            ], 401);
        } catch (\Exception $e) {
            Log::error('Login error', ['message' => $e->getMessage()]);
            
            return response()->json([
                'data' => null,
                'error' => ['message' => 'Server error: ' . $e->getMessage()]
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Revoke the token that was used to authenticate the current request
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
            }
            
            return response()->json([
                'data' => ['message' => 'Logged out successfully'],
                'error' => null
            ]);
        } catch (\Exception $e) {
            Log::error('Logout error', ['message' => $e->getMessage()]);
            
            return response()->json([
                'data' => null,
                'error' => ['message' => 'Server error: ' . $e->getMessage()]
            ], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            return response()->json([
                'data' => [
                    'user' => $request->user()
                ],
                'error' => null
            ]);
        } catch (\Exception $e) {
            Log::error('User fetch error', ['message' => $e->getMessage()]);
            
            return response()->json([
                'data' => null,
                'error' => ['message' => 'Server error: ' . $e->getMessage()]
            ], 500);
        }
    }
}