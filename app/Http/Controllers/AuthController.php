<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        Log::info('Request Api Data:', $request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|unique:users,phone|min:10|max:15',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' =>$validator->errors()],400);
        }

        if (!$request->email && !$request->phone) {
            return response()->json(['message' => 'Either email or phone is required'], 400);
        }

        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json(['message' => 'The email already registered'], 409); // 409 Conflict
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['token' => $token,'user' => $user],200);
    }

    public function login(Request $request)
    {
        Log::info('Login API Data', $request->all());
        if ($request->isMethod('get')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request method. Please use POST for login.'
            ], 405); // 405 = Method Not Allowed
        }

        $validator = Validator::make($request->all(),[
            'identifier' => 'required', // Can be either email or phone
            'password' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json(['message' =>$validator->errors()],400);
        }

        $field = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        
        $user = User::where($field, $request->identifier)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect'], 401);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user],200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
