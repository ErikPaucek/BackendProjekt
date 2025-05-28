<?php

namespace App\Http\Controllers;

use App\Models\Subpage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubpageController extends Controller
{
    public function index()
    {
        return response()->json(Subpage::all());
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'editor' && !$user->years->contains($request->year_id)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'year_id' => 'required|exists:years,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);
        $subpage = Subpage::create($validated);
        return response()->json($subpage, 201);
    }

    public function show(Subpage $subpage)
    {
        return response()->json($subpage);
    }

    public function update(Request $request, Subpage $subpage)
    {
        $user = Auth::user();
        if ($user->role === 'editor' && !$user->years->contains($subpage->year_id)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'year_id' => 'required|exists:years,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);
        $subpage->update($validated);
        return response()->json($subpage);
    }

    public function destroy(Subpage $subpage)
    {
        $user = Auth::user();
        if ($user->role === 'editor' && !$user->years->contains($subpage->year_id)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $subpage->delete();
        return response()->json(null, 204);
    }
}