<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voiture;
use Illuminate\Http\Request;

class VoitureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Voiture::with(['categorie', 'agence']);

        if ($request->has('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        if ($request->has('agence_id')) {
            $query->where('agence_id', $request->agence_id);
        }

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        return response()->json($query->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'immatriculation' => 'required|string|unique:voitures|max:100',
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'annee' => 'required|integer|min:1900|max:' . date('Y'),
            'couleur' => 'required|string|max:100',
            'prix_journalier' => 'required|numeric|min:0',
            'statut' => 'required|string|in:disponible,reserve,en_service',
            'kilometrage' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'agence_id' => 'required|exists:agences,id',
            'photo' => 'nullable|image',

        ]);

         $file = $request->file('photo');
        if ($file)
            $path = $file->store('photos', 'public');

        $voiture = Voiture::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Voiture créée avec succès',
            'voiture' => $voiture,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Voiture $voiture)
    {
        return response()->json($voiture->load(['categorie', 'agence']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Voiture $voiture)
    {
        $validated = $request->validate([
            'immatriculation' => 'string|unique:voitures,immatriculation,' . $voiture->id,
            'marque' => 'string',
            'modele' => 'string',
            'annee' => 'integer',
            'couleur' => 'string',
            'prix_journalier' => 'numeric',
            'statut' => 'string',
            'kilometrage' => 'integer',
            'categorie_id' => 'exists:categories,id',
            'agence_id' => 'exists:agences,id',
            'photo' => 'nullable|image',
        ]);

            $file = $request->file('photo');
        if ($file)
            $path = $file->store('photos', 'public');

        $voiture->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Voiture mise à jour avec succès',
            'voiture' => $voiture,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voiture $voiture)
    {
        $voiture->delete();

        return response()->json(null, 204);
    }
}
