@extends('layouts.app')

@section('title', 'Camera Control')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">IP-based Camera Control Panel</h3>
                    <p class="card-subtitle">Manage camera access for different IPs in your network</p>
                </div>
                
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Your Camera Access</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="status-indicator" id="your-status-indicator"></div>
                                            <div>
                                                <h5 class="mb-0">Your IP: <span id="your-ip" class="text-primary"></span></h5>
                                                <p class="mb-0 text-muted">Status: <span id="your-status-text">Loading...</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button id="btn-start-camera" class="btn btn-primary btn-lg" onclick="startCamera()">
                                            <i class="bi bi-camera-video-fill me-2"></i> Start Camera
                                        </button>
                                        <button id="btn-stop-camera" class="btn btn-danger btn-lg" onclick="stopCamera()" disabled>
                                            <i class="bi bi-stop-circle-fill me-2"></i> Stop Camera
                                        </button>
                                        <button id="btn-clear-stop" class="btn btn-warning btn-lg" onclick="clearStop()" disabled>
                                            <i class="bi bi-arrow-clockwise me-2"></i> Clear Block
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Camera Stream</h4>
                                </div>
                                <div class="card-body">
                                    <div class="camera-preview-container">
                                        <img id="camera-feed" class="img-fluid rounded" style="display: none;" alt="Camera feed">
                                        <div id="camera-placeholder" class="text-center p-5 text-muted">
                                            <i class="bi bi-camera-video-off fs-1"></i>
                                            <p class="mt-2">Camera feed will appear here when active</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <div class="badge bg-info" id="camera-status-badge">Camera: Inactive</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Active Camera Users</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>IP Address</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="active-ips-table">
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">No active users</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button class="btn btn-secondary" onclick="refreshActiveIps()">
                                            <i class="bi bi-refresh me-2"></i> Refresh List
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .status-indicator {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .status-indicator.allowed {
        background: #d1e7dd;
        color: #155724;
    }
    .status-indicator.active {
        background: #cce5ff;
        color: #004085;
        animation: pulse 2s infinite;
    }
    .status-indicator.blocked {
        background: #f8d7da;
        color: #721c24;
    }
    .status-indicator.inactive {
        background: #e2e3e5;
        color: #383d41;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .camera-preview-container {
        min-height: 300px;
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .ip-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .ip-badge.active {
        background: #cce5ff;
        color: #004085;
    }
    
    .ip-badge.stopped {
        background: #f8d7da;
        color: #721c24;
    }
</style>

<script>
// Configuration
const CAMERA_API_BASE = "{{ config('services.fastapi.url') ?? 'http://localhost:8001' }}";
const CAMERA_START_URL = "{{ route('camera.start') }}";
const CAMERA_STOP_URL = "{{ route('camera.stop') }}";
const CAMERA_STATUS_URL = "{{ route('camera.status') }}";
const CAMERA_CLEAR_STOP_URL = "{{ route('camera.clear-stop') }}";
const CAMERA_ACTIVE_IPS_URL = "{{ route('camera.active-ips') }}";

// Elements
const yourIpEl = document.getElementById('your-ip');
const yourStatusIndicatorEl = document.getElementById('your-status-indicator');
const yourStatusTextEl = document.getElementById('your-status-text');
const btnStartCameraEl = document.getElementById('btn-start-camera');
const btnStopCameraEl = document.getElementById('btn-stop-camera');
const btnClearStopEl = document.getElementById('btn-clear-stop');
const cameraFeedEl = document.getElementById('camera-feed');
const cameraPlaceholderEl = document.getElementById('camera-placeholder');
const cameraStatusBadgeEl = document.getElementById('camera-status-badge');
const activeIpsTableEl = document.getElementById('active-ips-table');

// State
let cameraStreamActive = false;
let yourStatusInterval = null;

// Initialize
async function init() {
    await updateYourInfo();
    await refreshActiveIps();
    
    // Start polling for status updates
    yourStatusInterval = setInterval(updateYourInfo, 5000);
    
    // Handle window unload - stop camera when leaving
    window.addEventListener('beforeunload', async function() {
        if (cameraStreamActive) {
            await stopCamera();
        }
    });
}

// Update your IP and status
async function updateYourInfo() {
    try {
        const response = await fetch(CAMERA_STATUS_URL, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.ip) {
            yourIpEl.textContent = data.ip;
        }
        
        // Update status indicator and text
        yourStatusIndicatorEl.className = 'status-indicator';
        
        if (data.is_active) {
            yourStatusIndicatorEl.classList.add('active');
            yourStatusTextEl.textContent = 'Active - Camera in use';
            btnStartCameraEl.disabled = true;
            btnStopCameraEl.disabled = false;
            btnClearStopEl.disabled = true;
        } else if (data.is_stopped) {
            yourStatusIndicatorEl.classList.add('blocked');
            yourStatusTextEl.textContent = 'Blocked - Camera stopped by you';
            btnStartCameraEl.disabled = true;
            btnStopCameraEl.disabled = true;
            btnClearStopEl.disabled = false;
        } else if (data.is_allowed) {
            yourStatusIndicatorEl.classList.add('allowed');
            yourStatusTextEl.textContent = 'Allowed - Ready to use';
            btnStartCameraEl.disabled = false;
            btnStopCameraEl.disabled = true;
            btnClearStopEl.disabled = true;
        } else {
            yourStatusIndicatorEl.classList.add('blocked');
            yourStatusTextEl.textContent = 'Not allowed - Another IP is using camera';
            btnStartCameraEl.disabled = true;
            btnStopCameraEl.disabled = true;
            btnClearStopEl.disabled = true;
        }
        
        // Update camera feed visibility
        if (data.is_active) {
            if (!cameraStreamActive) {
                startCameraStream();
            }
        } else {
            if (cameraStreamActive) {
                stopCameraStream();
            }
        }
        
    } catch (error) {
        console.error('Error fetching camera status:', error);
        yourStatusTextEl.textContent = 'Error loading status';
        yourStatusIndicatorEl.className = 'status-indicator inactive';
    }
}

// Start camera for this IP
async function startCamera() {
    try {
        const response = await fetch(CAMERA_START_URL, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Camera started for your IP', 'success');
            await updateYourInfo();
            await refreshActiveIps();
        } else {
            showToast(data.message || 'Failed to start camera', 'error');
        }
        
    } catch (error) {
        console.error('Error starting camera:', error);
        showToast('Error starting camera: ' + error.message, 'error');
    }
}

// Stop camera for this IP
async function stopCamera() {
    try {
        const response = await fetch(CAMERA_STOP_URL, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Camera stopped for your IP', 'success');
            stopCameraStream();
            await updateYourInfo();
            await refreshActiveIps();
        } else {
            showToast(data.message || 'Failed to stop camera', 'error');
        }
        
    } catch (error) {
        console.error('Error stopping camera:', error);
        showToast('Error stopping camera: ' + error.message, 'error');
    }
}

// Clear stopped status for this IP
async function clearStop() {
    try {
        const response = await fetch(CAMERA_CLEAR_STOP_URL, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Camera access restored for your IP', 'success');
            await updateYourInfo();
        } else {
            showToast(data.message || 'Failed to clear stop status', 'error');
        }
        
    } catch (error) {
        console.error('Error clearing stop status:', error);
        showToast('Error clearing stop status: ' + error.message, 'error');
    }
}

// Refresh active IPs list
async function refreshActiveIps() {
    try {
        const response = await fetch(CAMERA_ACTIVE_IPS_URL, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        // Update table
        const activeIps = data.active_ips || [];
        const stoppedIps = data.stopped_ips || [];
        
        if (activeIps.length === 0 && stoppedIps.length === 0) {
            activeIpsTableEl.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted">No active users</td>
                </tr>
            `;
            return;
        }
        
        let html = '';
        
        // Add active IPs
        activeIps.forEach(ip => {
            html += `
                <tr>
                    <td><span class="ip-badge active">${ip}</span></td>
                    <td><span class="badge bg-success">Active</span></td>
                    <td>-</td>
                </tr>
            `;
        });
        
        // Add stopped IPs
        stoppedIps.forEach(ip => {
            html += `
                <tr>
                    <td><span class="ip-badge stopped">${ip}</span></td>
                    <td><span class="badge bg-warning text-dark">Stopped</span></td>
                    <td>-</td>
                </tr>
            `;
        });
        
        activeIpsTableEl.innerHTML = html;
        
    } catch (error) {
        console.error('Error fetching active IPs:', error);
        showToast('Error loading active users', 'error');
    }
}

// Start camera stream
function startCameraStream() {
    if (cameraStreamActive) return;
    
    cameraStreamActive = true;
    cameraFeedEl.style.display = 'block';
    cameraPlaceholderEl.style.display = 'none';
    cameraStatusBadgeEl.textContent = 'Camera: Active';
    cameraStatusBadgeEl.className = 'badge bg-success';
    
    // Start streaming from FastAPI
    const videoFeedUrl = CAMERA_API_BASE + '/video_feed?' + new Date().getTime();
    cameraFeedEl.src = videoFeedUrl;
    
    // Handle errors
    cameraFeedEl.onerror = function() {
        cameraStatusBadgeEl.textContent = 'Camera: Error';
        cameraStatusBadgeEl.className = 'badge bg-danger';
        cameraFeedEl.style.display = 'none';
        cameraPlaceholderEl.style.display = 'block';
        cameraStreamActive = false;
    };
}

// Stop camera stream
function stopCameraStream() {
    if (!cameraStreamActive) return;
    
    cameraStreamActive = false;
    cameraFeedEl.style.display = 'none';
    cameraPlaceholderEl.style.display = 'block';
    cameraStatusBadgeEl.textContent = 'Camera: Inactive';
    cameraStatusBadgeEl.className = 'badge bg-info';
    
    // Stop the stream by clearing the src
    cameraFeedEl.src = '';
}

// Show toast notification
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    // Toast body
    const toastBody = document.createElement('div');
    toastBody.className = 'd-flex';
    
    const toastMessage = document.createElement('div');
    toastMessage.className = 'toast-body';
    toastMessage.textContent = message;
    
    const toastClose = document.createElement('button');
    toastClose.type = 'button';
    toastClose.className = 'btn-close btn-close-white me-2 m-auto';
    toastClose.setAttribute('data-bs-dismiss', 'toast');
    toastClose.setAttribute('aria-label', 'Close');
    
    toastBody.appendChild(toastMessage);
    toastBody.appendChild(toastClose);
    toast.appendChild(toastBody);
    
    // Add to container
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    toastContainer.appendChild(toast);
    
    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 5000 });
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// Create toast container if it doesn't exist
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '11';
    document.body.appendChild(container);
    return container;
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', init);
</script>
@endsection