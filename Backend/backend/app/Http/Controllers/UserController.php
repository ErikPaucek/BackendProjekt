<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Zoznam používateľov
    public function index(Request $request)
    {
        $role = $request->query('role');
        if ($role) {
            return response()->json(User::where('role', $role)->get());
        }
        return response()->json(User::all());
    }

    // Pridanie editora/admina
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,editor',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        return response()->json($user, 201);
    }

    // Úprava používateľa
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|in:admin,editor',
        ]);
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }
        $user->update($validated);
        return response()->json($user);
    }

    // Zmazanie používateľa
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}