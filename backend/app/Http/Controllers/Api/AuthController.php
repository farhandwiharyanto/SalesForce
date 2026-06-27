<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LoginHistory;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            $token = $user->createToken('auth-token')->plainTextToken;

            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'sign_in_time' => now(),
                'status' => 'Signed in'
            ]);

            return response()->json([
                'user' => $user,
                'token' => $token
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        
        $history = LoginHistory::where('user_id', $user->id)
            ->where('status', 'Signed in')
            ->orderBy('id', 'desc')
            ->first();
            
        if ($history) {
            $history->update([
                'sign_out_time' => now(),
                'status' => 'Signed out'
            ]);
        }

        $user->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
