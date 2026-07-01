@extends('layouts.app')

@section('title', 'Camera Control - WebSocket')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0">WebSocket Camera Control Panel</h3>
                        <p class="card-subtitle mb-0">Real-time camera control with <10ms latency</p>
                    </div>
                    <div>
                        <span class="badge bg-primary" id="ws-status-badge">Disconnected</span>
                    </div>
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
                                                <h5 class="mb-0">Your IP: <span id="your-ip" class="text-primary">-</span></h5>
                                                <p class="mb-0 text-muted">Status: <span id="your-status-text">Connecting...</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex gap-2 flex-wrap">
                                            <button id="btn-start-camera" class="btn btn-primary flex-fill" onclick="startCamera()" disabled>
                                                <i class="bi bi-camera-video-fill me-2"></i> Start Camera
                                            </button>
                                            <button id="btn-stop-camera" class="btn btn-danger flex-fill" onclick="stopCamera()" disabled>
                                                <i class="bi bi-stop-circle-fill me-2"></i> Stop Camera
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button id="btn-clear-stop" class="btn btn-warning flex-fill" onclick="clearStop()" disabled>
                                            <i class="bi bi-arrow-clockwise me-2"></i> Clear Block
                                        </button>
                                        <button id="btn-refresh-status" class="btn btn-secondary flex-fill" onclick="refreshStatus()">
                                            <i class="bi bi-refresh me-2"></i> Refresh
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
                                    <h4 class="card-title">Active Camera Users (Real-time)</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>IP Address</th>
                                                    <th>Status</th>
                                                    <th>Last Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="active-ips-table">
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">Connecting to WebSocket...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="ws-latency-indicator" id="ws-latency-indicator"></div>
                                        <div>
                                            <h6 class="mb-0">WebSocket Latency</h6>
                                            <p class="mb-0 text-muted small">Ping: <span id="ws-latency-value">-</span> ms</p>
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
    
    .ip-badge.connecting {
        background: #fff3cd;
        color: #856404;
    }
    
    .ws-latency-indicator {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e2e3e5;
        color: #383d41;
        font-size: 12px;
    }
    
    .ws-latency-indicator.low {
        background: #d1e7dd;
        color: #155724;
    }
    
    .ws-latency-indicator.medium {
        background: #fff3cd;
        color: #856404;
    }
    
    .ws-latency-indicator.high {
        background: #f8d7da;
        color: #721c24;
    }
</style>

<!-- Load WebSocket client -->
<script src="{{ asset('js/websocket-camera.js') }}"></script>

<script>
// Configuration
const FASTAPI_WS_URL = 'ws://127.0.0.1:8001/ws/camera';
const FASTAPI_HTTP_URL = 'http://127.0.0.1:8001';

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
const wsStatusBadgeEl = document.getElementById('ws-status-badge');
const wsLatencyValueEl = document.getElementById('ws-latency-value');
const wsLatencyIndicatorEl = document.getElementById('ws-latency-indicator');

// State
let cameraStreamActive = false;
let lastPingTime = 0;
let lastPongTime = 0;
let activeIps = {};

// Initialize WebSocket connection
const cameraWS = new CameraWebSocket(FASTAPI_WS_URL);

// Set up WebSocket event listeners
cameraWS
    .on('connected', handleWebSocketConnected)
    .on('disconnected', handleWebSocketDisconnected)
    .on('error', handleWebSocketError)
    .on('status_update', handleStatusUpdate)
    .on('ip_status_changed', handleIpStatusChanged)
    .on('ip_connection_change', handleIpConnectionChange)
    .on('pong', handlePong);

// WebSocket event handlers
function handleWebSocketConnected() {
    console.log('[WS] Connected to camera WebSocket');
    wsStatusBadgeEl.textContent = 'Connected';
    wsStatusBadgeEl.className = 'badge bg-success';
    
    // Request current status
    refreshStatus();
    
    // Start ping/pong for latency measurement
    startPingPong();
}

function handleWebSocketDisconnected(event) {
    console.log('[WS] Disconnected from camera WebSocket:', event);
    wsStatusBadgeEl.textContent = 'Disconnected';
    wsStatusBadgeEl.className = 'badge bg-danger';
    wsLatencyValueEl.textContent = '-';
    wsLatencyIndicatorEl.className = 'ws-latency-indicator';
    
    // Update UI to show disconnected state
    updateConnectionStatus(false);
}

function handleWebSocketError(error) {
    console.error('[WS] WebSocket error:', error);
    showToast(`WebSocket Error: ${error.message || error}`, 'danger');
}

function handleStatusUpdate(message) {
    console.log('[WS] Status update:', message);
    updateYourStatus(message);
    updateActiveIpsTable(message);
}

function handleIpStatusChanged(message) {
    console.log('[WS] IP status changed:', message);
    updateActiveIpsTable(message);
    
    // If this is about our IP, update our status
    if (message.ip === yourIpEl.textContent) {
        refreshStatus();
    }
}

function handleIpConnectionChange(message) {
    console.log('[WS] IP connection change:', message);
    // Could update a connection list if we had one
}

function handlePong(message) {
    lastPongTime = Date.now();
    const latency = lastPongTime - lastPingTime;
    wsLatencyValueEl.textContent = latency.toFixed(0);
    
    // Update latency indicator
    wsLatencyIndicatorEl.className = 'ws-latency-indicator';
    if (latency < 50) {
        wsLatencyIndicatorEl.classList.add('low');
        wsLatencyIndicatorEl.textContent = '🟢';
    } else if (latency < 200) {
        wsLatencyIndicatorEl.classList.add('medium');
        wsLatencyIndicatorEl.textContent = '🟡';
    } else {
        wsLatencyIndicatorEl.classList.add('high');
        wsLatencyIndicatorEl.textContent = '🔴';
    }
}

// Start ping/pong for latency measurement
function startPingPong() {
    // Send ping every 5 seconds
    setInterval(() => {
        if (cameraWS.socket && cameraWS.socket.readyState === WebSocket.OPEN) {
            lastPingTime = Date.now();
            cameraWS.send({ action: 'ping', timestamp: lastPingTime });
        }
    }, 5000);
}

// Update connection status UI
function updateConnectionStatus(connected) {
    if (connected) {
        yourStatusIndicatorEl.className = 'status-indicator';
        yourStatusTextEl.textContent = 'Connected';
    } else {
        yourStatusIndicatorEl.className = 'status-indicator inactive';
        yourStatusTextEl.textContent = 'Disconnected';
        btnStartCameraEl.disabled = true;
        btnStopCameraEl.disabled = true;
        btnClearStopEl.disabled = true;
    }
}

// Update your status based on message
function updateYourStatus(message) {
    if (message.ip) {
        yourIpEl.textContent = message.ip;
    }
    
    yourStatusIndicatorEl.className = 'status-indicator';
    
    if (message.is_active) {
        yourStatusIndicatorEl.classList.add('active');
        yourStatusTextEl.textContent = 'Active - Camera in use';
        btnStartCameraEl.disabled = true;
        btnStopCameraEl.disabled = false;
        btnClearStopEl.disabled = true;
    } else if (message.is_stopped) {
        yourStatusIndicatorEl.classList.add('blocked');
        yourStatusTextEl.textContent = 'Blocked - Camera stopped by you';
        btnStartCameraEl.disabled = true;
        btnStopCameraEl.disabled = true;
        btnClearStopEl.disabled = false;
    } else if (message.is_allowed) {
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
    if (message.is_active) {
        if (!cameraStreamActive) {
            startCameraStream();
        }
    } else {
        if (cameraStreamActive) {
            stopCameraStream();
        }
    }
}

// Update active IPs table
function updateActiveIpsTable(message) {
    const activeIps = new Set(message.active_ips || []);
    const stoppedIps = new Set(message.stopped_ips || []);
    
    if (activeIps.size === 0 && stoppedIps.size === 0) {
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
        if (!activeIps.has(ip)) {
            html += `
                <tr>
                    <td><span class="ip-badge stopped">${ip}</span></td>
                    <td><span class="badge bg-warning text-dark">Stopped</span></td>
                    <td>-</td>
                </tr>
            `;
        }
    });
    
    activeIpsTableEl.innerHTML = html;
}

// Refresh current IP status
async function refreshStatus() {
    try {
        const status = await cameraWS.getStatus();
        updateYourStatus(status);
        updateActiveIpsTable(status);
    } catch (error) {
        console.error('Error refreshing status:', error);
        showToast(`Error: ${error.message}`, 'error');
    }
}

// Camera control functions
async function startCamera() {
    try {
        btnStartCameraEl.disabled = true;
        btnStartCameraEl.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Starting...';
        
        const response = await cameraWS.startCamera();
        
        btnStartCameraEl.disabled = false;
        btnStartCameraEl.innerHTML = '<i class="bi bi-camera-video-fill me-2"></i> Start Camera';
        
        if (response.success) {
            showToast(response.message || 'Camera started for your IP', 'success');
            await refreshStatus();
        } else {
            showToast(response.message || 'Failed to start camera', 'error');
        }
    } catch (error) {
        btnStartCameraEl.disabled = false;
        btnStartCameraEl.innerHTML = '<i class="bi bi-camera-video-fill me-2"></i> Start Camera';
        console.error('Error starting camera:', error);
        showToast(`Error: ${error.message}`, 'error');
    }
}

async function stopCamera() {
    try {
        btnStopCameraEl.disabled = true;
        btnStopCameraEl.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Stopping...';
        
        const response = await cameraWS.stopCamera();
        
        btnStopCameraEl.disabled = false;
        btnStopCameraEl.innerHTML = '<i class="bi bi-stop-circle-fill me-2"></i> Stop Camera';
        
        if (response.success) {
            showToast(response.message || 'Camera stopped for your IP', 'success');
            stopCameraStream();
            await refreshStatus();
        } else {
            showToast(response.message || 'Failed to stop camera', 'error');
        }
    } catch (error) {
        btnStopCameraEl.disabled = false;
        btnStopCameraEl.innerHTML = '<i class="bi bi-stop-circle-fill me-2"></i> Stop Camera';
        console.error('Error stopping camera:', error);
        showToast(`Error: ${error.message}`, 'error');
    }
}

async function clearStop() {
    try {
        btnClearStopEl.disabled = true;
        btnClearStopEl.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Clearing...';
        
        const response = await cameraWS.clearStop();
        
        btnClearStopEl.disabled = false;
        btnClearStopEl.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i> Clear Block';
        
        if (response.success) {
            showToast(response.message || 'Camera access restored for your IP', 'success');
            await refreshStatus();
        } else {
            showToast(response.message || 'Failed to clear stop status', 'error');
        }
    } catch (error) {
        btnClearStopEl.disabled = false;
        btnClearStopEl.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i> Clear Block';
        console.error('Error clearing stop status:', error);
        showToast(`Error: ${error.message}`, 'error');
    }
}

// Camera stream functions
function startCameraStream() {
    if (cameraStreamActive) return;
    
    cameraStreamActive = true;
    cameraFeedEl.style.display = 'block';
    cameraPlaceholderEl.style.display = 'none';
    cameraStatusBadgeEl.textContent = 'Camera: Active';
    cameraStatusBadgeEl.className = 'badge bg-success';
    
    // Start streaming from FastAPI
    const videoFeedUrl = FASTAPI_HTTP_URL + '/video_feed?' + new Date().getTime();
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
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
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
    
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 5000 });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '11';
    document.body.appendChild(container);
    return container;
}

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    cameraWS.disconnect();
});

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    updateConnectionStatus(false);
});
</script>
@endsection