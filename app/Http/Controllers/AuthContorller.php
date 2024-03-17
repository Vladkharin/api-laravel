<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthContorller extends Controller
{
    public function registration(RegistrationRequest $request)
    {
        $user = User::query()->create($request->validated());

        return response()->json([
            "success" => true,
            "message" => "Success",
            "token" => $user->createToken('api')->plainTextToken
        ], 201);
    }

    public function login(Request $request)
    {
        $attempt = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($attempt) {
            return response()->json([
                "success" => true,
                "message" =>  "$" . "uccess",
                "token" => Auth::user()->createToken('api')->plainTextToken
            ]);
        }

        return response()->json([
            "success" => false,
            "message" => "Login failed",
        ], 401);

    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            "success" => true,
            "message" => "logout",
        ], 201);
    }
}
