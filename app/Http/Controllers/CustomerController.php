<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $customers = Customer::paginate($perPage);
    
        $response = [
            'message' => 'Customers retrieved successfully',
            'count' => $customers->total(),
            'current_page' => $customers->currentPage(),
            'last_page' => $customers->lastPage(),
            'per_page' => $customers->perPage(),
            'next_page_url' => $customers->nextPageUrl(),
            'prev_page_url' => $customers->previousPageUrl(),
            'data' => $customers->items()
        ];
    
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'territory_code' => 'required|string|max:10',
            'currency_code' => 'required|string|max:10',
            'post_code' => 'required|string|max:10',
            'county' => 'nullable|string|max:255',
            'abn' => 'required|string|max:15',
            'salesperson_code' => 'required|string|max:10',
            'country_region_code' => 'required|string|max:10',
            'location_code' => 'required|string|max:10',
            'phone_no' => 'required|string|max:15',
        ];
    
        // Define custom error messages
        $messages = [
            'name.required' => 'The name field is required.',
            'address.required' => 'The address field is required.',
            'city.required' => 'The city field is required.',
            'territory_code.required' => 'The territory code field is required.',
            'currency_code.required' => 'The currency code field is required.',
            'post_code.required' => 'The post code field is required.',
            'abn.required' => 'The ABN field is required.',
            'salesperson_code.required' => 'The salesperson code field is required.',
            'country_region_code.required' => 'The country/region code field is required.',
            'location_code.required' => 'The location code field is required.',
            'phone_no.required' => 'The phone number field is required.',
            // Add other custom messages as needed
        ];
    
        // Validate the request
        $validatedData = $request->validate($rules, $messages);
    
        // Create the customer
        $customer = Customer::create($validatedData);
    
        // Return success response
        $response = [
            'message' => 'Customer created successfully',
            'data' => $customer
        ];
    
        return response()->json($response, 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Attempt to find the customer by ID
        $customer = Customer::find($id);
    
        // Check if the customer exists
        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found',
                'error' => 'The customer with the specified ID does not exist.'
            ], 404);
        }
    
        // Return success response with the customer data
        return response()->json([
            'message' => 'Customer retrieved successfully',
            'data' => $customer
        ], 200);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Define validation rules
        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'territory_code' => 'sometimes|required|string|max:10',
            'currency_code' => 'sometimes|required|string|max:10',
            'post_code' => 'sometimes|required|string|max:10',
            'county' => 'nullable|string|max:255',
            'abn' => 'sometimes|required|string|max:15',
            'salesperson_code' => 'sometimes|required|string|max:10',
            'country_region_code' => 'sometimes|required|string|max:10',
            'location_code' => 'sometimes|required|string|max:10',
            'phone_no' => 'sometimes|required|string|max:15',
        ];
    
        // Define custom error messages
        $messages = [
            'name.required' => 'The name field is required.',
            'address.required' => 'The address field is required.',
            'city.required' => 'The city field is required.',
            'territory_code.required' => 'The territory code field is required.',
            'currency_code.required' => 'The currency code field is required.',
            'post_code.required' => 'The post code field is required.',
            'abn.required' => 'The ABN field is required.',
            'salesperson_code.required' => 'The salesperson code field is required.',
            'country_region_code.required' => 'The country/region code field is required.',
            'location_code.required' => 'The location code field is required.',
            'phone_no.required' => 'The phone number field is required.',
            // Add other custom messages as needed
        ];
    
        // Validate the request
        $validatedData = $request->validate($rules, $messages);
    
        // Find the customer
        $customer = Customer::findOrFail($id);
    
        // Update the customer with validated data
        $customer->update($validatedData);
    
        // Return success response
        $response = [
            'message' => 'Customer updated successfully',
            'data' => $customer
        ];
    
        return response()->json($response, 200);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Attempt to find the customer by ID
        $customer = Customer::find($id);
    
        // Check if the customer exists
        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found',
                'error' => 'The customer with the specified ID does not exist.'
            ], 404);
        }
    
        // Delete the customer
        $customer->delete();
    
        // Return success response
        return response()->json([
            'message' => 'Customer deleted successfully'
        ], 200);
    }
    
}
