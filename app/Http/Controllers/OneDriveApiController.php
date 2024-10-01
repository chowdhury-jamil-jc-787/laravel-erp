<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\QuotationTracker;
use App\Models\UploadedFile;
use App\Models\Customer;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Log;

class OneDriveApiController extends Controller
{
    public function uploadFilesToOneDrive(Request $request)
{
    try {
        // Validate inputs
        $request->validate([
            'company_name' => 'required|string',
            'salesperson_code' => 'required|string',
            'files.*' => 'required|file' // Multiple files can be uploaded
        ]);

        $companyName = $request->input('company_name');
        $salespersonCode = $request->input('salesperson_code');
        $files = $request->file('files') ?? []; // Ensure $files is an array, even if no files are uploaded
        $todayFolder = Carbon::now()->format('Y-m-d'); // Today's date folder

        $accessToken = $this->getAccessToken(); // Optimized token retrieval

        // Ensure the folder structure exists (company and date folder)
        $folderExists = $this->ensureCompanyAndDateFoldersExist($accessToken, $companyName, $todayFolder);

        if (!$folderExists) {
            return response()->json(['error' => 'Could not create folder structure'], 500);
        }

        $fileNames = [];
        $fileDownloadUrls = [];
        $responses = [];

        foreach ($files as $file) {
            $originalFileName = $file->getClientOriginalName();
            $fileContent = file_get_contents($file->getPathname());

            // Check if the file already exists in OneDrive for the current date folder
            if ($this->fileExistsInOneDrive($accessToken, $companyName, $todayFolder, $originalFileName)) {
                // Handle case where file already exists
                $responses[] = [
                    'status' => 'error',
                    'message' => 'File already exists in OneDrive',
                    'file_name' => $originalFileName,
                ];
                continue; // Skip this file if it already exists
            }

            // Upload the file to OneDrive
            $uploadResponse = $this->uploadFileToOneDrive($accessToken, $companyName, $todayFolder, $originalFileName, $fileContent);

            // Store file names and download URLs in arrays for saving in database as JSON
            $fileNames[] = $originalFileName;
            $fileDownloadUrls[] = $uploadResponse['@microsoft.graph.downloadUrl'];

            // Add successful response
            $responses[] = [
                'status' => 'File uploaded successfully',
                'file_name' => $originalFileName,
                'file_location' => $uploadResponse['parentReference']['path'],
                'file_download_url' => $uploadResponse['@microsoft.graph.downloadUrl'],
            ];
        }

        // If at least one file was uploaded, save the information to the database
        if (!empty($fileNames)) {
            $this->saveFileInfo($companyName, $salespersonCode, $fileNames, $uploadResponse['parentReference']['path'], $fileDownloadUrls);
        }

        return response()->json($responses, 200);
    } catch (ClientException $e) {
        // Handle Guzzle ClientException (such as errors from OneDrive)
        $errorMessage = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
        Log::error('OneDrive API ClientException: ' . $errorMessage);
        return response()->json(['error' => 'OneDrive API error', 'details' => $errorMessage], 500);
    } catch (RequestException $e) {
        // Handle RequestException (e.g., network issues, invalid requests)
        $errorMessage = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
        Log::error('OneDrive API RequestException: ' . $errorMessage);
        return response()->json(['error' => 'Request error', 'details' => $errorMessage], 500);
    } catch (Exception $e) {
        // Handle any other general exceptions
        Log::error('General error: ' . $e->getMessage());
        return response()->json(['error' => 'An unexpected error occurred', 'details' => $e->getMessage()], 500);
    }
}



    private function ensureCompanyAndDateFoldersExist($accessToken, $companyName, $todayFolder)
    {
        $client = new Client();
        $companyFolderExists = false;
        $todayFolderExists = false;

        // Step 1: Check if the company folder exists, if not, create it
        try {
            $response = $client->get('https://graph.microsoft.com/v1.0/me/drive/root:/'.$companyName, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            ]);
            $companyFolderExists = true; // Company folder exists
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                // If company folder does not exist, create it
                try {
                    $client->post('https://graph.microsoft.com/v1.0/me/drive/root/children', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'name' => $companyName,
                            'folder' => new \stdClass(), // Empty object to indicate folder creation
                            '@microsoft.graph.conflictBehavior' => 'rename'
                        ]
                    ]);
                    $companyFolderExists = true; // Company folder successfully created
                } catch (ClientException $e) {
                    Log::error('Error creating company folder: ' . $e->getResponse()->getBody()->getContents());
                    throw $e; // Handle errors for folder creation
                }
            } else {
                throw $e;
            }
        }

        // Step 2: Check if today's date folder exists within the company folder
        if ($companyFolderExists) {
            try {
                $client->get('https://graph.microsoft.com/v1.0/me/drive/root:/'.$companyName.'/'.$todayFolder, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ]
                ]);
                $todayFolderExists = true; // Today's date folder exists
            } catch (ClientException $e) {
                if ($e->getResponse()->getStatusCode() == 404) {
                    // If today's folder does not exist, create it within the company folder
                    try {
                        $client->post('https://graph.microsoft.com/v1.0/me/drive/root:/'.$companyName.':/children', [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $accessToken,
                                'Content-Type' => 'application/json',
                            ],
                            'json' => [
                                'name' => $todayFolder,
                                'folder' => new \stdClass(), // Empty object to indicate folder creation
                                '@microsoft.graph.conflictBehavior' => 'rename'
                            ]
                        ]);
                        $todayFolderExists = true; // Today's date folder successfully created
                    } catch (ClientException $e) {
                        Log::error('Error creating date folder: ' . $e->getResponse()->getBody()->getContents());
                        throw $e; // Handle errors for folder creation
                    }
                } else {
                    throw $e;
                }
            }
        }

        return $todayFolderExists; // Return whether today's folder exists or is created
    }

    private function fileExistsInOneDrive($accessToken, $companyName, $todayFolder, $fileName)
    {
        $client = new Client();
        try {
            $client->get('https://graph.microsoft.com/v1.0/me/drive/root:/'.$companyName.'/'.$todayFolder.'/'.$fileName, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            ]);
            return true; // File exists
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                return false; // File does not exist
            }
            throw $e; // Other errors should be handled appropriately
        }
    }

    private function uploadFileToOneDrive($accessToken, $companyName, $todayFolder, $fileName, $fileContent)
    {
        $client = new Client();
        $uploadResponse = $client->put('https://graph.microsoft.com/v1.0/me/drive/root:/'.$companyName.'/'.$todayFolder.'/'.$fileName.':/content', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/octet-stream',
            ],
            'body' => $fileContent
        ]);
        return json_decode($uploadResponse->getBody(), true);
    }

    private function saveFileInfo($companyName, $salespersonCode, array $fileNames, $fileLocation, array $fileDownloadUrls)
    {
        // Step 1: Save the file info to the UploadedFile model
        $uploadedFile = new UploadedFile();
        $uploadedFile->company_name = $companyName;
        $uploadedFile->salesperson_code = $salespersonCode;
        $uploadedFile->file_name = json_encode($fileNames);  // Store array as JSON
        $uploadedFile->file_location = $fileLocation;
        $uploadedFile->file_download_url = json_encode($fileDownloadUrls);  // Store array as JSON
        $uploadedFile->save();
    
        // Step 2: Find the customer_id by looking up the company name in the customers table
        $customer = Customer::where('name', $companyName)->first();
    
        if (!$customer) {
            throw new \Exception('Customer not found');
        }
    
        // Step 3: Save the info to the QuotationTracker model
        $quotationTracker = new QuotationTracker();
        $quotationTracker->date = Carbon::now(); // Today's date
        $quotationTracker->customer_id = $customer->id; // Customer ID from the customer record
        $quotationTracker->REP = $salespersonCode; // Salesperson code (same as REP column)
        $quotationTracker->item_quantity = null; // Null value for item quantity
        $quotationTracker->price = null; // Null value for price
        $quotationTracker->Done_by = null; // Null value for Done_by
        $quotationTracker->Progress = '0%'; // Default progress as 0%
        $quotationTracker->start_date = Carbon::now(); // Today's date for start_date
        $quotationTracker->end_date = null; // Null value for end_date
        $quotationTracker->remarks = null; // Null value for remarks
        $quotationTracker->win_status = null; // Default win_status as "pending"
        $quotationTracker->Location = $fileLocation; // File location for Location column
        $quotationTracker->win_date = null; // Null value for win_date
        $quotationTracker->download_link = json_encode($fileDownloadUrls); // File download links stored as JSON
        $quotationTracker->uploaded_file_id = $uploadedFile->id; // Set the uploaded file ID
    
        $quotationTracker->save();
    }

    private function getAccessToken()
    {
        try {
            $client = new Client();
            $tokenResponse = $client->post('https://login.microsoftonline.com/' . env('MICROSOFT_TENANT_ID') . '/oauth2/v2.0/token', [
                'form_params' => [
                    'client_id'     => env('MICROSOFT_CLIENT_ID'),
                    'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
                    'grant_type'    => 'password',  // Or 'authorization_code' if using OAuth flow
                    'scope'         => 'Files.ReadWrite offline_access', // Request Files.ReadWrite permission
                    'username'      => env('MICROSOFT_EMAIL'),  // User email or username
                    'password'      => env('MICROSOFT_PASSWORD') // User password
                ]
            ]);

            return json_decode($tokenResponse->getBody(), true)['access_token'];
        } catch (ClientException $e) {
            // Catch specific errors from the token request
            Log::error('Error fetching access token: ' . $e->getMessage());
            throw new Exception('Error fetching access token.');
        }
    }
    

    public function getQuotationTrackers(Request $request)
    {
        // Paginate the results (10 per page)
        $perPage = 10; // You can adjust the per-page limit
        $quotations = QuotationTracker::with(['customer', 'uploadedFile'])->paginate($perPage);

        // Format the output
        $results = $quotations->map(function ($quotation) {
            return [
                'id' => $quotation->id,
                'date' => $quotation->date,
                'customer' => [
                    'id' => $quotation->customer->id,
                    'name' => $quotation->customer->name,
                    'address' => $quotation->customer->address,
                    'city' => $quotation->customer->city,
                    'territory_code' => $quotation->customer->territory_code,
                ],
                'uploaded_file' => $quotation->uploadedFile ? [
                    'id' => $quotation->uploadedFile->id,
                    'file_name' => $quotation->uploadedFile->file_name,
                    'file_location' => $quotation->uploadedFile->file_location,
                    'file_download_url' => $quotation->uploadedFile->file_download_url,
                ] : null,
                'REP' => $quotation->REP,
                'item_quantity' => $quotation->item_quantity,
                'price' => $quotation->price,
                'done_by' => $quotation->Done_by,
                'remarks' => $quotation->remarks,
                'progress' => $quotation->Progress,
                'location' => $quotation->Location,
                'win_status' => $quotation->win_status,
                'start_date' => $quotation->start_date,
                'end_date' => $quotation->end_date,
                'download_link' => $quotation->download_link,
            ];
        });

        // Build the pagination output
        return response()->json([
            'count' => $quotations->total(),
            'next' => $quotations->nextPageUrl(),
            'previous' => $quotations->previousPageUrl(),
            'results' => $results,
        ]);
    }

    public function updateQuotationTracker(Request $request, $id)
    {
        // Custom error messages
        $messages = [
            'item_quantity.integer' => 'Item quantity must be an integer.',
            'price.numeric' => 'Price must be a valid number.',
            'Done_by.integer' => 'The Done_by field must reference a valid user ID.',
            'Done_by.exists' => 'The selected Done_by user does not exist.',
            'Progress.string' => 'Progress must be a valid string.',
            'start_date.date' => 'Start date must be a valid date.',
            'end_date.date' => 'End date must be a valid date.',
            'win_status.string' => 'Win status must be a valid string.',
            'win_date.date' => 'Win date must be a valid date.',
        ];

        // Validate the request to ensure valid data
        $request->validate([
            'item_quantity' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'Done_by' => 'nullable|string|exists:users,salesperson_code',  // Assumes Done_by references a user ID
            'Progress' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'win_status' => 'nullable|string|max:255',
            'win_date' => 'nullable|date',
        ], $messages);

        // Find the QuotationTracker by ID
        $quotationTracker = QuotationTracker::find($id);

        if (!$quotationTracker) {
            return response()->json(['error' => 'Quotation Tracker not found'], 404);
        }

        // Update only the fields that are provided in the request
        $quotationTracker->item_quantity = $request->input('item_quantity', $quotationTracker->item_quantity);
        $quotationTracker->price = $request->input('price', $quotationTracker->price);
        $quotationTracker->Done_by = $request->input('Done_by', $quotationTracker->Done_by);
        $quotationTracker->Progress = $request->input('Progress', $quotationTracker->Progress);
        $quotationTracker->start_date = $request->input('start_date', $quotationTracker->start_date);
        $quotationTracker->end_date = $request->input('end_date', $quotationTracker->end_date);
        $quotationTracker->remarks = $request->input('remarks', $quotationTracker->remarks);
        $quotationTracker->win_status = $request->input('win_status', $quotationTracker->win_status);
        $quotationTracker->win_date = $request->input('win_date', $quotationTracker->win_date);

        // Save the updated record
        $quotationTracker->save();

        // Return a success response with the updated record
        return response()->json([
            'message' => 'Quotation Tracker updated successfully',
            'data' => $quotationTracker
        ], 200);
    }
}
