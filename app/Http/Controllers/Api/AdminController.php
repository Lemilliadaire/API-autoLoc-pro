<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Admin::with('user')->get();
    }


    // création d'un administrateur
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:admins,user_id',
            'photo_profil' => 'nullable|image',
        ]);

        if ($request->hasFile('photo_profil')) {
            $validated['photo_profil'] = $request->file('photo_profil')->store('admins_photos', 'public');
        }

        $admin = Admin::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Profil administrateur créé avec succès',
            'admin' => $admin,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'photo_profil' => 'nullable|image',
        ]);

        if ($request->hasFile('photo_profil')) {
            if ($admin->photo_profil) {
                Storage::disk('public')->delete($admin->photo_profil);
            }
            $validated['photo_profil'] = $request->file('photo_profil')->store('admins_photos', 'public');
        }

        $admin->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Profil administrateur mis à jour avec succès',
            'admin' => $admin,
        ], 200);
    }

    public function updatePhoto(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        
        $request->validate([
            'photo' => 'required|image'
        ]);

        if ($request->hasFile('photo')) {
            if ($admin->photo_profil) {
                Storage::disk('public')->delete($admin->photo_profil);
            }
            $path = $request->file('photo')->store('admins_photos', 'public');
            $admin->photo_profil = $path;
            $admin->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Photo mise à jour avec succès',
            'photo_url' => $admin->photo_url,
            'admin' => $admin
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        return $admin->load('user');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        if ($admin->photo_profil) {
            Storage::disk('public')->delete($admin->photo_profil);
        }
        $admin->delete();

        return response()->json(null, 204);
    }
}
