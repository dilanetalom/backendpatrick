<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\User;
use App\Models\Site;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApiAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => $request->country,
            'city' => $request->city,
            'neighborhood' => $request->neighborhood,
            'phone' => $request->phone,
        ]);

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        
        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => "Ces identifiants ne correspondent pas à nos enregistrements."
            ], 401);
        }

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        
        return response()->json([
            'message' => 'Vous avez été déconnecté avec succès.'
        ], 200);
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'user' => $user,
            'message' => 'Utilisateur authentifié récupéré avec succès.'
        ], 200);
    }
    
    /**
     * Get all users.
     */
    public function getUsers()
    {
        $users = User::all();
        
        return response()->json([
            'data' => $users,
            'message' => 'Liste des utilisateurs récupérée avec succès.'
        ], 200);
    }

    /**
     * Get user by ID.
     */
    public function getByIdUser($id)
    { 
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé.'
            ], 404);
        }
        
        return response()->json([
            'data' => $user,
            'message' => 'Utilisateur récupéré avec succès.'
        ], 200);
    }

    /**
     * Update user details.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé.'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'firstName' => 'sometimes|string|max:255',
            'lastName' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->fill($request->only([
            'firstName', 'lastName', 'email', 'country', 'city', 'neighborhood', 'phone'
        ]));

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        return response()->json([
            'data' => $user,
            'message' => 'Utilisateur mis à jour avec succès.'
        ], 200);
    }
}
