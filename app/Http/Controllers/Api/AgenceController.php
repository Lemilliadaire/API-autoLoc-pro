<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agence;

class AgenceController extends Controller
{
    // use App\Models\Agence;
    // use Illuminate\Http\Request;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Agence::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'adresse' => 'required|string',
            'telephone' => 'required|string',
            'ville' => 'required|string',
            'logo' => 'nullable|image',
        ]);

         $file = $request->file('logo');
        if ($file)
            $path = $file->store('logos', 'public');

        $agence = Agence::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Agence créée avec succès',
            'agence' => $agence,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Agence $agence)
    {
        return $agence;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agence $agence)
    {
        $validated = $request->validate([
            'nom' => 'string',
            'adresse' => 'string',
            'telephone' => 'string',
            'ville' => 'string',
            'logo' => 'nullable|image',
        ]);
            $file = $request->file('logo');
        if ($file)
            $path = $file->store('logos', 'public');

        $agence->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Agence mise à jour avec succès',
            'agence' => $agence,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agence $agence)
    {
        $agence->delete();

        return response()->json(null, 204);
    }
}
