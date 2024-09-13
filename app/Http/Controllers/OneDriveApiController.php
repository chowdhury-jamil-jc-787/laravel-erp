<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\UploadedFile;
use GuzzleHttp\Exception\ClientException;

class OneDriveApiController extends Controller
{
    public function handleOneDriveUpload(Request $request)
    {
        // Validate inputs
        $request->validate([
            'company_name' => 'required|string',
            'salesperson_code' => 'required|string',
            'file' => 'required|file'
        ]);

        $companyName = $request->input('company_name');
        $salespersonCode = $request->input('salesperson_code');
        $file = $request->file('file');
        $fileExtension = $file->getClientOriginalExtension();
        $baseFileName = '_working_' . Carbon::now()->format('Ymd') . '_' . $salespersonCode;
        $fileName = $baseFileName . '.' . $fileExtension;
        $fileContent = file_get_contents($file->getPathname());

        $accessToken = $this->getAccessToken(); // Optimized token retrieval

        // Ensure company folder exists, create if it doesn't
        $this->ensureCompanyFolderExists($accessToken, $companyName);

        // Find a unique file name if base already exists
        $fileName = $this->getUniqueFileName($accessToken, $companyName, $baseFileName, $fileExtension);

        // Upload the file to OneDrive
        $uploadResponse = $this->uploadFileToOneDrive($accessToken, $companyName, $fileName, $fileContent);

        // Save file information to database
        $this->saveFileInfo($companyName, $salespersonCode, $fileName, $uploadResponse['parentReference']['path'], $uploadResponse['@microsoft.graph.downloadUrl']);

        // Return response
        return response()->json([
            'status' => 'File uploaded successfully',
            'file_name' => $fileName,
            'file_location' => $uploadResponse['parentReference']['path'],
            'file_download_url' => $uploadResponse['@microsoft.graph.downloadUrl'],
        ], 200);
    }

    private function getAccessToken()
    {
        $client = new Client();
        $tokenResponse = $client->post('https://login.microsoftonline.com/' . env('MICROSOFT_TENANT_ID') . '/oauth2/v2.0/token', [
            'form_params' => [
                'client_id'     => env('MICROSOFT_CLIENT_ID'),
                'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
                'grant_type'    => 'password',
                'scope'        => 'Files.ReadWrite offline_access',
                'username'      => env('MICROSOFT_EMAIL'),
                'password'      => env('MICROSOFT_PASSWORD')
            ]
        ]);
        return json_decode($tokenResponse->getBody(), true)['access_token'];
    }

    private function ensureCompanyFolderExists($accessToken, $companyName)
    {
        $client = new Client();
        try {
            // Try to fetch the folder to check if it exists
            $client->get('https://graph.microsoft.com/v1.0/me/drive/root:/'.$companyName, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            ]);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                // Create the folder if not found
                $client->post('https://graph.microsoft.com/v1.0/me/drive/root/children', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'name' => $companyName,
                        'folder' => new \stdClass(),
                        '@microsoft.graph.conflictBehavior' => 'rename'
                    ]
                ]);
            } else {
                throw $e;
            }
        }
    }

    private function getUniqueFileName($accessToken, $companyName, $baseFileName, $fileExtension)
    {
        $client = new Client();
        $fileName = $baseFileName . '.' . $fileExtension;
        $counter = 1;

        try {
            $client->get('https://graph.microsoft.com/v1.0/me/drive/root:/'.$companyName.'/'.$fileName, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            ]);

            // If file exists, start numbering
            do {
                $counter++;
                $fileName = $baseFileName . $counter . '.' . $fileExtension;
                try {
                    $client->get('https://graph.microsoft.com/v1.0/me/drive/root:/'.$companyName.'/'.$fileName, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken,
                        ]
                    ]);
                } catch (ClientException $e) {
                    if ($e->getResponse()->getStatusCode() == 404) {
                        break;
                    }
                }
            } while (true);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() != 404) {
                throw $e;
            }
        }

        return $fileName;
    }

    private function uploadFileToOneDrive($accessToken, $companyName, $fileName, $fileContent)
    {
        $client = new Client();
        $uploadResponse = $client->put('https://graph.microsoft.com/v1.0/me/drive/root:/'.$companyName.'/'.$fileName.':/content', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/octet-stream',
            ],
            'body' => $fileContent
        ]);
        return json_decode($uploadResponse->getBody(), true);
    }

    private function saveFileInfo($companyName, $salespersonCode, $fileName, $fileLocation, $fileDownloadUrl)
    {
        $uploadedFile = new UploadedFile();
        $uploadedFile->company_name = $companyName;
        $uploadedFile->salesperson_code = $salespersonCode;
        $uploadedFile->file_name = $fileName;
        $uploadedFile->file_location = $fileLocation;
        $uploadedFile->file_download_url = $fileDownloadUrl;
        $uploadedFile->save();
    }
}
