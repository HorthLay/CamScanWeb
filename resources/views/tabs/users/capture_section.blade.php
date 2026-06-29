{{-- ═══════════════════════════════════════════════════════════════════
     WEBCAM CAPTURE CARD  —  calls FastAPI POST /register/search
     Drop this inside your users/create.blade.php or modal body
═══════════════════════════════════════════════════════════════════ --}}

<style>
.capture-card{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden}
.capture-head{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px}
.capture-head h3{font-size:15px;font-weight:700;color:var(--fg);margin:0}
.capture-body{padding:20px}
.preview-box{position:relative;width:100%;aspect-ratio:4/3;background:#0a0a0a;border-radius:12px;overflow:hidden;margin-bottom:16px;display:flex;align-items:center;justify-content:center}
.preview-box img{width:100%;height:100%;object-fit:cover;display:block}
.preview-placeholder{display:flex;flex-direction:column;align-items:center;gap:10px;color:#555}
.preview-placeholder .material-symbols-outlined{font-size:48px}
.preview-placeholder span{font-size:13px}
.live-badge{position:absolute;top:10px;left:10px;background:#ef4444;color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:20px;display:flex;align-items:center;gap:4px;letter-spacing:.06em}
.live-badge .dot{width:6px;height:6px;background:#fff;border-radius:50%;animation:blink 1s infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:0}}
.oval-guide{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none}
.oval-guide div{width:160px;height:200px;border:2px solid rgba(99,102,241,.6);border-radius:50%}
.countdown-overlay{position:absolute;inset:0;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center;display:none}
.countdown-num{font-size:100px;font-weight:900;color:#fff;line-height:1;text-shadow:0 4px 24px rgba(0,0,0,.5);animation:pop .4s cubic-bezier(.22,1,.36,1)}
@keyframes pop{from{transform:scale(1.5);opacity:0}to{transform:scale(1);opacity:1}}
.smile-text{font-size:32px;font-weight:800;color:#fbbf24;text-shadow:0 4px 24px rgba(0,0,0,.5)}
.status-bar{font-size:13px;color:var(--fg-muted);text-align:center;min-height:20px;margin-bottom:14px;font-family:'JetBrains Mono',monospace}
.capture-btn{width:100%;padding:13px;border-radius:10px;font-size:14px;font-weight:700;font-family:'Space Grotesk',sans-serif;cursor:pointer;border:none;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px}
.capture-btn-start{background:#4f46e5;color:#fff}
.capture-btn-start:hover:not(:disabled){background:#4338ca}
.capture-btn-start:disabled{opacity:.6;cursor:not-allowed}
.capture-btn-save{background:#059669;color:#fff;margin-top:10px}
.capture-btn-save:hover:not(:disabled){background:#047857}
.capture-btn-save:disabled{opacity:.6;cursor:not-allowed}
.capture-btn-retake{background:none;border:1.5px solid var(--border)!important;color:var(--fg-muted);margin-top:8px}
.capture-btn-retake:hover{border-color:var(--accent)!important;color:var(--accent)}
.ai-results{background:var(--input-bg);border:1px solid var(--border);border-radius:10px;padding:14px 16px;margin-bottom:14px;display:none}
.ai-results-title{font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-faint);margin-bottom:10px;font-family:'JetBrains Mono',monospace;display:flex;align-items:center;gap:6px}
.ai-field{margin-bottom:12px}
.ai-field:last-child{margin-bottom:0}
.ai-field label{display:block;font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-muted);margin-bottom:5px;font-family:'JetBrains Mono',monospace}
.ai-field input,.ai-field select{width:100%;padding:9px 12px;border-radius:8px;background:var(--card);border:1.5px solid var(--input-border);color:var(--fg);font-size:13.5px;font-family:'Space Grotesk',sans-serif;outline:none;transition:border .2s}
.ai-field input:focus,.ai-field select:focus{border-color:var(--accent)}
.cap-toggle{display:flex;align-items:center;gap:10px;margin-top:2px}
.cap-toggle input{width:auto}
.ai-badge{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;padding:2px 7px;border-radius:20px;background:#eef2ff;color:#4f46e5;font-weight:600;vertical-align:middle;margin-left:6px}
.dark .ai-badge{background:#1e1b4b;color:#a5b4fc}
.spin{animation:spin .65s linear infinite;display:inline-block}
@keyframes spin{to{transform:rotate(360deg)}}
</style>

{{-- ── CAPTURE CARD ── --}}
<div class="capture-card">
  <div class="capture-head">
    <span class="material-symbols-outlined" style="font-size:20px;color:var(--accent)">photo_camera</span>
    <h3>Webcam Capture <span style="font-size:12px;font-weight:500;color:var(--fg-faint)">(3-2-1 countdown)</span></h3>
  </div>

  <div class="capture-body">

    {{-- Preview box --}}
    <div class="preview-box" id="preview-box">
      {{-- Placeholder --}}
      <div class="preview-placeholder" id="preview-placeholder">
        <span class="material-symbols-outlined">videocam_off</span>
        <span>Click Start to begin countdown</span>
      </div>

      {{-- Captured image (shown after /register/capture returns) --}}
      <img id="captured-img" alt="Captured face" style="display:none"/>

      {{-- Live badge --}}
      <div class="live-badge" id="live-badge" style="display:none">
        <span class="dot"></span> LIVE
      </div>

      {{-- Countdown overlay --}}
      <div class="countdown-overlay" id="countdown-overlay">
        <span class="countdown-num" id="countdown-num">3</span>
      </div>

      {{-- Face oval guide --}}
      <div class="oval-guide" id="oval-guide" style="display:none">
        <div></div>
      </div>
    </div>

    {{-- Status text --}}
    <p class="status-bar" id="cap-status">Ready — press Start Countdown</p>

    {{-- AI results (shown after capture) --}}
    <div class="ai-results" id="ai-results">
      <div class="ai-results-title">
        <span class="material-symbols-outlined" style="font-size:14px">auto_awesome</span>
        Mistral AI — auto-detected
        <span class="ai-badge">AI</span>
      </div>

      {{-- Name (required, user must fill) --}}
      <div class="ai-field">
        <label>Full name <span style="color:#ef4444">*</span></label>
        <input type="text" id="cap-name" placeholder="Enter full name" required/>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        {{-- Age --}}
        <div class="ai-field">
          <label>Estimated age</label>
          <input type="number" id="cap-age" placeholder="—" min="1" max="120"/>
        </div>
        {{-- Gender --}}
        <div class="ai-field">
          <label>Gender</label>
          <select id="cap-gender">
            <option value="">—</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        {{-- Password --}}
        <div class="ai-field">
          <label>Password <span style="color:#ef4444">*</span></label>
          <input type="password" id="cap-password" placeholder="Min 8 characters" required/>
        </div>
        {{-- Role --}}
        <div class="ai-field">
          <label>Role <span style="color:#ef4444">*</span></label>
          <select id="cap-role" required>
            <option value="">Select role</option>
            @foreach($roles as $role)
              <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- Account status --}}
      <div class="ai-field">
        <label>Account status</label>
        <label class="cap-toggle">
          <input type="checkbox" id="cap-active" value="1"/>
          <span style="font-size:13px;color:var(--fg-muted)">Active user</span>
        </label>
      </div>

      {{-- AI notes (read-only) --}}
      <div class="ai-field">
        <label>AI notes</label>
        <input type="text" id="cap-notes" readonly style="opacity:.7;cursor:default"/>
      </div>
    </div>

    {{-- Hidden base64 image storage --}}
    <input type="hidden" id="cap-image-b64"/>

    {{-- Buttons --}}
    <button class="capture-btn capture-btn-start" id="btn-start" onclick="startCapture()">
      <span class="material-symbols-outlined" style="font-size:18px">play_circle</span>
      Start Countdown
    </button>

    <button class="capture-btn capture-btn-retake" id="btn-retake" style="display:none" onclick="retakeCapture()">
      <span class="material-symbols-outlined" style="font-size:16px">replay</span>
      Retake
    </button>

    <button class="capture-btn capture-btn-save" id="btn-save" style="display:none" onclick="saveUser()">
      <span class="material-symbols-outlined" style="font-size:16px">person_add</span>
      Save User
    </button>

  </div>
</div>

<script>
// ── Config ───────────────────────────────────────────────────────────────────
const FASTAPI_SEARCH_URL   = "{{ rtrim(config('services.fastapi.url') ?? 'http://localhost:8001', '/') }}/register/search";
const LARAVEL_CAPTURE_REGISTER_URL = window.ROUTES?.captureStore ?? "{{ route('users.capture-register') }}";
const CSRF_TOKEN           = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ── State ────────────────────────────────────────────────────────────────────
let capturedB64   = null;   // base64 image from FastAPI
let capturedBlob  = null;   // converted to Blob for form submit
let isCapturing   = false;

// ── Elements ─────────────────────────────────────────────────────────────────
const elStatus      = () => document.getElementById('cap-status');
const elBtnStart    = () => document.getElementById('btn-start');
const elBtnRetake   = () => document.getElementById('btn-retake');
const elBtnSave     = () => document.getElementById('btn-save');
const elAiResults   = () => document.getElementById('ai-results');
const elCapturedImg = () => document.getElementById('captured-img');
const elPlaceholder = () => document.getElementById('preview-placeholder');
const elLiveBadge   = () => document.getElementById('live-badge');
const elOvalGuide   = () => document.getElementById('oval-guide');
const elCountdown   = () => document.getElementById('countdown-overlay');
const elCountNum    = () => document.getElementById('countdown-num');


// ── STEP 1: Hit FastAPI /register/capture ─────────────────────────────────────
// FastAPI handles the webcam + TTS countdown + InsightFace + Mistral internally.
// We just show a visual countdown in the browser in sync.

async function startCapture() {
    if (isCapturing) return;
    isCapturing = true;

    elBtnStart().disabled = true;
    elBtnRetake().style.display = 'none';
    elBtnSave().style.display   = 'none';
    elAiResults().style.display = 'none';
    elCapturedImg().style.display = 'none';
    elPlaceholder().style.display = 'none';
    elLiveBadge().style.display   = 'flex';
    elOvalGuide().style.display   = 'flex';

    // Visual countdown in browser (mirrors FastAPI voice countdown)
    await runVisualCountdown();

    setStatus('<span class="spin material-symbols-outlined" style="font-size:15px;vertical-align:middle">refresh</span> Capturing & analyzing with Mistral AI…');

    try {
        const res = await fetch(FASTAPI_SEARCH_URL, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
        });

        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.detail ?? `HTTP ${res.status}`);
        }

        const data = await res.json();

        if (!data.success) throw new Error(data.message ?? 'Capture failed.');

        // ── Show captured image ───────────────────────────────────────────────
        capturedB64 = data.image_base64;
        capturedBlob = await b64ToBlob(capturedB64, 'image/jpeg');

        elCapturedImg().src           = 'data:image/jpeg;base64,' + capturedB64;
        elCapturedImg().style.display = 'block';
        elLiveBadge().style.display   = 'none';
        elOvalGuide().style.display   = 'none';
        elCountdown().style.display   = 'none';

        // ── Fill AI fields ────────────────────────────────────────────────────
        document.getElementById('cap-age').value      = data.estimated_age ?? '';
        document.getElementById('cap-gender').value   = normalizeGender(data.gender);
        document.getElementById('cap-notes').value    = data.ai_notes ?? '';

        elAiResults().style.display = 'block';
        elBtnRetake().style.display = 'flex';
        elBtnSave().style.display   = 'flex';
        elBtnStart().style.display  = 'none';

        setStatus('✓ Captured! Fill in the name then click Save User.');

    } catch (e) {
        setStatus('⚠ ' + e.message);
        elLiveBadge().style.display   = 'none';
        elOvalGuide().style.display   = 'none';
        elCountdown().style.display   = 'none';
        elPlaceholder().style.display = 'flex';
        elBtnStart().disabled         = false;
    } finally {
        isCapturing = false;
    }
}

// ── Visual countdown (3 → 2 → 1 → Smile!) ───────────────────────────────────
async function runVisualCountdown() {
    const overlay = elCountdown();
    const numEl   = elCountNum();
    overlay.style.display = 'flex';

    for (const label of ['3', '2', '1']) {
        numEl.className   = 'countdown-num';
        numEl.textContent = label;
        // Re-trigger animation
        void numEl.offsetWidth;
        numEl.className = 'countdown-num';
        setStatus(`Get ready… ${label}`);
        await sleep(1000);
    }

    numEl.className   = 'smile-text';
    numEl.textContent = '😊 Smile!';
    setStatus('Smile!');
    await sleep(600);
}

// ── Retake ────────────────────────────────────────────────────────────────────
function retakeCapture() {
    capturedB64  = null;
    capturedBlob = null;

    elCapturedImg().style.display = 'none';
    elAiResults().style.display   = 'none';
    elBtnRetake().style.display   = 'none';
    elBtnSave().style.display     = 'none';
    elBtnStart().style.display    = 'flex';
    elBtnStart().disabled         = false;
    elPlaceholder().style.display = 'flex';
    elCountdown().style.display   = 'none';

    document.getElementById('cap-name').value     = '';
    document.getElementById('cap-age').value      = '';
    document.getElementById('cap-gender').value   = '';
    document.getElementById('cap-password').value = '';
    document.getElementById('cap-role').value     = '';
    document.getElementById('cap-active').checked = false;
    document.getElementById('cap-notes').value    = '';

    setStatus('Ready — press Start Countdown');
}

// ── STEP 2: Save confirmed user ───────────────────────────────────────────────
async function saveUser() {
    const name = document.getElementById('cap-name').value.trim();
    const gender = document.getElementById('cap-gender').value;
    const password = document.getElementById('cap-password').value;
    const roleId = document.getElementById('cap-role').value;

    if (!name) {
        showCaptureToast('Please enter the full name.', 'error');
        document.getElementById('cap-name').focus();
        return;
    }
    if (!gender) {
        showCaptureToast('Please choose a gender.', 'error');
        document.getElementById('cap-gender').focus();
        return;
    }
    if (password.length < 8) {
        showCaptureToast('Password must be at least 8 characters.', 'error');
        document.getElementById('cap-password').focus();
        return;
    }
    if (!roleId) {
        showCaptureToast('Please choose a role.', 'error');
        document.getElementById('cap-role').focus();
        return;
    }
    if (!capturedBlob) {
        showCaptureToast('No photo captured. Please retake.', 'error');
        return;
    }

    elBtnSave().disabled  = true;
    elBtnSave().innerHTML = `<span class="spin material-symbols-outlined" style="font-size:16px">refresh</span> Saving…`;

    const form = new FormData();
    form.append('face_image', capturedBlob, 'face.jpg');
    form.append('name',       name);
    form.append('gender',     gender);
    form.append('password',   password);
    form.append('role_id',    roleId);
    if (document.getElementById('cap-active').checked) {
        form.append('active', '1');
    }

    try {
        const res  = await fetch(LARAVEL_CAPTURE_REGISTER_URL, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body:   form,
        });

        const data = await res.json();

        if (res.ok && data.success) {
            showCaptureToast(data.message ?? `User "${data.name}" saved successfully!`, 'success');
            setStatus(`✓ Saved as user #${data.user_id}`);
            elBtnSave().innerHTML = `<span class="material-symbols-outlined" style="font-size:16px">check_circle</span> Saved!`;

            // Redirect to users list after 1.5 s
            setTimeout(() => { window.location.href = '/users'; }, 1500);
        } else {
            throw new Error(data.message ?? data.detail ?? firstValidationError(data) ?? 'Save failed.');
        }

    } catch (e) {
        showCaptureToast('Error: ' + e.message, 'error');
        elBtnSave().disabled  = false;
        elBtnSave().innerHTML = `<span class="material-symbols-outlined" style="font-size:16px">person_add</span> Save User`;
    }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function setStatus(html) {
    elStatus().innerHTML = html;
}

function sleep(ms) {
    return new Promise(r => setTimeout(r, ms));
}

async function b64ToBlob(b64, mime) {
    const res  = await fetch('data:' + mime + ';base64,' + b64);
    return await res.blob();
}

function normalizeGender(value) {
    const gender = String(value ?? '').trim().toLowerCase();
    if (gender === 'male' || gender === 'female') return gender;
    if (gender) return 'other';
    return '';
}

function firstValidationError(data) {
    if (!data || !data.errors) return null;
    const firstKey = Object.keys(data.errors)[0];
    return firstKey ? data.errors[firstKey][0] : null;
}

function showCaptureToast(msg, type) {
    // Reuse existing showToast if available, else fallback
    if (typeof showToast === 'function') {
        showToast(msg, type);
    } else {
        alert(msg);
    }
}
</script>
