<?php

namespace App\Http\Controllers;

use App\Models\QuotationTracker;
use App\Models\Quote;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuoteController extends Controller
{
    // Store a new Quotation Tracker record
    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'date' => 'required|date',
            'companyName' => 'required|string',
            'quoteNo' => 'required|exists:quotation_trackers,id', // Ensures that quoteNo is a valid foreign key from the quotes table
            'tables' => 'required|array', // Only check that tables is an array, no further validation on its structure
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 400);
        }
    
        // Check if companyName exists in the customers table
        $customer = Customer::where('name', $request->companyName)->first();
        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'The company name does not exist in the customers table.',
            ], 400);
        }
    
        // Create a new QuotationTracker record
        $quotationTracker = Quote::create([
            'name' => $request->name,
            'date' => $request->date,
            'companyName' => $request->companyName,
            'quoteNo' => $request->quoteNo,
            'tables' => $request->tables, // Storing the array directly, regardless of structure
        ]);
    
        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Quotation Tracker created successfully.',
            'data' => $quotationTracker,
        ], 201);
    }
    

    // Retrieve all Quotation Trackers with pagination
    public function index(Request $request)
    {
        // Define the number of records per page (you can adjust this number)
        $perPage = 10;
        
        // Get the paginated data from the Quote model
        $quotationTrackers = Quote::paginate($perPage);

        // Prepare custom response format
        $response = [
            'count' => $quotationTrackers->total(), // Total number of records
            'next' => $quotationTrackers->nextPageUrl(), // URL for the next page, if available
            'previous' => $quotationTrackers->previousPageUrl(), // URL for the previous page, if available
            'results' => $quotationTrackers->items(), // The current page items
        ];

        // Return the response without wrapping it in "status" and "data"
        return response()->json($response);
    }



    // Show a specific Quotation Tracker
    public function show($id)
    {
        $quotationTracker = Quote::find($id);

        if (!$quotationTracker) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quotation Tracker not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $quotationTracker,
        ]);
    }

    // Update a Quotation Tracker
    public function update(Request $request, $id)
    {
        $quotationTracker = Quote::find($id);

        if (!$quotationTracker) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quotation Tracker not found',
            ], 404);
        }

        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'date' => 'required|date',
            'companyName' => 'required|string',
            'quoteNo' => 'required|exists:quotes,id',
            'tables' => 'required|json',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Check if companyName exists in the customers table
        $customer = Customer::where('name', $request->companyName)->first();
        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'The company name does not exist in the customers table.',
            ], 400);
        }

        // Update the Quotation Tracker record
        $quotationTracker->update([
            'name' => $request->name,
            'date' => $request->date,
            'companyName' => $request->companyName,
            'quoteNo' => $request->quoteNo,
            'tables' => $request->tables,
        ]);

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Quotation Tracker updated successfully.',
            'data' => $quotationTracker,
        ]);
    }

    // Delete a Quotation Tracker
    public function destroy($id)
    {
        $quotationTracker = QuotationTracker::find($id);

        if (!$quotationTracker) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quotation Tracker not found',
            ], 404);
        }

        // Delete the record
        $quotationTracker->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Quotation Tracker deleted successfully.',
        ]);
    }
}
