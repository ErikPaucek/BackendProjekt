<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;

class AuthController
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Poskytnuté prihlasovacie údaje sú nesprávne.'],
            ]);
        }

        $code = random_int(100000, 999999);
        $user->two_factor_code = $code;
        $user->two_factor_expires_at = now()->addMinutes(5);
        $user->save();

        Mail::raw("Your 2FA code is: $code", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your 2FA Code');
        });

        return response()->json([
            '2fa_required' => true,
            'user_id' => $user->id,
        ]);
    }

    public function verify2fa(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string',
        ]);

        $user = User::find($request->user_id);

        if (
            $user->two_factor_code === $request->code &&
            $user->two_factor_expires_at &&
            now()->lt($user->two_factor_expires_at)
        ) {
            $user->two_factor_code = null;
            $user->two_factor_expires_at = null;
            $user->save();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json(['message' => 'Invalid or expired code'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}