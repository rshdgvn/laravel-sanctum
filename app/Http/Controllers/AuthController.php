<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register (Request $request) 
    {
        $validated = $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed']
        ]);

        $user = User::create($validated);

        $token = $user->createToken($request->name);

        return response()->json([
            'message' => 'Registered successfully',
            'user' => $user,
            'token' => $token->plainTextToken,
        ]);
    }

    public function login (Request $request) 
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users'],
            'password' => ['required']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ]);
        }


        $token = $user->createToken($user->name);

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken
        ]);
    }

    public function logout (Request $request) 
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'You are logged out'
        ]);
    }
}
