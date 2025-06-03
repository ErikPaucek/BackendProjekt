<?php
namespace App\Http\Controllers;
use App\Models\Subpage;
use App\Models\ConferenceYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SubpageController
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

        $exists = Subpage::where('year_id', $validated['year_id'])
            ->whereRaw('LOWER(TRIM(title)) = ?', [mb_strtolower(trim($validated['title']))])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Podstránka s týmto názvom už existuje v tomto ročníku.'], 422);
        }

        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $i = 2;
        while (Subpage::where('year_id', $validated['year_id'])->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }
        $validated['slug'] = $slug;

        $subpage = Subpage::create($validated);
        return response()->json($subpage, 201);
    }

    public function showBySlugAndYear($year, $slug)
    {
        $conferenceYear = ConferenceYear::where('year', $year)->firstOrFail();
        $subpage = Subpage::where('slug', $slug)
            ->where('year_id', $conferenceYear->id)
            ->firstOrFail();
        return response()->json($subpage);
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

        $exists = Subpage::where('year_id', $validated['year_id'])
            ->whereRaw('LOWER(TRIM(title)) = ?', [mb_strtolower(trim($validated['title']))])
            ->where('id', '!=', $subpage->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Podstránka s týmto názvom už existuje v tomto ročníku.'], 422);
        }

        if ($subpage->title !== $validated['title']) {
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $i = 2;
            while (Subpage::where('year_id', $validated['year_id'])->where('slug', $slug)->where('id', '!=', $subpage->id)->exists()) {
                $slug = $originalSlug . '-' . $i;
                $i++;
            }
            $validated['slug'] = $slug;
        }

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