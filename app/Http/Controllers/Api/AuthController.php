<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    // enregistrer un nouvel utilisateur
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'password_confirm' => 'required|string|min:8|same:password',
            'role' => 'string|in:user,admin',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'lastname' => $validated['lastname'],
            'telephone' => $validated['telephone'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'password_confirm' => Hash::make($validated['password_confirm']),
            'role' => $validated['role'] ?? 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'utilisateur créé avec succès',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // connecter l'utilisateur et générer un token d'accès
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'connexion réussie',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // déconnecter l'utilisateur en supprimant le token actuel
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'déconnexion réussie'
        ]);
    }

    // montre les informations de l'utilisateur authentifié
    public function me(Request $request)
    {
        return $request->user();
    }

    // envoyer un lien de réinitialisation de mot de passe
    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Aucun utilisateur trouvé avec cet email',
            ], 404);
        }

        // Générer un token de réinitialisation
        $token = \Illuminate\Support\Str::random(64);

        // Stocker le token dans la base de données
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $validated['email']],
            [
                'email' => $validated['email'],
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // TODO: Envoyer l'email avec le token
        // Pour l'instant, on retourne le token directement (à des fins de développement)
        return response()->json([
            'status' => true,
            'message' => 'Lien de réinitialisation envoyé',
            'token' => $token, // À retirer en production
        ]);
    }

    // réinitialiser le mot de passe
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'status' => false,
                'message' => 'Token invalide',
            ], 400);
        }

        // Vérifier si le token correspond
        if (!Hash::check($validated['token'], $resetRecord->token)) {
            return response()->json([
                'status' => false,
                'message' => 'Token invalide',
            ], 400);
        }

        // Vérifier si le token n'a pas expiré (1 heure)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            return response()->json([
                'status' => false,
                'message' => 'Token expiré',
            ], 400);
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $validated['email'])->first();
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Supprimer le token
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Mot de passe réinitialisé avec succès',
        ]);
    }
}
