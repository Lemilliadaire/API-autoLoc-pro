<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paiement;

class PaiementController extends Controller
{
    // use App\Models\Paiement;
    // use Illuminate\Http\Request;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Paiement::with('reservation')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'montant' => 'required|numeric',
            'methode' => 'required|string',
            'statut' => 'required|string',
            'date_paiement' => 'required|date',
        ]);

        $paiement = Paiement::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Paiement créé avec succès',
            'paiement' => $paiement,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Paiement $paiement)
    {
        return $paiement->load('reservation');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paiement $paiement)
    {
        $validated = $request->validate([
            'montant' => 'numeric',
            'methode' => 'string',
            'statut' => 'string',
            'date_paiement' => 'date',
        ]);

        $paiement->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Paiement mis à jour avec succès',
            'paiement' => $paiement,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paiement $paiement)
    {
        $paiement->delete();

        return response()->json(null, 204);
    }
}
