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

    public function update(Request $request, ConferenceYear $Year)
    {
        $validated = $request->validate([
            'year' => 'required|integer|unique:years,year,' . $Year->id,
        ]);
        $Year->update($validated);
        return response()->json($Year);
    }

    public function destroy(ConferenceYear $conferenceYear)
    {
        $conferenceYear->delete();
        return response()->json(null, 204);
    }
}