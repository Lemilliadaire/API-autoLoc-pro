<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categorie;

class CategorieController extends Controller
{
    // use App\Models\Categorie;
    // use Illuminate\Http\Request;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Categorie::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'description' => 'nullable|string',
            'prix_base' => 'required|numeric',
        ]);

        $categorie = Categorie::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Catégorie créée avec succès',
            'categorie' => $categorie,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Categorie $categorie)
    {
        return $categorie;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categorie $categorie)
    {
        $validated = $request->validate([
            'nom' => 'string',
            'description' => 'nullable|string',
            'prix_base' => 'numeric',
        ]);

        $categorie->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Catégorie mise à jour avec succès',
            'categorie' => $categorie,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categorie $categorie)
    {
        $categorie->delete();

        return response()->json(null, 204);
    }
}
