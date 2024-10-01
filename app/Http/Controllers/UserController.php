<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    
    // Display a listing of the users (Read - Index)
    public function index(Request $request)
    {
        // Set the number of items per page (from query or default)
        $perPage = $request->get('per_page', 10);

        // Start a query on the User model
        $query = User::query();

        // Iterate over all query parameters and apply them as filters if they match a column in the 'users' table
        foreach ($request->all() as $key => $value) {
            // Ensure we are not applying pagination parameters as filters
            if (in_array($key, ['page', 'per_page'])) {
                continue;
            }

            // Apply search to all columns dynamically
            if (Schema::hasColumn('users', $key)) {
                $query->where($key, 'LIKE', '%' . $value . '%');
            }
        }

        // Get the paginated data after filtering
        $users = $query->paginate($perPage);

        // Build the response
        $response = [
            'count' => $users->total(),  // Total number of users
            'next' => $users->nextPageUrl(),  // URL to the next page
            'previous' => $users->previousPageUrl(),  // URL to the previous page
            'results' => $users->items(),  // Current page of users
        ];

        // Return the customized response as JSON
        return response()->json($response, 200);
    }
    

    // Store a newly created user in the database (Create)
    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'salesperson_code' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ];

        // Custom error messages
        $messages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'email.unique' => 'This email address is already registered.',
            'salesperson_code.string' => 'The salesperson code must be a string.',
            'salesperson_code.max' => 'The salesperson code may not be greater than 255 characters.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];

        // Validate the request with custom messages
        $request->validate($rules, $messages);

        // Create the new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'salesperson_code' => $request->salesperson_code,
            'password' => Hash::make($request->password),
        ]);

        // Return the newly created user as JSON with status 201
        return response()->json($user, 201);
    }


    // Display the specified user (Read - Show)
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user, 200);
    }

    // Update the specified user in the database (Update)
    // Update the specified user in the database (Update)
    // Update the specified user in the database (Update)
public function update(Request $request, $id)
{
    // Find the user by ID
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Define the validation rules for all columns you want to allow updates for
    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        'salesperson_code' => 'nullable|string|max:255',
        'password' => 'nullable|string|min:8|confirmed',  // Optional: If user wants to update password
        'phone' => 'nullable|string|max:20',  // Example: Allow updating phone number
        'address' => 'nullable|string|max:255',  // Example: Allow updating address
    ];

    // Custom error messages (optional)
    $messages = [
        'name.required' => 'The name field is required.',
        'email.required' => 'The email field is required.',
        'email.unique' => 'This email is already taken.',
        'password.confirmed' => 'Password confirmation does not match.',
        'phone.string' => 'The phone number must be a valid string.',
        'address.string' => 'The address must be a valid string.',
    ];

    // Validate the request with custom messages
    $request->validate($rules, $messages);

    // Prepare the data to update, excluding any columns that were not provided in the request
    $updateData = [
        'name' => $request->name,
        'email' => $request->email,
        'salesperson_code' => $request->salesperson_code,
        'phone' => $request->phone,  // If updating phone number
        'address' => $request->address,  // If updating address
    ];

    // Only update password if it is provided
    if ($request->filled('password')) {
        $updateData['password'] = Hash::make($request->password);
    }

    // Update the user data
    $user->update($updateData);

    // Return the updated user as JSON
    return response()->json($user, 200);
}


    // Remove the specified user from the database (Delete)
    public function destroy($id)
    {
        // Attempt to find the user by ID
        $user = User::find($id);

        // If user is not found, return a 404 response with a custom message
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        try {
            // Delete the user from the database
            $user->delete();

            // Return a success message with the ID of the deleted user
            return response()->json([
                'message' => 'User deleted successfully',
                'user_id' => $user->id
            ], 200);

        } catch (\Exception $e) {
            // If there's an exception during the delete process, return a 500 error
            return response()->json([
                'message' => 'Failed to delete the user',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    

}
