<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CameraController extends Controller
{
    /**
     * Start camera for the current user's IP
     */
    public function start(Request $request)
    {
        $baseUrl = config('services.fastapi.url');
        
        if (!$baseUrl) {
            return response()->json([
                'success' => false,
                'message' => 'FastAPI service URL not configured'
            ], 500);
        }

        try {
            $response = Http::timeout(10)->post("{$baseUrl}/register/camera/start");
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to start camera: ' . ($response->json('message') ?? $response->body()),
                'error' => $response->json()
            ], $response->status());
            
        } catch (\Exception $e) {
            Log::error("Camera start failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Camera start failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stop camera for the current user's IP
     */
    public function stop(Request $request)
    {
        $baseUrl = config('services.fastapi.url');
        
        if (!$baseUrl) {
            return response()->json([
                'success' => false,
                'message' => 'FastAPI service URL not configured'
            ], 500);
        }

        try {
            $response = Http::timeout(10)->post("{$baseUrl}/register/camera/stop");
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to stop camera: ' . ($response->json('message') ?? $response->body()),
                'error' => $response->json()
            ], $response->status());
            
        } catch (\Exception $e) {
            Log::error("Camera stop failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Camera stop failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check camera status for the current user's IP
     */
    public function status(Request $request)
    {
        $baseUrl = config('services.fastapi.url');
        
        if (!$baseUrl) {
            return response()->json([
                'success' => false,
                'message' => 'FastAPI service URL not configured',
                'is_allowed' => false,
                'is_active' => false
            ], 500);
        }

        try {
            $response = Http::timeout(10)->get("{$baseUrl}/register/camera/status");
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get camera status',
                'is_allowed' => false,
                'is_active' => false,
                'error' => $response->body()
            ], $response->status());
            
        } catch (\Exception $e) {
            Log::error("Camera status check failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Camera status check failed: ' . $e->getMessage(),
                'is_allowed' => false,
                'is_active' => false
            ], 500);
        }
    }

    /**
     * Clear stopped status for the current user's IP
     */
    public function clearStop(Request $request)
    {
        $baseUrl = config('services.fastapi.url');
        
        if (!$baseUrl) {
            return response()->json([
                'success' => false,
                'message' => 'FastAPI service URL not configured'
            ], 500);
        }

        try {
            $response = Http::timeout(10)->post("{$baseUrl}/register/camera/clear-stop");
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear camera stop: ' . ($response->json('message') ?? $response->body())
            ], $response->status());
            
        } catch (\Exception $e) {
            Log::error("Camera clear stop failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Camera clear stop failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of all active IPs using the camera
     */
    public function activeIps(Request $request)
    {
        $baseUrl = config('services.fastapi.url');
        
        if (!$baseUrl) {
            return response()->json([
                'active_ips' => [],
                'stopped_ips' => []
            ]);
        }

        try {
            $response = Http::timeout(10)->get("{$baseUrl}/register/camera/active-ips");
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return response()->json([
                'active_ips' => [],
                'stopped_ips' => [],
                'error' => $response->body()
            ]);
            
        } catch (\Exception $e) {
            Log::error("Camera active IPs failed: " . $e->getMessage());
            return response()->json([
                'active_ips' => [],
                'stopped_ips' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get camera control panel view
     */
    public function controlPanel()
    {
        return view('tabs.camera.control');
    }
}