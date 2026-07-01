/**
 * WebSocket Camera Control Client
 * Real-time camera control with <10ms latency
 * 
 * Usage:
 * const cameraWS = new CameraWebSocket('ws://127.0.0.1:8001/ws/camera');
 * 
 * // Start camera
 * await cameraWS.startCamera();
 * 
 * // Stop camera
 * await cameraWS.stopCamera();
 * 
 * // Check status
 * const status = await cameraWS.getStatus();
 */

class CameraWebSocket {
    constructor(url) {
        this.url = url;
        this.socket = null;
        this.listeners = {};
        this.requestId = 0;
        this.pendingRequests = new Map();
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 1000; // 1 second
        
        // Auto-reconnect flag
        this.shouldReconnect = false;
        
        // Connect immediately
        this.connect();
    }
    
    /**
     * Connect to WebSocket server
     */
    connect() {
        this.shouldReconnect = true;
        
        try {
            // Close existing connection
            if (this.socket && this.socket.readyState === WebSocket.OPEN) {
                this.socket.close();
            }
            
            this.socket = new WebSocket(this.url);
            
            this.socket.onopen = () => {
                this.reconnectAttempts = 0;
                console.log('[WS] Camera WebSocket connected');
                this.emit('connected');
                
                // Send ping to check connection
                this.send({ action: 'ping', timestamp: Date.now() });
            };
            
            this.socket.onclose = (event) => {
                console.log(`[WS] Camera WebSocket closed: code=${event.code}, reason=${event.reason}`);
                this.emit('disconnected', event);
                
                if (this.shouldReconnect && this.reconnectAttempts < this.maxReconnectAttempts) {
                    this.reconnectAttempts++;
                    setTimeout(() => this.connect(), this.reconnectDelay * this.reconnectAttempts);
                }
            };
            
            this.socket.onerror = (error) => {
                console.error('[WS] Camera WebSocket error:', error);
                this.emit('error', error);
            };
            
            this.socket.onmessage = (event) => {
                try {
                    const message = JSON.parse(event.data);
                    this.handleMessage(message);
                } catch (e) {
                    console.error('[WS] Error parsing message:', e);
                }
            };
            
        } catch (error) {
            console.error('[WS] Failed to create WebSocket:', error);
            this.emit('error', error);
        }
    }
    
    /**
     * Disconnect from WebSocket server
     */
    disconnect() {
        this.shouldReconnect = false;
        if (this.socket) {
            this.socket.close();
        }
    }
    
    /**
     * Send message to server
     */
    send(message) {
        if (!this.socket || this.socket.readyState !== WebSocket.OPEN) {
            console.warn('[WS] WebSocket not connected, cannot send message');
            return false;
        }
        
        try {
            this.socket.send(JSON.stringify(message));
            return true;
        } catch (error) {
            console.error('[WS] Failed to send message:', error);
            return false;
        }
    }
    
    /**
     * Handle incoming messages
     */
    handleMessage(message) {
        // Handle pong response
        if (message.action === 'pong') {
            this.emit('pong', message);
            return;
        }
        
        // Handle status updates
        if (message.action === 'status_update') {
            this.emit('status_update', message);
            return;
        }
        
        // Handle IP status changes (from other clients)
        if (message.action === 'ip_status_changed') {
            this.emit('ip_status_changed', message);
            return;
        }
        
        // Handle IP connected/disconnected
        if (message.action === 'ip_connected' || message.action === 'ip_disconnected') {
            this.emit('ip_connection_change', message);
            return;
        }
        
        // Handle error messages
        if (message.action === 'error') {
            this.emit('error', message);
            // Also resolve any pending request with error
            const requestId = message.request_id;
            if (requestId && this.pendingRequests.has(requestId)) {
                const { resolve } = this.pendingRequests.get(requestId);
                resolve({ success: false, error: message.error, request_id: requestId });
                this.pendingRequests.delete(requestId);
            }
            return;
        }
        
        // Handle responses to our requests
        if (message.action === 'response') {
            const requestId = message.request_id;
            if (requestId && this.pendingRequests.has(requestId)) {
                const { resolve } = this.pendingRequests.get(requestId);
                resolve(message);
                this.pendingRequests.delete(requestId);
            }
            return;
        }
        
        // Emit generic message event
        this.emit('message', message);
    }
    
    /**
     * Send request and wait for response
     */
    async request(action, data = {}) {
        return new Promise((resolve, reject) => {
            const requestId = `req_${Date.now()}_${this.requestId++}`;
            
            // Store the promise resolver
            this.pendingRequests.set(requestId, { resolve, reject });
            
            // Send the request
            const message = { action, request_id: requestId, ...data };
            
            // Try to send, if fails, reject immediately
            if (!this.send(message)) {
                this.pendingRequests.delete(requestId);
                reject(new Error('WebSocket not connected'));
            }
            
            // Set timeout
            setTimeout(() => {
                if (this.pendingRequests.has(requestId)) {
                    this.pendingRequests.delete(requestId);
                    reject(new Error('Request timeout'));
                }
            }, 5000); // 5 second timeout
        });
    }
    
    // Event emitter methods
    on(eventName, callback) {
        if (!this.listeners[eventName]) {
            this.listeners[eventName] = [];
        }
        this.listeners[eventName].push(callback);
        return this;
    }
    
    off(eventName, callback) {
        if (this.listeners[eventName]) {
            this.listeners[eventName] = this.listeners[eventName].filter(
                cb => cb !== callback
            );
        }
        return this;
    }
    
    emit(eventName, ...args) {
        if (this.listeners[eventName]) {
            this.listeners[eventName].forEach(callback => {
                try {
                    callback(...args);
                } catch (error) {
                    console.error(`[WS] Error in ${eventName} listener:`, error);
                }
            });
        }
        return this;
    }
    
    // Camera control methods
    
    /**
     * Start camera for current IP
     */
    async startCamera() {
        const response = await this.request('start_camera');
        if (!response.success) {
            throw new Error(response.message || 'Failed to start camera');
        }
        return response;
    }
    
    /**
     * Stop camera for current IP
     */
    async stopCamera() {
        const response = await this.request('stop_camera');
        if (!response.success) {
            throw new Error(response.message || 'Failed to stop camera');
        }
        return response;
    }
    
    /**
     * Get camera status for current IP
     */
    async getStatus() {
        const response = await this.request('get_status');
        if (!response.success) {
            throw new Error(response.message || 'Failed to get status');
        }
        return response;
    }
    
    /**
     * Clear stopped status for current IP
     */
    async clearStop() {
        const response = await this.request('clear_stop');
        if (!response.success) {
            throw new Error(response.message || 'Failed to clear stop status');
        }
        return response;
    }
    
    /**
     * Get list of all active IPs
     */
    async getActiveIps() {
        const response = await this.request('get_active_ips');
        if (!response.success) {
            throw new Error(response.message || 'Failed to get active IPs');
        }
        return response;
    }
    
    /**
     * Check if camera is available for current IP
     */
    async isAllowed() {
        const status = await this.getStatus();
        return status.is_allowed;
    }
    
    /**
     * Check if camera is currently active for current IP
     */
    async isActive() {
        const status = await this.getStatus();
        return status.is_active;
    }
    
    /**
     * Get current IP
     */
    async getCurrentIp() {
        const status = await this.getStatus();
        return status.ip;
    }
}

/**
 * Singleton instance for easy access
 */
let cameraWebSocketInstance = null;

function getCameraWebSocket(url = null) {
    if (!cameraWebSocketInstance) {
        const wsUrl = url || window.CAMERA_WS_URL || 'ws://127.0.0.1:8001/ws/camera';
        cameraWebSocketInstance = new CameraWebSocket(wsUrl);
    }
    return cameraWebSocketInstance;
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { CameraWebSocket, getCameraWebSocket };
}