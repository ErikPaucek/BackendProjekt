<?php

namespace App\Http\Controllers;

use App\Models\ConferenceYear;
use Illuminate\Http\Request;

class ConferenceYearController extends Controller
{
    public function index()
    {
        return response()->json(ConferenceYear::all());
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
        return response()->json($conferenceYear);
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
}