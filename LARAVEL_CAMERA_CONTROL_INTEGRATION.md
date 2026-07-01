# Laravel Camera Control Integration Guide

## Overview

This guide explains how the IP-based camera control system has been integrated into the Laravel CamScanWeb application. The system now allows multiple IPs to use the camera simultaneously, with proper access control.

## What Was Implemented

### 1. Backend Changes (PHP)

#### New Controller: `CameraController.php`
- **Location**: `app/Http/Controllers/CameraController.php`
- **Purpose**: Proxy requests to FastAPI camera control endpoints
- **Methods**:
  - `start()` - Start camera for current IP
  - `stop()` - Stop camera for current IP  
  - `status()` - Check camera access status for current IP
  - `clearStop()` - Clear stopped status for current IP
  - `activeIps()` - Get list of all active and stopped IPs
  - `controlPanel()` - Show camera control panel

#### New Routes
- **Location**: `routes/web.php`
- **Routes Added**:
  ```php
  Route::get('/camera/control', [CameraController::class, 'controlPanel'])->name('camera.control');
  Route::post('/camera/start', [CameraController::class, 'start'])->name('camera.start');
  Route::post('/camera/stop', [CameraController::class, 'stop'])->name('camera.stop');
  Route::get('/camera/status', [CameraController::class, 'status'])->name('camera.status');
  Route::post('/camera/clear-stop', [CameraController::class, 'clearStop'])->name('camera.clear-stop');
  Route::get('/camera/active-ips', [CameraController::class, 'activeIps'])->name('camera.active-ips');
  ```

### 2. Frontend Changes (JavaScript)

#### Updated: `search_capture.blade.php`
- **Location**: `resources/views/tabs/users/search_capture.blade.php`
- **Changes**:
  - Added IP-based camera control to `startSearchStream()` function
  - Added IP-based camera stop to `stopSearchStream()` function
  - Added toast notification function `showSearchToast()`
  - Modified `runFaceSearch()` to use async/await for camera startup

#### New View: `control.blade.php`
- **Location**: `resources/views/tabs/camera/control.blade.php`
- **Purpose**: Full camera control panel with IP management
- **Features**:
  - Real-time camera status for current IP
  - Start/Stop/Clear buttons with proper state management
  - Live camera feed preview
  - Active users table showing all IPs using camera
  - Automatic status polling
  - Clean UI with Bootstrap styling

### 3. Configuration

The system uses existing FastAPI URL configuration:
- **Config**: `config/services.php` (already configured)
- **Environment**: `.env` file with `FASTAPI_URL=http://127.0.0.1:8001`

## How It Works

### Scenario 1: User A Opens Camera
1. User A (IP: 192.168.1.100) clicks "Start Camera" or "SEARCH & IDENTIFY FACE"
2. Laravel calls `/camera/start` route
3. CameraController proxies to FastAPI `/register/camera/start`
4. FastAPI checks if IP is allowed, starts camera for this IP
5. User A can now see live camera feed and use face search

### Scenario 2: User B Opens Camera While User A is Using It
1. User B (IP: 192.168.1.101) clicks "Start Camera"
2. Laravel calls `/camera/start` route
3. CameraController proxies to FastAPI `/register/camera/start`
4. FastAPI checks if IP is allowed (User A is active, but User B is new)
5. User B can start camera and use it simultaneously
6. Both users can use camera at the same time

### Scenario 3: User A Stops Camera
1. User A clicks "Stop Camera"
2. Laravel calls `/camera/stop` route
3. CameraController proxies to FastAPI `/register/camera/stop`
4. FastAPI removes User A's IP from active IPs, adds to stopped IPs
5. Camera hardware remains active for User B
6. User A cannot restart camera while User B is still using it

### Scenario 4: User A Tries to Restart Camera
1. User A clicks "Start Camera" again
2. Laravel calls `/camera/status` to check permission
3. FastAPI returns `is_allowed: false` because User B is still active
4. User A sees error message: "Camera is being used by another IP"
5. User A is blocked from restarting

### Scenario 5: User B Stops Camera
1. User B clicks "Stop Camera"
2. Laravel calls `/camera/stop` route
3. FastAPI removes User B's IP from active IPs
4. Since no IPs are active, camera hardware is released
5. User A can now restart camera

## API Integration Details

### Camera Control API (Laravel Routes)

| Method | Route | Description | FastAPI Endpoint |
|--------|-------|-------------|------------------|
| GET | `/camera/control` | Camera control panel UI | - |
| POST | `/camera/start` | Start camera for current IP | `/register/camera/start` |
| POST | `/camera/stop` | Stop camera for current IP | `/register/camera/stop` |
| GET | `/camera/status` | Check current IP status | `/register/camera/status` |
| POST | `/camera/clear-stop` | Clear stopped status | `/register/camera/clear-stop` |
| GET | `/camera/active-ips` | List all active IPs | `/register/camera/active-ips` |

### JavaScript Integration Example

```javascript
// Start camera with IP control
async function startCamera() {
    try {
        // Check status first
        const statusResponse = await fetch('/camera/status', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        const statusData = await statusResponse.json();
        
        if (!statusData.is_allowed) {
            alert(statusData.message || 'Camera access denied');
            return false;
        }
        
        // Start camera
        const startResponse = await fetch('/camera/start', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const startData = await startResponse.json();
        
        if (startData.success) {
            // Camera started, can now access video feed
            videoFeed.src = 'http://127.0.0.1:8001/video_feed?' + Date.now();
            return true;
        } else {
            alert(startData.message);
            return false;
        }
        
    } catch (error) {
        console.error('Camera start failed:', error);
        alert('Error starting camera');
        return false;
    }
}

// Stop camera with IP control
async function stopCamera() {
    try {
        const response = await fetch('/camera/stop', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Camera stopped for this IP
            videoFeed.src = '';
            return true;
        } else {
            console.error('Camera stop failed:', data.message);
            return false;
        }
        
    } catch (error) {
        console.error('Error stopping camera:', error);
        return false;
    }
}
```

## UI Components

### 1. Camera Control Panel
**Access**: `/camera/control` (requires `detection` tab access)

Features:
- Real-time status indicator for current IP
- Start/Stop/Clear buttons with proper state management
- Live camera feed preview
- Active users table
- Automatic status polling every 5 seconds
- Responsive design

### 2. Search & Identify Page
**Access**: `/users` (requires `users` tab access)

Updated Features:
- IP-based camera control in "SEARCH & IDENTIFY FACE" button
- Start/Stop camera toggle button with IP tracking
- Error handling for camera access denial
- Toast notifications for camera status changes

## Configuration

### Environment Variables

Ensure your `.env` file has:
```env
FASTAPI_URL=http://127.0.0.1:8001
```

### FastAPI Configuration

The FastAPI service (`CamScan`) should be running at the configured URL with the new IP-based camera control endpoints:
- `POST /register/camera/start`
- `POST /register/camera/stop`
- `GET /register/camera/status`
- `POST /register/camera/clear-stop`
- `GET /register/camera/active-ips`

## Middleware & Security

- All camera control routes are protected by the `auth` and `active` middleware
- The `detection` tab access is required for the camera control panel
- CSRF protection is enabled for all POST requests
- Camera endpoints in FastAPI handle IP-based access control

## Testing the Integration

1. **Start FastAPI service**:
   ```bash
   cd D:\laravel project\python\CamScan
   python main.py
   ```
   (Should be running on http://127.0.0.1:8001)

2. **Start Laravel application**:
   ```bash
   cd D:\laravel project\CamScanWeb
   php artisan serve
   ```
   (Should be running on http://localhost:8000)

3. **Test with multiple browsers or devices**:
   - Open Chrome and login to Laravel app
   - Go to `/camera/control` or use the search page
   - Click "Start Camera" - should work
   - Open Firefox and login to Laravel app
   - Click "Start Camera" - should also work
   - In Chrome, click "Stop Camera"
   - In Chrome, try "Start Camera" again - should be blocked
   - In Firefox, click "Stop Camera"
   - In Chrome, "Start Camera" should now work

## Troubleshooting

### Issue: Camera not starting
- **Check**: FastAPI service is running
- **Check**: FASTAPI_URL in Laravel .env is correct
- **Check**: Browser console for errors
- **Check**: Network tab for failed requests

### Issue: IP is blocked but should be allowed
- **Check**: Call `/camera/status` to see current status
- **Check**: Call `/camera/active-ips` to see all active users
- **Check**: Use `/camera/clear-stop` to reset if needed

### Issue: Camera feed not showing
- **Check**: Camera hardware is available and not in use by other applications
- **Check**: FastAPI `/video_feed` endpoint is accessible
- **Check**: Browser has camera permissions

### Issue: Multiple users can't use camera simultaneously
- **Check**: FastAPI IP-based control is working
- **Check**: `_active_ips` set in FastAPI capture_service.py
- **Check**: No errors in FastAPI console

## Files Modified

### Laravel CamScanWeb Project
- ✅ `app/Http/Controllers/CameraController.php` (NEW)
- ✅ `routes/web.php` (UPDATED)
- ✅ `resources/views/tabs/camera/control.blade.php` (NEW)
- ✅ `resources/views/tabs/users/search_capture.blade.php` (UPDATED)
- ✅ `resources/views/tabs/users/capture_section.blade.php` (COMPATIBLE - no changes needed)

### Python CamScan Project
- ✅ `services/capture_service.py` (UPDATED)
- ✅ `main.py` (UPDATED)
- ✅ `routers/registration.py` (UPDATED)

## Access Control Matrix

| User Action | User A IP | User B IP | Camera Hardware |
|-------------|-----------|-----------|----------------|
| User A starts camera | ✅ Active | - | 🔴 In Use |
| User B starts camera | ✅ Active | ✅ Active | 🔴 In Use |
| User A stops camera | ❌ Stopped | ✅ Active | 🔴 In Use |
| User A tries to restart | ❌ Blocked | ✅ Active | 🔴 In Use |
| User B stops camera | ❌ Stopped | ❌ Stopped | 🟢 Released |
| User A can now restart | ✅ Active | - | 🔴 In Use |

## Best Practices

1. **Always stop camera when done**: Use the stop button or call `/camera/stop` when leaving the page
2. **Handle errors gracefully**: Check `is_allowed` before starting camera
3. **Use async/await**: Camera operations are asynchronous
4. **Clean up on unload**: Stop camera when user navigates away
5. **Show user feedback**: Display status messages and toast notifications

## Future Enhancements

1. **Session-based tracking**: Instead of IP-based, use Laravel session IDs
2. **User-based access**: Allow administrators to grant/deny camera access to specific users
3. **Time limits**: Add time limits for camera usage per IP
4. **Priority system**: Allow certain users to have priority access
5. **Notification system**: Notify users when camera becomes available