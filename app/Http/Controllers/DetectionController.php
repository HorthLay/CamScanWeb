<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DetectionController extends Controller
{
    public function index()
    {
        return view('tabs.detection.index');
    }

    /**
     * Process detection on a frame or stream
     */
    public function detect(Request $request)
    {
        $request->validate([
            'image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'image_data' => 'sometimes|string',
            'camera_ip' => 'sometimes|string',
            'stream_url' => 'sometimes|string|url',
        ]);

        $baseUrl = config('services.fastapi.url');

        if (!$baseUrl) {
            return response()->json([
                'success' => false,
                'message' => 'FastAPI service URL not configured'
            ], 500);
        }

        try {
            $payload = [];

            // Handle base64 encoded image data
            if ($request->has('image_data')) {
                $payload['image_data'] = $request->image_data;
            }
            // Handle file upload
            elseif ($request->hasFile('image')) {
                $imagePath = $request->file('image')->getRealPath();
                $imageData = base64_encode(file_get_contents($imagePath));
                $payload['image_data'] = $imageData;
            }

            // Add camera info if provided
            if ($request->has('camera_ip')) {
                $payload['camera_ip'] = $request->camera_ip;
            }
            if ($request->has('stream_url')) {
                $payload['stream_url'] = $request->stream_url;
            }

            $response = Http::timeout(30)->post("{$baseUrl}/detect", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return response()->json([
                'success' => false,
                'message' => 'Detection failed: ' . ($response->json('message') ?? $response->body()),
                'error' => $response->json()
            ], $response->status());

        } catch (\Exception $e) {
            Log::error("Detection failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Detection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start detection on a specific camera stream
     */
    public function startStreamDetection(Request $request)
    {
        $request->validate([
            'stream_url' => 'required|string',
            'camera_ip' => 'sometimes|string',
            'detection_type' => 'sometimes|string|in:face,object,motion,all',
        ]);

        $baseUrl = config('services.fastapi.url');

        if (!$baseUrl) {
            return response()->json([
                'success' => false,
                'message' => 'FastAPI service URL not configured'
            ], 500);
        }

        try {
            $payload = [
                'stream_url' => $request->stream_url,
                'detection_type' => $request->detection_type ?? 'face',
            ];

            if ($request->has('camera_ip')) {
                $payload['camera_ip'] = $request->camera_ip;
            }

            $response = Http::timeout(30)->post("{$baseUrl}/detect/stream/start", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to start stream detection: ' . ($response->json('message') ?? $response->body()),
                'error' => $response->json()
            ], $response->status());

        } catch (\Exception $e) {
            Log::error("Start stream detection failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Start stream detection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stop detection on a stream
     */
    public function stopStreamDetection(Request $request)
    {
        $request->validate([
            'stream_id' => 'sometimes|string',
            'camera_ip' => 'sometimes|string',
        ]);

        $baseUrl = config('services.fastapi.url');

        if (!$baseUrl) {
            return response()->json([
                'success' => false,
                'message' => 'FastAPI service URL not configured'
            ], 500);
        }

        try {
            $payload = [];
            if ($request->has('stream_id')) {
                $payload['stream_id'] = $request->stream_id;
            }
            if ($request->has('camera_ip')) {
                $payload['camera_ip'] = $request->camera_ip;
            }

            $response = Http::timeout(10)->post("{$baseUrl}/detect/stream/stop", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to stop stream detection: ' . ($response->json('message') ?? $response->body()),
                'error' => $response->json()
            ], $response->status());

        } catch (\Exception $e) {
            Log::error("Stop stream detection failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Stop stream detection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detection status
     */
    public function detectionStatus(Request $request)
    {
        $baseUrl = config('services.fastapi.url');

        if (!$baseUrl) {
            return response()->json([
                'success' => false,
                'message' => 'FastAPI service URL not configured',
                'is_detecting' => false
            ], 500);
        }

        try {
            $response = Http::timeout(10)->get("{$baseUrl}/detect/status");

            if ($response->successful()) {
                return $response->json();
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to get detection status',
                'is_detecting' => false,
                'error' => $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            Log::error("Detection status check failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Detection status check failed: ' . $e->getMessage(),
                'is_detecting' => false
            ], 500);
        }
    }

    /**
     * Validate camera stream URL
     */
    public function validateStream(Request $request)
    {
        $request->validate([
            'stream_url' => 'required|string',
        ]);

        // Basic URL validation
        $url = $request->stream_url;
        
        // Common stream URL patterns
        $patterns = [
            '/^rtsp:\/\//i',           // RTSP stream
            '/^http:\/\/.+\.m3u8/i',   // HLS stream
            '/^http:\/\/.+\.mp4/i',    // MP4 stream
            '/^http:\/\/.+\.flv/i',    // FLV stream
            '/^websocket:\/\//i',       // WebSocket stream
        ];

        $isValid = false;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                $isValid = true;
                break;
            }
        }

        // Also accept regular HTTP URLs (for webcam or MJPEG streams)
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            $isValid = true;
        }

        // Check if it's a local webcam (special case)
        if ($url === 'webcam' || $url === 'local' || $url === 'localhost') {
            return response()->json([
                'success' => true,
                'valid' => true,
                'type' => 'webcam',
                'message' => 'Local webcam access'
            ]);
        }

        return response()->json([
            'success' => true,
            'valid' => $isValid,
            'type' => $isValid ? 'stream' : 'unknown',
            'message' => $isValid ? 'Valid stream URL' : 'Invalid stream URL format'
        ]);
    }

    /**
     * Get detection results for a specific stream
     */
    public function getResults(Request $request)
    {
        $request->validate([
            'stream_id' => 'sometimes|string',
            'camera_ip' => 'sometimes|string',
            'limit' => 'sometimes|integer|min:1|max:100',
        ]);

        $baseUrl = config('services.fastapi.url');

        if (!$baseUrl) {
            return response()->json([
                'success' => false,
                'message' => 'FastAPI service URL not configured',
                'results' => []
            ], 500);
        }

        try {
            $query = [];
            if ($request->has('stream_id')) {
                $query['stream_id'] = $request->stream_id;
            }
            if ($request->has('camera_ip')) {
                $query['camera_ip'] = $request->camera_ip;
            }
            if ($request->has('limit')) {
                $query['limit'] = $request->limit;
            }

            $response = Http::timeout(10)->get("{$baseUrl}/detect/results", $query);

            if ($response->successful()) {
                return $response->json();
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to get detection results',
                'results' => [],
                'error' => $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            Log::error("Get detection results failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Get detection results failed: ' . $e->getMessage(),
                'results' => []
            ], 500);
        }
    }
}
