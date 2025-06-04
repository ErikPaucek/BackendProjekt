<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreatedMail;
use App\Mail\UserPasswordChangedMail;
use App\Mail\UserDeletedMail;

class UserController
{
    public function index(Request $request)
    {
        $role = $request->query('role');
        if ($role) {
            return response()->json(User::where('role', $role)->get());
        }
        return response()->json(User::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,editor',
        ]);
        $plainPassword = $validated['password'];
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        Mail::to($user->email)->send(new UserCreatedMail($user, $plainPassword));

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|in:admin,editor',
        ]);
        if (isset($validated['password'])) {
            $plainPassword = $validated['password'];
            $validated['password'] = Hash::make($validated['password']);
            $user->update($validated);

            Mail::to($user->email)->send(new UserPasswordChangedMail($user, $plainPassword));
        } else {
            $user->update($validated);
        }
        return response()->json($user);
    }

    public function destroy(User $user)
    {
        Mail::to($user->email)->send(new UserDeletedMail($user));
        $user->delete();
        return response()->json(null, 204);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Staré heslo nie je správne.'], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Heslo bolo úspešne zmenené.']);
    }
}