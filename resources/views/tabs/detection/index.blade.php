@extends('layouts.app')

@section('title', 'Camera Detection')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Camera Stream Detection</h3>
                    <p class="card-subtitle">Connect to CCTV cameras or use local webcam for real-time detection</p>
                </div>

                <div class="card-body">
                    <!-- Camera Connection Section -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Camera Source Configuration</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <!-- Camera Source Toggle -->
                                        <div class="col-12">
                                            <div class="d-flex gap-2 mb-3">
                                                <button class="btn btn-primary" id="btn-ip-camera" onclick="switchToIpCamera()">
                                                    <i class="bi bi-camera-video me-1"></i> IP Camera
                                                </button>
                                                <button class="btn btn-outline-secondary" id="btn-webcam" onclick="switchToWebcam()">
                                                    <i class="bi bi-webcam me-1"></i> Local Webcam
                                                </button>
                                            </div>
                                        </div>

                                        <!-- IP Camera Input -->
                                        <div id="ip-camera-section">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="camera-ip" class="form-label">Camera IP / Stream URL</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-globe"></i>
                                                    </span>
                                                    <input type="text" class="form-control" id="camera-ip" 
                                                           placeholder="Enter camera IP or RTSP/HTTP URL" 
                                                           value="">
                                                </div>
                                                <div class="form-text">
                                                    Examples: rtsp://192.168.1.100:554/stream, http://192.168.1.100:8080/video
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="stream-protocol" class="form-label">Protocol</label>
                                                <select class="form-select" id="stream-protocol">
                                                    <option value="">Auto Detect</option>
                                                    <option value="rtsp">RTSP</option>
                                                    <option value="http">HTTP</option>
                                                    <option value="https">HTTPS</option>
                                                    <option value="mjpeg">MJPEG</option>
                                                    <option value="hls">HLS (.m3u8)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="stream-port" class="form-label">Port</label>
                                                <input type="text" class="form-control" id="stream-port" 
                                                       placeholder="554" value="554">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="stream-path" class="form-label">Stream Path</label>
                                                <input type="text" class="form-control" id="stream-path" 
                                                       placeholder="/stream, /video, /live.sdp" value="/stream">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="camera-username" class="form-label">Username</label>
                                                <input type="text" class="form-control" id="camera-username" 
                                                       placeholder="Optional">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="camera-password" class="form-label">Password</label>
                                                <input type="password" class="form-control" id="camera-password" 
                                                       placeholder="Optional">
                                            </div>
                                        </div>
                                        </div>

                                        <!-- Webcam Section (Hidden by default) -->
                                        <div class="col-12" id="webcam-section" style="display: none;">
                                            <div class="alert alert-info">
                                                <i class="bi bi-info-circle me-2"></i> 
                                                Local webcam will be accessed through your browser. Ensure you have granted camera permissions.
                                            </div>
                                        </div>

                                        <!-- Detection Type Selection -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="detection-type" class="form-label">Detection Type</label>
                                                <select class="form-select" id="detection-type">
                                                    <option value="face">Face Detection</option>
                                                    <option value="object">Object Detection</option>
                                                    <option value="motion">Motion Detection</option>
                                                    <option value="all" selected>All Types</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="confidence-threshold" class="form-label">Confidence Threshold (%)</label>
                                                <input type="range" class="form-range" min="50" max="99" value="70" id="confidence-threshold">
                                                <div class="text-center">
                                                    <span id="confidence-value">70%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Connection Buttons -->
                                        <div class="col-12">
                                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                <button class="btn btn-secondary me-md-2" onclick="testConnection()" id="btn-test-connection">
                                                    <i class="bi bi-check-circle me-1"></i> Test Connection
                                                </button>
                                                <button class="btn btn-primary" onclick="connectCamera()" id="btn-connect">
                                                    <i class="bi bi-plug me-1"></i> Connect Camera
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Camera Preview Section -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Connection Status</h4>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <div class="status-indicator status-lg bg-secondary mx-auto mb-3" id="connection-status"></div>
                                        <h5 id="connection-status-text">Not Connected</h5>
                                        <p class="text-muted" id="connection-info">Configure a camera source to begin</p>
                                    </div>
                                    <hr>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-danger" onclick="disconnectCamera()" id="btn-disconnect" disabled>
                                            <i class="bi bi-plug-fill me-1"></i> Disconnect
                                        </button>
                                        <button class="btn btn-success" onclick="startDetection()" id="btn-start-detection" disabled>
                                            <i class="bi bi-play-circle me-1"></i> Start Detection
                                        </button>
                                        <button class="btn btn-warning" onclick="stopDetection()" id="btn-stop-detection" disabled>
                                            <i class="bi bi-stop-circle me-1"></i> Stop Detection
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Video Stream Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Live Stream & Detection</h4>
                                    <div>
                                        <span class="badge bg-secondary" id="stream-badge">No Stream</span>
                                        <span class="badge bg-secondary ms-2" id="fps-badge">FPS: 0</span>
                                        <span class="badge bg-secondary ms-2" id="resolution-badge">0x0</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Video Stream Container -->
                                    <div class="stream-container position-relative bg-dark rounded overflow-hidden" style="height: 500px;">
                                        <!-- Video Element -->
                                        <video id="video-stream" class="w-100 h-100" autoplay muted playsinline
                                               style="object-fit: contain; display: none;">
                                        </video>
                                        
                                        <!-- Canvas for processing -->
                                        <canvas id="canvas-overlay" class="position-absolute top-0 start-0" 
                                                style="display: none;"></canvas>
                                        
                                        <!-- Image Element for MJPEG streams -->
                                        <img id="mjpeg-stream" class="w-100 h-100" style="object-fit: contain; display: none;" 
                                             src="" alt="MJPEG Stream">
                                        
                                        <!-- Placeholder -->
                                        <div id="stream-placeholder" class="d-flex flex-column align-items-center justify-content-center h-100 text-white">
                                            <i class="bi bi-camera-video-off fs-1"></i>
                                            <p class="mt-2">No camera stream connected</p>
                                            <p class="text-muted">Connect a camera to begin detection</p>
                                        </div>

                                        <!-- Detection Overlay -->
                                        <div id="detection-overlay" class="position-absolute top-0 start-0 w-100 h-100" 
                                             style="pointer-events: none; display: none;">
                                        </div>

                                        <!-- Controls Overlay -->
                                        <div class="position-absolute bottom-0 start-0 end-0 p-3 bg-gradient-transparent">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <button class="btn btn-sm btn-outline-light" onclick="toggleFullscreen()">
                                                        <i class="bi bi-fullscreen"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-light ms-2" onclick="captureFrame()">
                                                        <i class="bi bi-camera"></i> Capture
                                                    </button>
                                                </div>
                                                <div id="detection-status" class="text-white">
                                                    <span class="badge bg-success" style="display: none;">Detecting</span>
                                                    <span class="badge bg-danger" style="display: none;">Not Detecting</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stream Info -->
                                    <div class="mt-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Stream URL</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control form-control-sm" id="active-stream-url" readonly>
                                                        <button class="btn btn-sm btn-outline-secondary" onclick="copyStreamUrl()">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Detection Results</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control form-control-sm" id="detection-results-summary" readonly>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="showResults()">
                                                            <i class="bi bi-list"></i> View All
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

                    <!-- Detection Results Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Detection Results</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-hover table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Timestamp</th>
                                                    <th>Type</th>
                                                    <th>Confidence</th>
                                                    <th>Label</th>
                                                    <th>Position</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="results-table-body">
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">
                                                        No detection results yet. Start detection to see results.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <button class="btn btn-sm btn-outline-danger" onclick="clearResults()">
                                                <i class="bi bi-trash me-1"></i> Clear All
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="exportResults()">
                                                <i class="bi bi-download me-1"></i> Export
                                            </button>
                                        </div>
                                        <div>
                                            <span class="text-muted">Total Detections: </span>
                                            <span id="total-detections" class="badge bg-primary">0</span>
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

<!-- Modal for Capture Preview -->
<div class="modal fade" id="captureModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Captured Frame</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img id="captured-image" class="img-fluid rounded shadow" src="" alt="Captured Frame">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveCapturedImage()">Save Image</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Stream URL Builder -->
<div class="modal fade" id="urlBuilderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stream URL Builder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Generated Stream URL</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="generated-url" readonly>
                        <button class="btn btn-outline-secondary" onclick="copyGeneratedUrl()">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                </div>
                <div class="alert alert-info">
                    <small>Use this URL to connect to your camera stream. For RTSP streams, you may need a proxy or conversion service as browsers don't support RTSP natively.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<style>
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }
    
    .status-lg {
        width: 24px;
        height: 24px;
    }
    
    .bg-gradient-transparent {
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
    }
    
    .stream-container {
        position: relative;
    }
    
    .detection-box {
        position: absolute;
        border: 2px solid #0d6efd;
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        font-size: 12px;
        padding: 2px 4px;
        pointer-events: none;
    }
    
    .detection-box .label {
        background-color: rgba(13, 110, 253, 0.8);
        color: white;
        padding: 1px 3px;
        border-radius: 2px;
        display: inline-block;
    }
    
    #detection-overlay {
        pointer-events: none;
    }
    
    .confidence-bar {
        height: 4px;
        background-color: #6c757d;
        margin-top: 2px;
    }
    
    .confidence-fill {
        height: 100%;
        background-color: #28a745;
        transition: width 0.3s;
    }
</style>

@endsection

@section('scripts')
<script>
// Global state
let stream = null;
let detectionInterval = null;
let videoElement = null;
let canvasElement = null;
let canvasCtx = null;
let isDetecting = false;
let currentStreamType = 'ip';
let detectionResults = [];
let streamUrl = '';

// DOM Elements
const cameraIpInput = document.getElementById('camera-ip');
const streamProtocolSelect = document.getElementById('stream-protocol');
const streamPortInput = document.getElementById('stream-port');
const streamPathInput = document.getElementById('stream-path');
const cameraUsernameInput = document.getElementById('camera-username');
const cameraPasswordInput = document.getElementById('camera-password');
const detectionTypeSelect = document.getElementById('detection-type');
const confidenceThresholdInput = document.getElementById('confidence-threshold');
const confidenceValueSpan = document.getElementById('confidence-value');

// Status elements
const connectionStatusEl = document.getElementById('connection-status');
const connectionStatusTextEl = document.getElementById('connection-status-text');
const connectionInfoEl = document.getElementById('connection-info');
const streamBadgeEl = document.getElementById('stream-badge');
const fpsBadgeEl = document.getElementById('fps-badge');
const resolutionBadgeEl = document.getElementById('resolution-badge');
const activeStreamUrlEl = document.getElementById('active-stream-url');
const detectionResultsSummaryEl = document.getElementById('detection-results-summary');
const totalDetectionsEl = document.getElementById('total-detections');

// Buttons
const btnTestConnection = document.getElementById('btn-test-connection');
const btnConnect = document.getElementById('btn-connect');
const btnDisconnect = document.getElementById('btn-disconnect');
const btnStartDetection = document.getElementById('btn-start-detection');
const btnStopDetection = document.getElementById('btn-stop-detection');
const btnIpCamera = document.getElementById('btn-ip-camera');
const btnWebcam = document.getElementById('btn-webcam');
const ipCameraSection = document.getElementById('ip-camera-section');
const webcamSection = document.getElementById('webcam-section');

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    videoElement = document.getElementById('video-stream');
    canvasElement = document.getElementById('canvas-overlay');
    canvasCtx = canvasElement.getContext('2d');
    
    function resizeCanvas() {
        if (videoElement.videoWidth > 0) {
            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;
        }
    }
    
    videoElement.addEventListener('loadedmetadata', resizeCanvas);
    videoElement.addEventListener('resize', resizeCanvas);
    
    confidenceThresholdInput.addEventListener('input', function() {
        confidenceValueSpan.textContent = this.value + '%';
    });
    
    switchToIpCamera();
});

function switchToIpCamera() {
    currentStreamType = 'ip';
    btnIpCamera.classList.remove('btn-outline-primary');
    btnIpCamera.classList.add('btn-primary');
    btnWebcam.classList.remove('btn-primary');
    btnWebcam.classList.add('btn-outline-secondary');
    webcamSection.style.display = 'none';
    cameraIpInput.disabled = false;
    streamProtocolSelect.disabled = false;
    streamPortInput.disabled = false;
    streamPathInput.disabled = false;
}

function switchToWebcam() {
    currentStreamType = 'webcam';
    btnWebcam.classList.remove('btn-outline-secondary');
    btnWebcam.classList.add('btn-primary');
    btnIpCamera.classList.remove('btn-primary');
    btnIpCamera.classList.add('btn-outline-primary');
    webcamSection.style.display = 'block';
    cameraIpInput.disabled = true;
    streamProtocolSelect.disabled = true;
    streamPortInput.disabled = true;
    streamPathInput.disabled = true;
}

function buildStreamUrl() {
    const ip = cameraIpInput.value.trim();
    const protocol = streamProtocolSelect.value;
    const port = streamPortInput.value.trim();
    const path = streamPathInput.value.trim();
    const username = cameraUsernameInput.value.trim();
    const password = cameraPasswordInput.value.trim();
    
    let url = '';
    
    if (currentStreamType === 'webcam') {
        url = 'webcam';
    } else if (ip) {
        let finalProtocol = protocol || 'rtsp';
        url = finalProtocol + '://' + ip;
        
        if (port) {
            url += ':' + port;
        }
        
        if (path) {
            url += (path.startsWith('/') ? path : '/' + path);
        }
        
        if (username && password) {
            url = finalProtocol + '://' + encodeURIComponent(username) + ':' + encodeURIComponent(password) + '@' + ip;
            if (port) url += ':' + port;
            if (path) url += (path.startsWith('/') ? path : '/' + path);
        } else if (username) {
            url = finalProtocol + '://' + encodeURIComponent(username) + '@' + ip;
            if (port) url += ':' + port;
            if (path) url += (path.startsWith('/') ? path : '/' + path);
        }
    }
    
    return url;
}

async function testConnection() {
    const url = buildStreamUrl();
    if (!url) {
        showAlert('Please enter a camera IP or select webcam', 'warning');
        return;
    }
    
    btnTestConnection.disabled = true;
    btnTestConnection.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Testing...';
    
    try {
        const response = await fetch('/detection/validate-stream', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ stream_url: url })
        });
        
        const data = await response.json();
        
        if (data.success && data.valid) {
            if (data.type === 'webcam') {
                showAlert('Local webcam ready for connection', 'success');
            } else {
                showAlert('Stream URL is valid: ' + url, 'success');
                document.getElementById('generated-url').value = url;
                new bootstrap.Modal(document.getElementById('urlBuilderModal')).show();
            }
        } else {
            showAlert('Invalid stream URL: ' + (data.message || 'Please check the format'), 'danger');
        }
    } catch (error) {
        showAlert('Connection test failed: ' + error.message, 'danger');
    } finally {
        btnTestConnection.disabled = false;
        btnTestConnection.innerHTML = '<i class="bi bi-check-circle me-1"></i> Test Connection';
    }
}

async function connectCamera() {
    const url = buildStreamUrl();
    if (!url) {
        showAlert('Please configure a camera source', 'warning');
        return;
    }
    
    await disconnectCamera();
    
    btnConnect.disabled = true;
    btnConnect.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Connecting...';
    
    try {
        if (currentStreamType === 'webcam' || url === 'webcam' || url === 'local' || url === 'localhost') {
            await connectWebcam();
        } else {
            await connectIpCamera(url);
        }
    } catch (error) {
        showAlert('Failed to connect: ' + error.message, 'danger');
        updateConnectionStatus('error', 'Connection Failed', 'Error: ' + error.message);
    } finally {
        btnConnect.disabled = false;
        btnConnect.innerHTML = '<i class="bi bi-plug me-1"></i> Connect Camera';
    }
}

async function connectWebcam() {
    try {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Webcam access not supported in your browser');
        }
        
        const constraints = {
            video: {
                width: { ideal: 1280 },
                height: { ideal: 720 },
                facingMode: 'environment'
            },
            audio: false
        };
        
        stream = await navigator.mediaDevices.getUserMedia(constraints);
        videoElement.srcObject = stream;
        videoElement.style.display = 'block';
        document.getElementById('stream-placeholder').style.display = 'none';
        
        await new Promise((resolve) => {
            videoElement.onloadedmetadata = resolve;
        });
        
        canvasElement.width = videoElement.videoWidth;
        canvasElement.height = videoElement.videoHeight;
        canvasElement.style.display = 'block';
        
        streamUrl = 'webcam';
        activeStreamUrlEl.value = 'Local Webcam';
        
        updateConnectionStatus('success', 'Connected', 'Local webcam active');
        streamBadgeEl.textContent = 'Live: Webcam';
        streamBadgeEl.className = 'badge bg-success';
        
        btnDisconnect.disabled = false;
        btnStartDetection.disabled = false;
        
        startFpsCounter();
        
    } catch (error) {
        console.error('Webcam error:', error);
        throw error;
    }
}

async function connectIpCamera(url) {
    try {
        videoElement.style.display = 'none';
        document.getElementById('mjpeg-stream').style.display = 'none';
        document.getElementById('stream-placeholder').style.display = 'block';
        
        const lowerUrl = url.toLowerCase();
        
        if (lowerUrl.includes('.m3u8')) {
            await connectHlsStream(url);
        } else if (lowerUrl.includes('.mp4') || lowerUrl.includes('.webm') || lowerUrl.includes('.flv')) {
            videoElement.src = url;
            videoElement.style.display = 'block';
            document.getElementById('stream-placeholder').style.display = 'none';
            
            await new Promise((resolve, reject) => {
                videoElement.onloadedmetadata = resolve;
                videoElement.onerror = () => reject(new Error('Failed to load video'));
            });
            
            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;
            canvasElement.style.display = 'block';
            
        } else if (lowerUrl.startsWith('http') && !lowerUrl.includes('.m3u8')) {
            await connectMjpegStream(url);
        } else if (lowerUrl.startsWith('rtsp')) {
            showAlert('RTSP streams require a proxy or conversion service. Please use an RTSP to WebRTC proxy.', 'warning');
            throw new Error('RTSP not supported directly in browser');
        } else {
            videoElement.src = url;
            videoElement.style.display = 'block';
            document.getElementById('stream-placeholder').style.display = 'none';
            
            await new Promise((resolve, reject) => {
                videoElement.onloadedmetadata = resolve;
                videoElement.onerror = () => reject(new Error('Failed to load stream'));
            });
            
            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;
            canvasElement.style.display = 'block';
        }
        
        streamUrl = url;
        activeStreamUrlEl.value = url;
        
        updateConnectionStatus('success', 'Connected', 'Stream: ' + url);
        streamBadgeEl.textContent = 'Live: IP Camera';
        streamBadgeEl.className = 'badge bg-success';
        
        btnDisconnect.disabled = false;
        btnStartDetection.disabled = false;
        
        startFpsCounter();
        
    } catch (error) {
        console.error('IP Camera error:', error);
        throw error;
    }
}

async function connectHlsStream(url) {
    if (typeof Hls !== 'undefined') {
        if (Hls.isSupported()) {
            const hls = new Hls();
            hls.loadSource(url);
            hls.attachMedia(videoElement);
            
            hls.on(Hls.Events.MANIFEST_PARSED, () => {
                videoElement.play().catch(e => console.error('Play error:', e));
                videoElement.style.display = 'block';
                document.getElementById('stream-placeholder').style.display = 'none';
                canvasElement.width = videoElement.videoWidth || 1280;
                canvasElement.height = videoElement.videoHeight || 720;
                canvasElement.style.display = 'block';
            });
            
            hls.on(Hls.Events.ERROR, (event, data) => {
                console.error('HLS Error:', data);
                showAlert('HLS Stream Error: ' + (data.details || 'Unknown error'), 'danger');
            });
            window.hls = hls;
        } else {
            throw new Error('HLS not supported in your browser');
        }
    } else {
        videoElement.src = url;
        videoElement.style.display = 'block';
        document.getElementById('stream-placeholder').style.display = 'none';
        
        await new Promise((resolve, reject) => {
            videoElement.onloadedmetadata = resolve;
            videoElement.onerror = () => reject(new Error('HLS not supported'));
        });
        
        canvasElement.width = videoElement.videoWidth;
        canvasElement.height = videoElement.videoHeight;
        canvasElement.style.display = 'block';
    }
}

async function connectMjpegStream(url) {
    const img = document.getElementById('mjpeg-stream');
    img.src = url;
    img.style.display = 'block';
    document.getElementById('stream-placeholder').style.display = 'none';
    
    await new Promise((resolve, reject) => {
        img.onload = resolve;
        img.onerror = () => reject(new Error('Failed to load MJPEG stream'));
    });
    
    canvasElement.width = img.naturalWidth || img.width || 1280;
    canvasElement.height = img.naturalHeight || img.height || 720;
    canvasElement.style.display = 'block';
}

async function disconnectCamera() {
    await stopDetection();
    
    if (videoElement && videoElement.srcObject) {
        const tracks = videoElement.srcObject.getTracks();
        tracks.forEach(track => track.stop());
        videoElement.srcObject = null;
    }
    
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    
    if (typeof Hls !== 'undefined' && window.hls) {
        window.hls.destroy();
        window.hls = null;
    }
    
    videoElement.style.display = 'none';
    videoElement.src = '';
    document.getElementById('mjpeg-stream').style.display = 'none';
    document.getElementById('mjpeg-stream').src = '';
    document.getElementById('stream-placeholder').style.display = 'block';
    canvasElement.style.display = 'none';
    
    streamUrl = '';
    activeStreamUrlEl.value = '';
    
    updateConnectionStatus('secondary', 'Not Connected', 'Configure a camera source to begin');
    streamBadgeEl.textContent = 'No Stream';
    streamBadgeEl.className = 'badge bg-secondary';
    fpsBadgeEl.textContent = 'FPS: 0';
    resolutionBadgeEl.textContent = '0x0';
    
    btnDisconnect.disabled = true;
    btnStartDetection.disabled = true;
    btnStopDetection.disabled = true;
    
    clearDetectionOverlay();
    detectionResultsSummaryEl.value = '';
}

function updateConnectionStatus(color, text, info) {
    connectionStatusEl.className = 'status-indicator status-lg bg-' + color + ' mx-auto mb-3';
    connectionStatusTextEl.textContent = text;
    connectionInfoEl.textContent = info;
}

async function startDetection() {
    if (!streamUrl) {
        showAlert('Please connect a camera first', 'warning');
        return;
    }
    
    if (isDetecting) {
        showAlert('Detection is already running', 'info');
        return;
    }
    
    const detectionType = detectionTypeSelect.value;
    
    btnStartDetection.disabled = true;
    btnStartDetection.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Starting...';
    
    try {
        const response = await fetch('/detection/stream/start', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                stream_url: streamUrl,
                camera_ip: streamUrl === 'webcam' ? null : streamUrl,
                detection_type: detectionType
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            isDetecting = true;
            document.querySelector('#detection-status .badge-success').style.display = 'inline-block';
            document.querySelector('#detection-status .badge-danger').style.display = 'none';
            
            btnStartDetection.disabled = true;
            btnStopDetection.disabled = false;
            btnStopDetection.innerHTML = '<i class="bi bi-stop-circle me-1"></i> Stop Detection';
            
            startLocalDetection();
            showAlert('Detection started successfully', 'success');
        } else {
            throw new Error(data.message || 'Failed to start detection');
        }
    } catch (error) {
        showAlert('Failed to start detection: ' + error.message, 'danger');
    } finally {
        btnStartDetection.disabled = false;
        btnStartDetection.innerHTML = '<i class="bi bi-play-circle me-1"></i> Start Detection';
    }
}

async function stopDetection() {
    if (!isDetecting) {
        return;
    }
    
    btnStopDetection.disabled = true;
    btnStopDetection.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Stopping...';
    
    try {
        const response = await fetch('/detection/stream/stop', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                stream_url: streamUrl,
                camera_ip: streamUrl === 'webcam' ? null : streamUrl
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            stopLocalDetection();
            document.querySelector('#detection-status .badge-success').style.display = 'none';
            document.querySelector('#detection-status .badge-danger').style.display = 'inline-block';
            btnStopDetection.disabled = true;
            btnStartDetection.disabled = false;
            showAlert('Detection stopped', 'info');
        } else {
            throw new Error(data.message || 'Failed to stop detection');
        }
    } catch (error) {
        showAlert('Failed to stop detection: ' + error.message, 'danger');
    } finally {
        btnStopDetection.disabled = false;
        btnStopDetection.innerHTML = '<i class="bi bi-stop-circle me-1"></i> Stop Detection';
    }
}

function startLocalDetection() {
    if (detectionInterval) {
        clearInterval(detectionInterval);
    }
    
    detectionInterval = setInterval(() => {
        if (videoElement && !videoElement.paused && videoElement.readyState >= HTMLMediaElement.HAVE_CURRENT_DATA) {
            processFrame();
        } else if (document.getElementById('mjpeg-stream').style.display === 'block') {
            fetchDetectionResults();
        }
    }, 500);
}

function stopLocalDetection() {
    if (detectionInterval) {
        clearInterval(detectionInterval);
        detectionInterval = null;
    }
    isDetecting = false;
    clearDetectionOverlay();
}

function processFrame() {
    if (!videoElement || videoElement.paused || videoElement.readyState < HTMLMediaElement.HAVE_CURRENT_DATA) {
        return;
    }
    
    canvasElement.width = videoElement.videoWidth;
    canvasElement.height = videoElement.videoHeight;
    canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
    canvasCtx.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);
    
    const frameData = canvasElement.toDataURL('image/jpeg', 0.7);
    simulateDetectionResults();
    resolutionBadgeEl.textContent = canvasElement.width + 'x' + canvasElement.height;
}

function simulateDetectionResults() {
    const overlay = document.getElementById('detection-overlay');
    overlay.innerHTML = '';
    overlay.style.display = 'block';
    
    const detections = [];
    const now = new Date();
    
    if (Math.random() > 0.5) {
        const x = Math.random() * (canvasElement.width - 200);
        const y = Math.random() * (canvasElement.height - 200);
        const width = 100 + Math.random() * 100;
        const height = 100 + Math.random() * 100;
        
        const detection = {
            id: 'face-' + now.getTime(),
            type: 'face',
            label: 'Person',
            confidence: 70 + Math.random() * 30,
            x: x,
            y: y,
            width: width,
            height: height,
            timestamp: now.toISOString()
        };
        
        detections.push(detection);
        drawDetectionBox(detection);
    }
    
    if (Math.random() > 0.5) {
        const objects = ['car', 'person', 'bottle', 'phone', 'chair', 'table'];
        const label = objects[Math.floor(Math.random() * objects.length)];
        const x = Math.random() * (canvasElement.width - 150);
        const y = Math.random() * (canvasElement.height - 150);
        const width = 50 + Math.random() * 100;
        const height = 50 + Math.random() * 100;
        
        const detection = {
            id: 'object-' + now.getTime(),
            type: 'object',
            label: label,
            confidence: 60 + Math.random() * 40,
            x: x,
            y: y,
            width: width,
            height: height,
            timestamp: now.toISOString()
        };
        
        detections.push(detection);
        drawDetectionBox(detection);
    }
    
    if (detections.length > 0) {
        updateDetectionResults(detections);
    }
}

function drawDetectionBox(detection) {
    const overlay = document.getElementById('detection-overlay');
    const box = document.createElement('div');
    box.className = 'detection-box';
    box.style.left = detection.x + 'px';
    box.style.top = detection.y + 'px';
    box.style.width = detection.width + 'px';
    box.style.height = detection.height + 'px';
    
    const label = document.createElement('div');
    label.className = 'label';
    label.textContent = detection.label + ' ' + detection.confidence.toFixed(1) + '%';
    box.appendChild(label);
    
    const confidenceBar = document.createElement('div');
    confidenceBar.className = 'confidence-bar';
    const confidenceFill = document.createElement('div');
    confidenceFill.className = 'confidence-fill';
    confidenceFill.style.width = (detection.confidence) + '%';
    confidenceBar.appendChild(confidenceFill);
    box.appendChild(confidenceBar);
    
    overlay.appendChild(box);
}

function clearDetectionOverlay() {
    const overlay = document.getElementById('detection-overlay');
    overlay.innerHTML = '';
    overlay.style.display = 'none';
}

function updateDetectionResults(newDetections) {
    detectionResults = [...newDetections, ...detectionResults].slice(0, 100);
    const uniqueLabels = new Set(detectionResults.map(d => d.label));
    const summary = Array.from(uniqueLabels).join(', ') + ' (' + detectionResults.length + ' total)';
    detectionResultsSummaryEl.value = summary;
    totalDetectionsEl.textContent = detectionResults.length;
    updateResultsTable();
}

function updateResultsTable() {
    const tbody = document.getElementById('results-table-body');
    
    if (detectionResults.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No detection results yet</td></tr>';
        return;
    }
    
    let html = '';
    detectionResults.slice(0, 50).forEach(result => {
        const timestamp = new Date(result.timestamp).toLocaleTimeString();
        const position = Math.round(result.x) + ',' + Math.round(result.y) + ' - ' + 
                         Math.round(result.width) + 'x' + Math.round(result.height);
        
        html += `
            <tr>
                <td>${timestamp}</td>
                <td><span class="badge bg-primary">${result.type}</span></td>
                <td>${result.confidence.toFixed(1)}%</td>
                <td>${result.label}</td>
                <td>${position}</td>
                <td>
                    <button class="btn btn-sm btn-outline-success" onclick="highlightDetection('${result.id}')">
                        <i class="bi bi-crosshair"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function highlightDetection(detectionId) {
    const detection = detectionResults.find(d => d.id === detectionId);
    if (detection) {
        clearDetectionOverlay();
        drawDetectionBox(detection);
        
        const rows = document.querySelectorAll('#results-table-body tr');
        rows.forEach(row => {
            if (row.textContent.includes(detectionId)) {
                row.classList.add('table-active');
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                setTimeout(() => {
                    row.classList.remove('table-active');
                }, 2000);
            }
        });
    }
}

async function fetchDetectionResults() {
    try {
        const response = await fetch('/detection/results?stream_url=' + encodeURIComponent(streamUrl) + '&limit=20', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });
        
        const data = await response.json();
        if (data.success && data.results && data.results.length > 0) {
            updateDetectionResults(data.results);
        }
    } catch (error) {
        console.error('Failed to fetch detection results:', error);
    }
}

function captureFrame() {
    if (!videoElement || videoElement.paused) {
        showAlert('No active stream to capture', 'warning');
        return;
    }
    
    canvasElement.width = videoElement.videoWidth;
    canvasElement.height = videoElement.videoHeight;
    canvasCtx.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);
    
    const imageData = canvasElement.toDataURL('image/jpeg', 0.9);
    const modal = new bootstrap.Modal(document.getElementById('captureModal'));
    document.getElementById('captured-image').src = imageData;
    modal.show();
    window.capturedImageData = imageData;
}

function saveCapturedImage() {
    if (window.capturedImageData) {
        const link = document.createElement('a');
        link.href = window.capturedImageData;
        link.download = 'capture-' + new Date().toISOString().slice(0, 19) + '.jpg';
        link.click();
        showAlert('Image saved to downloads', 'success');
    }
}

function clearResults() {
    detectionResults = [];
    updateDetectionResults([]);
    clearDetectionOverlay();
    showAlert('Detection results cleared', 'info');
}

function exportResults() {
    if (detectionResults.length === 0) {
        showAlert('No results to export', 'warning');
        return;
    }
    
    const csv = convertToCSV(detectionResults);
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    
    const link = document.createElement('a');
    link.href = url;
    link.download = 'detection-results-' + new Date().toISOString().slice(0, 19) + '.csv';
    link.click();
    URL.revokeObjectURL(url);
    showAlert('Results exported as CSV', 'success');
}

function convertToCSV(results) {
    const headers = ['Timestamp', 'Type', 'Label', 'Confidence', 'X', 'Y', 'Width', 'Height'];
    const rows = results.map(result => [
        result.timestamp || '',
        result.type || '',
        result.label || '',
        (result.confidence || 0).toFixed(2),
        Math.round(result.x || 0),
        Math.round(result.y || 0),
        Math.round(result.width || 0),
        Math.round(result.height || 0)
    ]);
    rows.unshift(headers);
    return rows.map(row => row.map(item => '"' + String(item).replace(/"/g, '""') + '"').join(',')).join('\n');
}

function copyStreamUrl() {
    const url = activeStreamUrlEl.value;
    if (url) {
        navigator.clipboard.writeText(url).then(() => {
            showAlert('Stream URL copied to clipboard', 'success');
        });
    }
}

function copyGeneratedUrl() {
    const url = document.getElementById('generated-url').value;
    if (url) {
        navigator.clipboard.writeText(url).then(() => {
            showAlert('URL copied to clipboard', 'success');
            bootstrap.Modal.getInstance(document.getElementById('urlBuilderModal')).hide();
        });
    }
}

function showResults() {
    document.querySelector('.card-body').scrollIntoView({ behavior: 'smooth', block: 'end' });
}

function toggleFullscreen() {
    const streamContainer = document.querySelector('.stream-container');
    if (!document.fullscreenElement) {
        streamContainer.requestFullscreen().catch(err => {
            showAlert('Error attempting to enable fullscreen: ' + err.message, 'danger');
        });
    } else {
        document.exitFullscreen();
    }
}

// FPS Counter
let frameCount = 0;
let lastTime = performance.now();
let fps = 0;

function startFpsCounter() {
    function update() {
        frameCount++;
        const now = performance.now();
        const elapsed = now - lastTime;
        
        if (elapsed >= 1000) {
            fps = Math.round(frameCount * 1000 / elapsed);
            fpsBadgeEl.textContent = 'FPS: ' + fps;
            frameCount = 0;
            lastTime = now;
        }
        
        if (videoElement && !videoElement.paused) {
            requestAnimationFrame(update);
        }
    }
    update();
}

function showAlert(message, type = 'info') {
    const alertId = 'alert-' + Date.now();
    const alertHtml = `
        <div id="${alertId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    const toast = new bootstrap.Toast(document.getElementById(alertId), { delay: 3000 });
    toast.show();
    
    setTimeout(() => {
        const element = document.getElementById(alertId);
        if (element) element.remove();
    }, 4000);
}

window.addEventListener('resize', function() {
    if (videoElement && videoElement.videoWidth > 0) {
        // Canvas will be resized by the video element's resize event
    }
});

window.addEventListener('beforeunload', async function() {
    await disconnectCamera();
});

document.getElementById('camera-ip').addEventListener('input', buildStreamUrl);
document.getElementById('stream-protocol').addEventListener('change', buildStreamUrl);
document.getElementById('stream-port').addEventListener('input', buildStreamUrl);
document.getElementById('stream-path').addEventListener('input', buildStreamUrl);
document.getElementById('camera-username').addEventListener('input', buildStreamUrl);
document.getElementById('camera-password').addEventListener('input', buildStreamUrl);
</script>
@endsection