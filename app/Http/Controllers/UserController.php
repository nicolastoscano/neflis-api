<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function me(Request $request)
    {
        return $request->user();
    }

    public function register (Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create ([
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('userstoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    // LogIn
    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

    // Check account
        $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'incorrect password'
            ], 401);
        }


        $token = $user->createToken('userstoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    // Logout
    public function logout (Request $request) {
        $request->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }
}
