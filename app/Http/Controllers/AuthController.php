<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\SocietyDetail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        Log::info('Request Api Data:', $request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|unique:users,phone|min:10|max:15',
            'email' => ['nullable', 'email', 'max:255', Rule::unique(User::class)->whereNotNull('email')],
            'password' => 'required|string|min:6',
            'security_question_id' => ['required', 'integer', 'exists:security_questions,id'],
            'security_answer' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        if (!$request->email && !$request->phone) {
            return response()->json(['message' => 'Either email or phone is required'], 400);
        }

        if ($request->email) {
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                return response()->json(['message' => 'The email already registered'], 409); // 409 Conflict
            }
        }

        if ($request->phone) {
            $existsInSociety = SocietyDetail::where('owner1_mobile', $request->phone)
                ->orWhere('owner2_mobile', $request->phone)
                ->orWhere('owner3_mobile', $request->phone)
                ->exists();
            if (! $existsInSociety) {
                return response()->json(['message' => 'This mobile number is not associated with any apartment owner in a society.'], 409);
            }
        }
        $role_id = Role::where('role', 'Society User')->value('id');
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'security_question_id' => $request->security_question_id,
            'security_answer' => Hash::make($request->security_answer),
            'role_id' => $role_id
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        log::info('token' . $token);
        return response()->json(['token' => $token, 'user' => $user], 200);
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

        $validator = Validator::make($request->all(), [
            'identifier' => 'required', // Can be either email or phone
            'password' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $field = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if ($field === 'phone') {
            $mobile = $request->identifier;
            $user = User::with('role')->where('phone', $mobile)->first();

            if ($user && in_array($user->role->role, ['Super Admin', 'Admin'], true)) {
                if (! $user || ! Hash::check($request->password, $user->password)) {
                    return response()->json(['message' => 'The provided credentials are incorrect'], 401);
                }

                $user->tokens()->delete();
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json(['token' => $token, 'user' => $user], 200);
            }

            if ($user && $user->role->role === 'Society User') {
                if (! Hash::check($request->password, $user->password)) {
                    return response()->json(['message' => 'The provided credentials are incorrect'], 401);
                }

                $user->tokens()->delete();
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json(['token' => $token, 'user' => $user], 200);
            }

            $societyDetail = SocietyDetail::where('owner1_mobile', $mobile)
                ->orWhere('owner2_mobile', $mobile)
                ->orWhere('owner3_mobile', $mobile)
                ->first();

            $expectedPassword = null;
            if ($societyDetail && $societyDetail->status) {
                $statusData = json_decode($societyDetail->status, true);
                if (is_string($statusData)) {
                    $statusData = json_decode($statusData, true);
                }
                $expectedPassword = $statusData['password'] ?? null;

                if ($expectedPassword && hash_equals((string) $expectedPassword, (string) $request->password)) {
                    if ($societyDetail->owner1_mobile === $mobile) {
                        $matchedName = $societyDetail->owner1_name;
                        $matchedEmail = $societyDetail->owner1_email;
                    } elseif ($societyDetail->owner2_mobile === $mobile) {
                        $matchedName = $societyDetail->owner2_name;
                        $matchedEmail = $societyDetail->owner2_email;
                    } else {
                        $matchedName = $societyDetail->owner3_name;
                        $matchedEmail = $societyDetail->owner3_email;
                    }
                    $matchedEmail = !empty($matchedEmail) ? $matchedEmail : null;
                    $societyUserRoleId = Role::where('role', 'Society User')->value('id');

                    $user = User::firstOrCreate(
                        ['phone' => $mobile],
                        [
                            'name' => $matchedName ?: 'Society Owner',
                            'email' => $matchedEmail,
                            'password' => Hash::make($request->password),
                            'role_id' => $societyUserRoleId,
                        ]
                    );

                    if ($user->role_id !== $societyUserRoleId) {
                        $user->role_id = $societyUserRoleId;
                        $user->save();
                    }

                    if (! Hash::check($request->password, $user->password)) {
                        $user->password = Hash::make($request->password);
                        $user->save();
                    }

                    $user->tokens()->delete();
                    $token = $user->createToken('auth_token')->plainTextToken;
                    return response()->json(['token' => $token, 'user' => $user], 200);
                }
            }

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'The provided credentials are incorrect'], 401);
            }

            if ($user->role_id == Role::where('role', 'Society User')->value('id')) {
                $existsInSociety = SocietyDetail::where('owner1_mobile', $mobile)
                    ->orWhere('owner2_mobile', $mobile)
                    ->orWhere('owner3_mobile', $mobile)
                    ->exists();

                if (! $existsInSociety) {
                    return response()->json(['message' => 'This mobile number is not registered as an owner in any society.'], 401);
                }
            }

            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['token' => $token, 'user' => $user], 200);
        }

        $user = User::where($field, $request->identifier)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect'], 401);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user], 200);
    }

    public function mobile_login(Request $request)
    {
        Log::info('Login API Data', $request->all());
        // 1. Check API Secret Key from Header
        $secretKey = $request->header('X-API-KEY');
        Log::info('secretKey ' . $secretKey);
        Log::info('secretKey ' . config('app.api_secret_key'));
        if (!$secretKey) {
            return response()->json([
                'status' => false,
                'message' => 'API secret key missing'
            ], 401);
        }
        if ($secretKey !== config('app.api_secret_key')) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid API access key'
            ], 401);
        }

        // 2. Validate Request
        $validator = Validator::make($request->all(), [
            'phone' => 'required|digits:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }

        // 3. Check User
        $user1 = DB::connection('mysql_second')
            ->table('users')
            ->where('mobile_no', $request->phone)
            ->first();

        // $user = User::where('phone', $request->phone)->first();
        if (!$user1) {
            return response()->json([
                'status' => false,
                'message' => 'Mobile number not registered'
            ], 404);
        }

        // 4. Check Role
        $user2 = User::where('phone', $request->phone)->first();
        if ($user2->role_id == 3) {
            $existsInSociety = SocietyDetail::where('owner1_mobile', $request->phone)
                ->orWhere('owner2_mobile', $request->phone)
                ->orWhere('owner3_mobile', $request->phone)
                ->exists();

            if (!$existsInSociety) {
                return response()->json([
                    'status' => false,
                    'message' => 'This mobile number is not registered as an owner in any society.'
                ], 401);
            }
        }

        // 5. Generate Token
        $token = $user2->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => true,
            'token' => $token,
            'user' => $user1
        ], 200);
    }

    public function logout(Request $request)
    {
        // $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    // public function user()
    // {
    //     $user = Auth::user();
    //     return response()->json([
    //         'success' => true,
    //         'data'    => $user
    //     ]);
    // }
}
