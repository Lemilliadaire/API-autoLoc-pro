<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    // use App\Models\Client;
    // use Illuminate\Http\Request;

    /**
     * la liste des clients.
     */
    public function index()
    {
        return Client::with('user')->get(); // Charger la relation 'user' avec chaque client
    } 

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'numero_permis' => 'required|string',
            'adresse' => 'required|string',
            'telephone' => 'required|string',
            'date_naissance' => 'required|date',
            'photo_profil' => 'nullable|image',
        ]);

        if ($request->hasFile('photo_profil')) {
            $validated['photo_profil'] = $request->file('photo_profil')->store('clients_photos', 'public');
        }

        $client = Client::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Client créé avec succès',
            'client' => $client,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return $client->load('user');
    } 

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'numero_permis' => 'string',
            'adresse' => 'string',
            'telephone' => 'string',
            'date_naissance' => 'date',
            'photo_profil' => 'nullable|image',
        ]);

        if ($request->hasFile('photo_profil')) {
            if ($client->photo_profil) {
                Storage::disk('public')->delete($client->photo_profil);
            }
            $validated['photo_profil'] = $request->file('photo_profil')->store('clients_photos', 'public');
        }

        $client->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Client mis à jour avec succès',
            'client' => $client,
        ], 200);
    }

    public function updatePhoto(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        if ($request->hasFile('photo_profil')) {
            if ($client->photo_profil) {
                Storage::disk('public')->delete($client->photo_profil);
            }
            $path = $request->file('photo_profil')->store('clients_photos', 'public');
            $client->photo_profil = $path;
            $client->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Photo mise à jour avec succès',
            'photo_url' => $client->photo_url,
            'client' => $client
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json(null, 204);
    }
}
