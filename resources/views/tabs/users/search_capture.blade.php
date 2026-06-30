{{-- ═══════════════════════════════════════════════════════════════════
     WEBCAM SEARCH & IDENTIFY CARD  —  calls FastAPI POST /register/search
     Mimics the surveillance dashboard layout from test.html
═══════════════════════════════════════════════════════════════════ --}}

<style>
.search-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 24px;
    margin-bottom: 8px;
}
@media (max-width: 768px) {
    .search-grid {
        grid-template-columns: 1fr;
    }
}
.search-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 20px;
    position: relative;
    overflow: hidden;
    transition: border-color 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
}
.search-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: transparent;
    transition: background 0.3s;
}
.search-card.scanning-active {
    border-color: rgba(5, 150, 105, 0.4);
    box-shadow: 0 4px 20px var(--accent-glow);
}
.search-card.scanning-active::before {
    background: var(--accent);
}
.search-card.match-success {
    border-color: rgba(16, 185, 129, 0.4);
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.1);
}
.search-card.match-success::before {
    background: #10b981;
}
.search-card.match-failed {
    border-color: rgba(239, 68, 68, 0.4);
    box-shadow: 0 4px 20px rgba(239, 68, 68, 0.1);
}
.search-card.match-failed::before {
    background: #ef4444;
}

.search-title {
    font-size: 12.5px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--fg-muted);
    margin-bottom: 16px;
    font-weight: 700;
    font-family: 'JetBrains Mono', monospace;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.cam-container {
    position: relative;
    width: 100%;
    aspect-ratio: 4 / 3;
    border-radius: 10px;
    overflow: hidden;
    background: #020617;
    border: 1px solid var(--border);
    box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.6);
}
.cam-feed {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.scan-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    display: none;
}
.scan-line {
    width: 100%;
    height: 3px;
    background: linear-gradient(to right, transparent, var(--accent), transparent);
    position: absolute;
    top: 0;
    left: 0;
    animation: search-scan 2s linear infinite;
    box-shadow: 0 0 8px var(--accent);
}
@keyframes search-scan {
    0% { top: 0%; }
    50% { top: 100%; }
    100% { top: 0%; }
}
.scan-pulse-box {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    height: 80%;
    border: 2px dashed rgba(5, 150, 105, 0.4);
    border-radius: 8px;
    animation: search-pulse-frame 2s infinite ease-in-out;
}
@keyframes search-pulse-frame {
    0%, 100% { opacity: 0.3; transform: translate(-50%, -50%) scale(0.95); }
    50% { opacity: 0.8; transform: translate(-50%, -50%) scale(1.02); }
}

.btn-search-trigger {
    width: 100%;
    margin-top: 18px;
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 14px;
    font-size: 14px;
    font-weight: 700;
    font-family: 'Space Grotesk', sans-serif;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 12px var(--accent-glow);
    transition: all 0.2s;
}
.dark .btn-search-trigger {
    color: #022c22;
}
.btn-search-trigger:hover:not(:disabled) {
    background: var(--accent-hover);
    transform: translateY(-1px);
}
.btn-search-trigger:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    box-shadow: none;
}

.badge-match {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.badge-match.standby {
    background: var(--input-bg);
    color: var(--fg-muted);
    border: 1px solid var(--border);
}
.badge-match.success {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}
.badge-match.danger {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}
.badge-match.loading {
    background: var(--accent-soft);
    color: var(--accent);
    border: 1px solid var(--accent);
}

.compare-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 18px;
}
.compare-box {
    position: relative;
    background: #0a0a0a;
    border-radius: 10px;
    border: 1px solid var(--border);
    overflow: hidden;
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}
.compare-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.compare-box .placeholder-txt {
    color: var(--fg-faint);
    font-size: 12px;
    font-weight: 500;
    text-align: center;
    padding: 10px;
}
.compare-box .box-lbl {
    position: absolute;
    bottom: 8px;
    left: 8px;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 9px;
    font-weight: 700;
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.1);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.conf-section {
    display: flex;
    flex-direction: column;
    gap: 6px;
    background: var(--input-bg);
    padding: 14px;
    border-radius: 10px;
    border: 1px solid var(--border);
    margin-bottom: 18px;
}
.conf-label {
    display: flex;
    justify-content: space-between;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--fg-muted);
}
.conf-val {
    font-weight: 700;
    color: var(--fg);
}
.bar-meter {
    width: 100%;
    height: 8px;
    background: var(--border);
    border-radius: 4px;
    overflow: hidden;
}
.bar-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, var(--accent), #10b981);
    border-radius: 4px;
    transition: width 0.8s cubic-bezier(0.1, 0.8, 0.25, 1);
}

.meta-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}
.meta-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--border);
    font-size: 13.5px;
}
.meta-row:last-child {
    border-bottom: none;
}
.meta-key {
    color: var(--fg-muted);
    font-weight: 500;
}
.meta-val {
    font-weight: 600;
    color: var(--fg);
    text-align: right;
    max-width: 65%;
    word-break: break-word;
}
.meta-val.highlight {
    color: var(--accent);
    font-weight: 700;
}
</style>

<div class="search-grid">
    <!-- Left Card: Camera/Surveillance -->
    <div class="search-card" id="searchCameraCard">
        <div class="search-title">
            <span>Live Surveillance Terminal</span>
            <span style="font-size: 11px; color: #10b981; font-weight: 600">● ONLINE</span>
        </div>

        <div class="cam-container">
            <img class="cam-feed" id="search-cam-feed" src="" alt="Camera feed standby">
            <div class="scan-overlay" id="searchScannerOverlay">
                <div class="scan-line"></div>
                <div class="scan-pulse-box"></div>
            </div>
        </div>

        <button class="btn-search-trigger" id="searchTriggerButton" onclick="runFaceSearch()">
            <span class="material-symbols-outlined" style="font-size: 18px">search</span>
            SEARCH & IDENTIFY FACE
        </button>
    </div>

    <!-- Right Card: Verification Results -->
    <div class="search-card" id="searchResultsCard">
        <div class="search-title">
            <span>Identification Analysis</span>
            <span id="searchMatchBadge" class="badge-match standby">STANDBY</span>
        </div>

        <!-- Image comparison -->
        <div class="compare-grid">
            <!-- Database Image -->
            <div class="compare-box">
                <img id="searchDbProfileImage" style="display: none;" alt="DB profile record">
                <div id="searchDbImagePlaceholder" class="placeholder-txt">
                    Database Profile Image
                </div>
                <div class="box-lbl">Database Record</div>
            </div>
            <!-- Captured Frame -->
            <div class="compare-box">
                <img id="searchCapturedImage" style="display: none;" alt="Captured webcam face">
                <div id="searchCapturedImagePlaceholder" class="placeholder-txt">
                    No Capture Yet
                </div>
                <div class="box-lbl">Surveillance Capture</div>
            </div>
        </div>

        <!-- Confidence meter -->
        <div class="conf-section">
            <div class="conf-label">
                <span>Match Confidence</span>
                <span class="conf-val" id="searchConfidenceValue">0%</span>
            </div>
            <div class="bar-meter">
                <div class="bar-fill" id="searchMeterFill"></div>
            </div>
        </div>

        <!-- User detail metadata -->
        <div class="meta-grid">
            <div class="meta-row">
                <span class="meta-key">Full Name</span>
                <span class="meta-val highlight" id="searchUserName">-</span>
            </div>
            <div class="meta-row">
                <span class="meta-key">Role / Position</span>
                <span class="meta-val" id="searchUserPosition">-</span>
            </div>
            <div class="meta-row">
                <span class="meta-key">Age / Gender</span>
                <span class="meta-val" id="searchUserAgeGender">-</span>
            </div>
            <div class="meta-row">
                <span class="meta-key">System User ID</span>
                <span class="meta-val" id="searchUserID">-</span>
            </div>
            <div class="meta-row">
                <span class="meta-key">Status</span>
                <span class="meta-val" id="searchUserStatus">-</span>
            </div>
            <div class="meta-row">
                <span class="meta-key">AI Notes</span>
                <span class="meta-val" id="searchUserNotes">-</span>
            </div>
        </div>
    </div>
</div>

<script>
// ── FastAPI URL Configuration ───────────────────────────────────────────────
const SEARCH_API_BASE = "{{ rtrim(config('services.fastapi.url') ?? 'http://localhost:8001', '/') }}";
const SEARCH_API_URL  = `${SEARCH_API_BASE}/register/search`;

// ── Web Audio Synthesizer Beeps ──────────────────────────────────────────────
function playSearchUIAudio(type) {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);

        if (type === 'success') {
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            gain.gain.setValueAtTime(0.08, ctx.currentTime);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.1);
            
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.frequency.setValueAtTime(1320, ctx.currentTime + 0.15);
            gain2.gain.setValueAtTime(0.08, ctx.currentTime + 0.15);
            osc2.start(ctx.currentTime + 0.15);
            osc2.stop(ctx.currentTime + 0.3);
        } else if (type === 'fail') {
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(150, ctx.currentTime);
            gain.gain.setValueAtTime(0.08, ctx.currentTime);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.35);
        }
    } catch (e) {
        console.warn("Could not play sound: ", e);
    }
}

// ── Audio Countdown ──────────────────────────────────────────────────────────
function playSearchCountdown() {
    return new Promise((resolve, reject) => {
        const audio = new Audio(`${SEARCH_API_BASE}/register/countdown-audio?ts=${Date.now()}`);
        audio.addEventListener("ended", resolve, { once: true });
        audio.addEventListener("error", reject, { once: true });
        audio.play().catch(reject);
    });
}

// ── Stream Lifecycle Controller ──────────────────────────────────────────────
function startSearchStream() {
    const feed = document.getElementById('search-cam-feed');
    if (feed) {
        feed.src = `${SEARCH_API_BASE}/video_feed?ts=${Date.now()}`;
    }
    resetSearchUI();
}

function stopSearchStream() {
    const feed = document.getElementById('search-cam-feed');
    if (feed) {
        feed.src = "";
    }
}

// ── Reset UI Fields ──────────────────────────────────────────────────────────
function resetSearchUI() {
    document.getElementById("searchCameraCard").className = "search-card";
    document.getElementById("searchResultsCard").className = "search-card";
    
    const badge = document.getElementById("searchMatchBadge");
    badge.className = "badge-match standby";
    badge.textContent = "STANDBY";

    document.getElementById("searchDbProfileImage").style.display = "none";
    const dbPlaceholder = document.getElementById("searchDbImagePlaceholder");
    dbPlaceholder.textContent = "Database Profile Image";
    dbPlaceholder.style.display = "block";

    document.getElementById("searchCapturedImage").style.display = "none";
    const capPlaceholder = document.getElementById("searchCapturedImagePlaceholder");
    capPlaceholder.textContent = "No Capture Yet";
    capPlaceholder.style.display = "block";

    document.getElementById("searchConfidenceValue").textContent = "0%";
    document.getElementById("searchMeterFill").style.width = "0%";

    document.getElementById("searchUserName").textContent = "-";
    document.getElementById("searchUserPosition").textContent = "-";
    document.getElementById("searchUserAgeGender").textContent = "-";
    document.getElementById("searchUserID").textContent = "-";
    document.getElementById("searchUserStatus").textContent = "-";
    document.getElementById("searchUserNotes").textContent = "-";
}

// ── Search Action ────────────────────────────────────────────────────────────
async function runFaceSearch() {
    const btn = document.getElementById("searchTriggerButton");
    const overlay = document.getElementById("searchScannerOverlay");
    const cameraCard = document.getElementById("searchCameraCard");
    const resultsCard = document.getElementById("searchResultsCard");
    const badge = document.getElementById("searchMatchBadge");

    const dbImage = document.getElementById("searchDbProfileImage");
    const dbPlaceholder = document.getElementById("searchDbImagePlaceholder");
    const capImage = document.getElementById("searchCapturedImage");
    const capPlaceholder = document.getElementById("searchCapturedImagePlaceholder");

    const confVal = document.getElementById("searchConfidenceValue");
    const meterFill = document.getElementById("searchMeterFill");

    const userName = document.getElementById("searchUserName");
    const userPos = document.getElementById("searchUserPosition");
    const userAgeGen = document.getElementById("searchUserAgeGender");
    const userId = document.getElementById("searchUserID");
    const userNotes = document.getElementById("searchUserNotes");

    btn.disabled = true;
    overlay.style.display = "block";
    
    cameraCard.className = "search-card scanning-active";
    resultsCard.className = "search-card scanning-active";
    badge.className = "badge-match loading";
    badge.textContent = "SCANNING...";

    try {
        // Step 1: Play Audio Countdown Beeps
        await playSearchCountdown();

        badge.textContent = "MATCHING...";

        // Step 2: Hit /register/search Endpoint
        const res = await fetch(`${SEARCH_API_URL}?server_countdown=false`, {
            method: "POST"
        });
        
        const data = await res.json();
        
        if (!res.ok || !data.success) {
            throw new Error(data.message ?? data.detail ?? "Search failed");
        }

        // Show Captured face
        capImage.src = `data:image/jpeg;base64,${data.image_base64}`;
        capImage.style.display = "block";
        capPlaceholder.style.display = "none";

        if (data.matched && data.user) {
            // Success match
            playSearchUIAudio('success');
            
            cameraCard.className = "search-card match-success";
            resultsCard.className = "search-card match-success";
            badge.className = "badge-match success";
            badge.textContent = "USER MATCHED";

            if (data.user.face_image) {
                dbImage.src = `${SEARCH_API_BASE}/${data.user.face_image}?ts=${Date.now()}`;
                dbImage.style.display = "block";
                dbPlaceholder.style.display = "none";
            } else {
                dbImage.style.display = "none";
                dbPlaceholder.textContent = "No Stored Image";
                dbPlaceholder.style.display = "block";
            }

            const pct = Math.round(parseFloat(data.user.confidence ?? 0) * 100);
            confVal.textContent = `${pct}%`;
            meterFill.style.width = `${pct}%`;

            userName.textContent = data.user.name || "Unknown User";
            userPos.textContent = data.user.position || "No Role Assigned";
            
            const age = data.user.age ?? "-";
            const gen = data.user.gender ?? "-";
            userAgeGen.textContent = `${age} / ${gen}`;
            
            userId.textContent = `#${data.user.id}`;
            
            // Display status with badge styling
            const note = data.user.note;
            const statusEl = document.getElementById("searchUserStatus");
            if (note) {
                const noteText = note.charAt(0).toUpperCase() + note.slice(1); // Capitalize first letter
                const badgeClass = note === 'work' ? 'badge-active' : note === 'resign' ? 'badge-inactive' : 'badge-unverified';
                statusEl.innerHTML = `<span class="badge ${badgeClass}">${noteText}</span>`;
            } else {
                statusEl.textContent = "-";
            }
            
            userNotes.textContent = data.user.ai_notes || "No system notes available.";

            // Trigger Laravel view notification if needed
            if (typeof showToast === 'function') {
                showToast(`Access granted: ${data.user.name}`, 'success');
            }
        } else {
            // Fail match
            playSearchUIAudio('fail');
            
            cameraCard.className = "search-card match-failed";
            resultsCard.className = "search-card match-failed";
            badge.className = "badge-match danger";
            badge.textContent = "UNKNOWN FACE";

            dbImage.style.display = "none";
            dbPlaceholder.textContent = "Access Denied";
            dbPlaceholder.style.display = "block";

            confVal.textContent = "0%";
            meterFill.style.width = "0%";

            userName.textContent = "ACCESS DENIED";
            userPos.textContent = "UNAUTHORIZED PERSONNEL";
            userAgeGen.textContent = "-";
            userId.textContent = "N/A";
            document.getElementById("searchUserStatus").textContent = "-";
            userNotes.textContent = "No matching face records found in database directory.";

            if (typeof showToast === 'function') {
                showToast("Unknown personnel face scan failed", 'error');
            }
        }
    } catch (e) {
        console.error(e);
        playSearchUIAudio('fail');
        
        cameraCard.className = "search-card match-failed";
        resultsCard.className = "search-card match-failed";
        badge.className = "badge-match danger";
        badge.textContent = "ERROR";

        userName.textContent = "SCAN ERROR";
        document.getElementById("searchUserStatus").textContent = "-";
        userNotes.textContent = e.message || "FastAPI connection timeout or webcam hardware issue.";
    } finally {
        btn.disabled = false;
        overlay.style.display = "none";
    }
}
</script>
