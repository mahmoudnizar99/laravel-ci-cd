<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use App\Models\EmailVerificationCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendVerificationCode;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:18',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'age' => $request->age,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(30); 

    // Store OTP
    EmailVerificationCode::updateOrCreate(
        ['email' => $user->email],
        [
            'code' => $otp,
            'expires_at' => $expiresAt
        ]
    );

    // Send OTP 
    Mail::to($user->email)->send(new SendVerificationCode($otp));

    return response()->json(['message' => 'OTP sent to your email. Please verify to continue.'], 201);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $verification = EmailVerificationCode::where('email', $request->email)
            ->where('code', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verification) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->email_verified_at = now();
        $user->save();

        // Delete the used OTP
        $verification->delete();

        return response()->json(['message' => 'Email verified successfully']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email not verified'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}