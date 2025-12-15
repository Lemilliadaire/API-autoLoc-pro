<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    // use App\Models\Reservation;
    // use Illuminate\Http\Request;
    // use Illuminate\Support\Facades\Auth;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            return Reservation::with(['client', 'voiture', 'agenceRetrait', 'agenceRetour'])->get();
        }
        return Reservation::with(['client', 'voiture', 'agenceRetrait', 'agenceRetour'])
            ->whereHas('client', function ($query) {
                $query->where('user_id', Auth::id());
            })->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'voiture_id' => 'required|exists:voitures,id',
            'date_debut' => 'required|date|after:today',
            'date_fin' => 'required|date|after:date_debut',
            'prix_total' => 'required|numeric',
            'statut' => 'required|string',
            'agence_retrait_id' => 'required|exists:agences,id',
            'agence_retour_id' => 'required|exists:agences,id',
        ]);

        // Check availability
        $exists = Reservation::where('voiture_id', $validated['voiture_id'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('date_debut', [$validated['date_debut'], $validated['date_fin']])
                    ->orWhereBetween('date_fin', [$validated['date_debut'], $validated['date_fin']]);
            })->exists();

        if ($exists) {
            return response()->json(['message' => 'Car not available for these dates'], 422);
        }

        $reservation = Reservation::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Réservation créée avec succès',
            'reservation' => $reservation,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        return $reservation->load(['client', 'voiture', 'agenceRetrait', 'agenceRetour', 'paiement']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'date_debut' => 'date|after:today',
            'date_fin' => 'date|after:date_debut',
            'prix_total' => 'numeric',
            'statut' => 'string',
            'agence_retrait_id' => 'exists:agences,id',
            'agence_retour_id' => 'exists:agences,id',
        ]);

        $reservation->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Réservation mise à jour avec succès',
            'reservation' => $reservation,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return response()->json(null, 204);
    }
}
