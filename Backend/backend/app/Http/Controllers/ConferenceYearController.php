<?php
namespace App\Http\Controllers;
use App\Models\ConferenceYear;
use Illuminate\Http\Request;

class ConferenceYearController extends Controller
{
    public function index()
    {
        return response()->json(
            ConferenceYear::with('users')->get()->map(function ($year) {
                $arr = $year->toArray();
                $arr['editors'] = $year->users;
                unset($arr['users']);
                return $arr;
            })
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|unique:years,year',
        ]);
        $conferenceYear = ConferenceYear::create($validated);
        return response()->json($conferenceYear, 201);
    }

    public function show(ConferenceYear $conferenceYear)
    {
        $year = $conferenceYear->load(['users', 'subpages']);
        $arr = $year->toArray();
        $arr['editors'] = $year->users;
        unset($arr['users']);
        $arr['pages'] = $year->subpages;
        unset($arr['subpages']);
        return response()->json($arr);
    }

    public function update(Request $request, ConferenceYear $conferenceYear)
    {
        $validated = $request->validate([
            'year' => 'required|integer|unique:years,year,' . $conferenceYear->id,
        ]);
        $conferenceYear->update($validated);
        return response()->json($conferenceYear);
    }

    public function destroy(ConferenceYear $conferenceYear)
    {
        \Log::info('Mazem ročník', ['id' => $conferenceYear->id, 'year' => $conferenceYear->year]);
        $deleted = $conferenceYear->delete();
        \Log::info('Delete result', ['deleted' => $deleted]);
        return response()->json(['deleted' => $deleted], 200);
    }

    public function assignEditor(Request $request, ConferenceYear $conferenceYear)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        
        if ($conferenceYear->users()->where('user_id', $validated['user_id'])->exists()) {
            return response()->json(['message' => 'Editor už je priradený.'], 409);
        }
        $conferenceYear->users()->attach($validated['user_id']);
        return response()->json(['message' => 'Editor priradený.']);
    }

    public function removeEditor(ConferenceYear $conferenceYear, $userId)
    {
        $conferenceYear->users()->detach($userId);
        return response()->json(['message' => 'Editor odobratý.']);
    }
}