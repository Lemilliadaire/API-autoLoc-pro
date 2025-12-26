<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voiture;
use App\Models\VoitureImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VoitureImageController extends Controller
{
    /**
     * Get all images for a specific car.
     */
    public function index(Voiture $voiture)
    {
        return response()->json([
            'status' => true,
            'images' => $voiture->images,
        ]);
    }

    /**
     * Store images for a specific car.
     */
    public function store(Request $request, Voiture $voiture)
    {
        $validated = $request->validate([
            'images' => 'required|array|max:20',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'type' => 'required|string|max:100',
        ]);

        $currentImageCount = $voiture->images()->count();
        $newImageCount = count($request->file('images'));

        if ($currentImageCount + $newImageCount > 20) {
            return response()->json([
                'status' => false,
                'message' => "Limite de 20 images atteinte. Vous ne pouvez ajouter que " . (20 - $currentImageCount) . " images supplémentaires.",
            ], 422);
        }

        $uploadedImages = [];
        foreach ($request->file('images') as $image) {
            $path = $image->store('voitures_gallery/' . $voiture->id, 'public');
            
            $voitureImage = VoitureImage::create([
                'voiture_id' => $voiture->id,
                'image_path' => $path,
                'type' => $validated['type'],
            ]);

            $uploadedImages[] = $voitureImage;
        }

        // Recharger la voiture avec toutes ses relations
        $voiture->load(['images', 'categorie', 'agence']);

        return response()->json([
            'status' => true,
            'message' => 'Images ajoutées avec succès à la galerie',
            'images' => $uploadedImages,
            'voiture' => $voiture,
        ], 201);
    }

    /**
     * Remove the specified image from the gallery.
     */
    public function destroy(VoitureImage $image)
    {
        $voitureId = $image->voiture_id;

        if ($image->image_path) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        // Recharger la voiture avec toutes ses relations
        $voiture = Voiture::with(['images', 'categorie', 'agence'])->find($voitureId);

        return response()->json([
            'status' => true,
            'message' => 'Image supprimée avec succès',
            'voiture' => $voiture,
        ], 200);
    }
}
