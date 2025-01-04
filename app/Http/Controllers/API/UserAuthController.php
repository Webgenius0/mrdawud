<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use App\Helper\Helper;
use App\Traits\apiresponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    use apiresponse;
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'language' => ['required', 'string', 'in:en,ar'],
            'city' => ['nullable', 'string', 'max:50'],
            'state' => ['nullable', 'string', 'max:50'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'age' => ['nullable', 'integer', 'min:14'],
            'phone' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $validated = $request->only([
                'username',
                'email',
                'password',
                'language',
                'city',
                'state',
                'age',
                'phone',
                'bio',
            ]);

            $validated['password'] = bcrypt($validated['password']);

            $user = User::create($validated);

            if ($request->hasFile('images')) {
                $userImages = [];
                foreach ($request->file('images') as $image) {
                    $url = Helper::fileUpload($image, 'users', $user->username . "-" . time());
                    array_push($userImages, [
                        'image' => $url
                    ]);
                }
                $user->images()->createMany($userImages); 
            }

            $this->generateOtp($user);
            DB::commit();
            return $this->success([
                'user' => $user->only('id', 'username', 'email', 'language', 'phone'),
            ], 'Check your email to verify your account', 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 400);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Attempt to log the user in
        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->error([], 'Invalid credentials', 401);
        }

        $user = Auth::user();
        if($user->email_verified_at == null){
            $this->generateOtp($user);
            return $this->error([], 'Check your email to verify your account', 401);
        }

        return $this->success([
            'token' => $this->respondWithToken($token),
            'user' => $user,
        ], 'User logged in successfully.', 200);
    }

    /**
     * Google Login
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }
        $credentials = $request->only('email');
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }
        $token = JWTAuth::fromUser($user);
        return $this->success([
            'token' => $this->respondWithToken($token),
        ], 'User logged in successfully.', 200);
    }

    /**
     * Forget Password Controller
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $this->generateOtp($user);


        return $this->success([], 'Check Your Email for Password Reset Otp', 200);
    }

    /**
     * Reset Password Controller
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email', 
            'password' => 'required|string|min:8|confirmed', 
            'otp' => 'required|numeric', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user->otp || !Hash::check($request->otp, $user->otp)) {
            return response()->json([
                'message' => 'Invalid OTP!',
            ], 400);
        }

        if ($user->otp_created_at && now()->gt(Carbon::parse($user->otp_created_at)->addMinutes(15))) {
            return response()->json([
                'message' => 'OTP has expired.',
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_created_at = null; 
        $user->save();

        return response()->json(['message' => 'Password reset successfully.'], 200);

    }

    // Resend Otp
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->error([], 'User not found', 404);
        }
        $this->generateOtp($user);
        return $this->success([], 'Check Your Email for Password Reset Otp', 200);
    }

    /**
     * Varify User Otp
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function varifyOtpWithOutAuth(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric', 
            'action' => 'required|in:email_verification,forgot_password', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if($request->action == 'email_verification') {
            if ($user->email_verified_at) {
                return response()->json([
                    'message' => 'Email already verified.',
                ], 400);
            }
            if (!Hash::check($request->otp, $user->otp)) {
                return response()->json([
                    'message' => 'Invalid OTP!',
                ], 400);
            }
            if ($user->otp_created_at && now()->gt(Carbon::parse($user->otp_created_at)->addMinutes(15))) {
                return response()->json([
                    'message' => 'OTP has expired.',
                ], 400);
            }            
            $user->email_verified_at = now();
            $user->otp = null;
            $user->otp_created_at = null;
            $user->save();
            return response()->json([
                'message' => 'Email verified successfully.',
            ], 200);
        }

        if($request->action == 'forgot_password') {
            if (!$user->otp || !Hash::check($request->otp, $user->otp)) {
                return response()->json([
                    'message' => 'Invalid OTP!',
                ], 400);
            }

            if ($user->otp_created_at && now()->gt(Carbon::parse($user->otp_created_at)->addMinutes(15))) {
                return response()->json([
                    'message' => 'OTP has expired.',
                ], 400);
            }

            return response()->json([
                'message' => 'OTP verified successfully.',
            ], 200);
        }
    }

    /**
     * Log out the user (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->success([
            'user' => Auth::user()->load('images'),
        ], 'User retrieved successfully', 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            // Refresh the token
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return $this->success([
                'access_token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ], 'Token refreshed successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 400);
        }
    }

    /**
     * Get Token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
