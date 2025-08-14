<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Services\UserService;

class AuthController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        Log::info('Request Api Data:', $request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|unique:users,phone|min:10|max:15',
            'email' => ['nullable', 'email','max:255',Rule::unique(User::class)->whereNotNull('email')],
            'password' => 'required|string|min:6',
            'security_question_id'=>['required', 'integer','exists:security_questions,id'],
            'security_answer'=>'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' =>$validator->errors()],400);
        }

        if (!$request->email && !$request->phone) {
            return response()->json(['message' => 'Either email or phone is required'], 400);
        }

        if($request->email){
        $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                return response()->json(['message' => 'The email already registered'], 409); // 409 Conflict
            }
        }

        if($request->phone){
            $existsInSociety = \App\Models\SocietyDetail::where('owner1_mobile', $request->phone)
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
            'security_question_id'=> $request->security_question_id,
            'security_answer'=> Hash::make($request->security_answer),
            'role_id'=> $role_id
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        log::info('token'. $token);
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

        if($field=='phone'){
        $userRole = \App\Models\User::where('phone',$request->identifier)->value('role_id');
            if($userRole==3){
            $existsInSociety = \App\Models\SocietyDetail::where('owner1_mobile', $request->identifier)
                ->orWhere('owner2_mobile', $request->identifier)
                ->orWhere('owner3_mobile', $request->identifier)
                ->exists();

                if (!$existsInSociety) {
                    return response()->json(['message' => 'This mobile number is not registered as an owner in any society.'], 401);
                }
            }
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

    public function user()
    {
        $user = $this->userService->getAuthenticatedUser();
        return response()->json([
            'success' => true,
            'data'    => $user
        ]);
    }
}
