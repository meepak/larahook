<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ApiRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        // Get the username of the authenticated user
        $username = Auth::user()->username;

        // Fetch all API requests for the user
        $apiRequests = ApiRequest::where('username', $username)
            ->orderBy('created_at', 'desc') // Optional: Order by the latest requests
            ->get();

        // Return the dashboard view with the requests
        return view('dashboard', compact('apiRequests'));
    }


    /**
     * Log any request made to the specified username's endpoint.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \JsonException
     */
    public function logRequest(Request $request): \Illuminate\Http\JsonResponse
    {
        // Check if the username is valid
        $user = User::where('username', $request->username)->first();

        if (!$user) {
            // Ignore the request if the username is invalid
            return response()->json(['error' => 'Invalid username'], 404);
        }

        // Collect request data
        $requestData = [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
            'query_params' => $request->query(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'timestamp' => now(),
        ];

        // Handle file uploads if any
        $filesData = [];
        foreach ($request->file() as $key => $file) {
            $uuid = uniqid();
            // Generate a unique file name with the original extension
            $originalExtension = $file->getClientOriginalExtension();
            $storedFilename = $uuid . '.' . $originalExtension;

            // Store the file with the preserved extension
            $path = $file->storeAs('uploads', $storedFilename, 'public');

            $filesData[] = [
                'field_name' => $key,
                'uuid' => $uuid,
                'original_name' => $file->getClientOriginalName(),
                'stored_path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];
        }

        // Save the API request to the database
        ApiRequest::create([
            'username' => $request->username,
            'request_data' => json_encode($requestData, JSON_THROW_ON_ERROR),
            'files' => json_encode($filesData, JSON_THROW_ON_ERROR),
        ]);

        // Respond with a success message
        return response()->json(['status' => 'Request logged successfully']);
    }

    public function deleteRequest($id)
    {
        $request = ApiRequest::find($id);

        if (!$request) {
            return redirect()->back()->withErrors(['error' => 'Request not found.']);
        }

        // Delete associated files
        $files = json_decode($request->files, true);
        if ($files) {
            foreach ($files as $file) {
                Storage::disk('public')->delete($file['stored_path']);
            }
        }

        $request->delete();

        return redirect()->route('dashboard')->with('message', 'Request deleted successfully.');
    }

    public function previewRequest(Request $request)
    {
        $username = Auth::user()->username;

        // Find the specific request by ID and ensure it belongs to the authenticated user
        $apiRequest = ApiRequest::where('id', $request->id)
            ->where('username', $username)
            ->firstOrFail(); // Throws 404 if not found

        // Decode the `request_data` into an associative array
        $requestDetails = json_decode($apiRequest->request_data, true);
        $files = json_decode($apiRequest->files, true);

        // Retrieve all API requests for the user, ordered by creation date
        $apiRequests = ApiRequest::where('username', $username)
            ->orderBy('created_at', 'desc')
            ->get();

        // Pass both `requestDetails` and `apiRequests` to the view
        return view('dashboard', [
            'requestDetails' => $requestDetails,
            'files' => $files,
            'apiRequests' => $apiRequests,
        ]);
    }

    public function downloadFile($fileUuid)
    {
        // Check if the authenticated user owns the file
        $fileRecord = ApiRequest::where('username', Auth::user()->username)
            ->whereJsonContains('files', [['uuid' => $fileUuid]]) // Assuming file UUID is stored under 'field_name'
            ->first();

        if (!$fileRecord) {
            abort(403, 'Unauthorized access to the file.');
        }

        // Find the file details in the files JSON array
        $fileDetails = collect(json_decode($fileRecord->files, true))->firstWhere('uuid', $fileUuid);

        if (!$fileDetails) {
            abort(404, 'File not found.');
        }

        // Get the stored file path and original name
        $storedPath = $fileDetails['stored_path'];
        $originalName = $fileDetails['original_name'];

        // Ensure the file exists on disk
        if (!Storage::disk('public')->exists($storedPath)) {
            abort(404, 'File not found.');
        }

        // Serve the file for download with its original name
        return Storage::disk('public')->download($storedPath, $originalName);
    }


    public function deleteRequests(Request $request)
    {
        $ids = $request->input('ids', []);
        $count = count($ids);
        if($count > 0) {
            ApiRequest::whereIn('id', $ids)->delete();
            return redirect()->route('dashboard')->with('message', "$count Selected requests deleted successfully.");
        } 

        return redirect()->route('dashboard')->with('message', 'You didn\'t select any requests to delete.');
    }


}
