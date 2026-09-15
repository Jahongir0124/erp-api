<?php

namespace App\Http\Controllers;

use app\Enums\UserStatus;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{


    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'roles' => $request->user()->getRoleNames(),
            'permissions' => $request->user()
                ->getAllPermissions()
                ->pluck('name')
        ]);
    }
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'msg' => 'Bad Credentials'
            ], 401);
        }

        if ($user->status !== UserStatus::ACTIVE) {
            return response()->json([
                'msg' => 'Account is not active'
            ]);
        }
        $user->tokens()->delete();
        $token = $user->createToken(
            'api-token',
            ['*'],
            now()->addHours(2)
            )->plainTextToken;
        return response()->json([
            'token' => $token
        ]);
    }
    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'msg' => 'Logged out'
        ]);
    }
}

