<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Signature;

class SignatureController extends Controller
{
    /**
     * Display a listing of the signatures.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        // Paginate the signatures with related user data and order by created_at in descending order
        $signatures = Signature::with('user')->orderBy('created_at', 'desc')->paginate($perPage);

        // Transform the paginated items to the desired structure
        $results = $signatures->map(function ($signature) {
            return [
                'id' => $signature->id,
                'user_id' => $signature->user_id,
                'name' => $signature->user->name,  // Fetching name from users table
                'position' => $signature->position,
                'dir' => $signature->dir,
                'phone' => $signature->phone,
                'fax' => $signature->fax,
                'email' => $signature->user->email,  // Fetching email from users table
                'created_at' => $signature->created_at,
                'updated_at' => $signature->updated_at,
            ];
        });

        return response()->json([
            'count' => $signatures->total(),
            'next' => $signatures->nextPageUrl(),
            'previous' => $signatures->previousPageUrl(),
            'results' => $results
        ]);
    }


    /**
     * Store a newly created signature in the database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'position' => 'required|string|max:255',
            'dir' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'fax' => 'nullable|string|max:15',
        ]);

        $signature = Signature::create($validatedData);

        return response()->json([
            'message' => 'Signature created successfully!',
            'signature' => $signature
        ], 201);
    }

    /**
     * Display the specified signature.
     */
    public function show($id)
    {
        $signature = Signature::find($id);

        if (!$signature) {
            return response()->json(['message' => 'Signature not found'], 404);
        }

        return response()->json($signature, 200);
    }

    /**
     * Update the specified signature in the database.
     */
    public function update(Request $request, $id)
    {
        $signature = Signature::find($id);
    
        if (!$signature) {
            return response()->json(['message' => 'Signature not found'], 404);
        }
    
        // Use the `sometimes` validation rule to allow partial updates
        $validatedData = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'position' => 'sometimes|string|max:255',
            'dir' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:15',
            'fax' => 'nullable|string|max:15',
        ]);
    
        // Update the signature with only the provided data
        $signature->update($validatedData);
    
        return response()->json([
            'message' => 'Signature updated successfully!',
            'signature' => $signature
        ], 200);
    }
    

    /**
     * Remove the specified signature from the database.
     */
    public function destroy($id)
    {
        $signature = Signature::find($id);

        if (!$signature) {
            return response()->json(['message' => 'Signature not found'], 404);
        }

        $signature->delete();

        return response()->json(['message' => 'Signature deleted successfully'], 200);
    }
}
