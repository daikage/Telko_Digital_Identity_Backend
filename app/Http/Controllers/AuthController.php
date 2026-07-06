<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'username' => 'required|string|unique:users',
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'avatar_url' => 'nullable|string',
            'avatar_base64' => 'nullable|string',
            'skills' => 'nullable|array',
        ]);

        $password = \Illuminate\Support\Str::random(24);
        $avatarUrl = $request->avatar_url;

        if ($request->filled('avatar_base64')) {
            $base64 = $request->input('avatar_base64');
            @list($type, $file_data) = explode(';', $base64);
            @list(, $file_data)      = explode(',', $file_data);
            if ($file_data) {
                $image = base64_decode($file_data);
                $imageName = 'avatars/' . \Illuminate\Support\Str::random(40) . '.png';
                \Illuminate\Support\Facades\Storage::disk('public')->put($imageName, $image);
                $avatarUrl = url('storage/' . $imageName);
            }
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->username, // Default name to username
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($password),
            ]);

            \App\Models\Profile::create([
                'user_id' => $user->id,
                'headline' => $request->headline,
                'bio' => $request->bio,
                'avatar_url' => $avatarUrl,
                'skills' => $request->skills ?? [],
            ]);

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            throw $e;
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user,
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
