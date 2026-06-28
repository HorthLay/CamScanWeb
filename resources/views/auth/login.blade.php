{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>CamScan | Login</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      fontFamily: {
        display: ['Space Grotesk', 'sans-serif'],
        mono: ['JetBrains Mono', 'monospace'],
      }
    }
  }
}
</script>
<style>
:root {
  --bg: #eef1f0;
  --card: #ffffff;
  --fg: #0f1a15;
  --fg-muted: #536359;
  --fg-faint: #93a59b;
  --border: #d0d9d4;
  --border-focus: #059669;
  --accent: #059669;
  --accent-hover: #047857;
  --accent-soft: #ecfdf5;
  --accent-glow: rgba(5,150,105,0.18);
  --panel-bg: #041f17;
  --panel-fg: #d1fae5;
  --panel-muted: #6ee7b7;
  --input-bg: #f4f7f6;
  --err-bg: #fef2f2;
  --err-fg: #991b1b;
  --err-bdr: #fecaca;
  --btn-text: #ffffff;
}
.dark {
  --bg: #070c0a;
  --card: #101a15;
  --fg: #e0ebe4;
  --fg-muted: #7a8f83;
  --fg-faint: #3e5349;
  --border: #1b2d24;
  --border-focus: #34d399;
  --accent: #34d399;
  --accent-hover: #6ee7b7;
  --accent-soft: rgba(52,211,153,0.08);
  --accent-glow: rgba(52,211,153,0.14);
  --panel-bg: #060f0b;
  --panel-fg: #a7f3d0;
  --panel-muted: #34d399;
  --input-bg: #0c1611;
  --err-bg: rgba(239,68,68,0.1);
  --err-fg: #fca5a5;
  --err-bdr: rgba(239,68,68,0.25);
  --btn-text: #022c22;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Space Grotesk',sans-serif;background:var(--bg);color:var(--fg);min-height:100vh;display:flex;transition:background .4s,color .4s}
.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;vertical-align:middle;line-height:1}

/* ── Layout ── */
.login-wrap{display:flex;width:100%;min-height:100vh}
.brand-side{width:46%;background:var(--panel-bg);position:relative;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:3rem;overflow:hidden;flex-shrink:0}
.form-side{flex:1;display:flex;align-items:center;justify-content:center;padding:2.5rem;position:relative;overflow:hidden}

/* ── Brand panel ── */
.brand-grid{position:absolute;inset:0;background-image:linear-gradient(var(--border) 1px,transparent 1px),linear-gradient(90deg,var(--border) 1px,transparent 1px);background-size:52px 52px;opacity:.18;mask-image:radial-gradient(ellipse 70% 70% at 50% 50%,#000 30%,transparent 100%);-webkit-mask-image:radial-gradient(ellipse 70% 70% at 50% 50%,#000 30%,transparent 100%)}
.brand-glow{position:absolute;width:360px;height:360px;border-radius:50%;background:radial-gradient(circle,var(--accent-glow),transparent 70%);top:50%;left:50%;transform:translate(-50%,-50%);pointer-events:none}

/* Radar */
.radar{position:relative;width:210px;height:210px;margin-bottom:2.5rem}
.radar-ring{position:absolute;border:1px solid var(--panel-muted);border-radius:50%;opacity:.12}
.radar-ring:nth-child(1){inset:0}
.radar-ring:nth-child(2){inset:28px}
.radar-ring:nth-child(3){inset:56px}
.radar-ring:nth-child(4){inset:84px}
.radar-h,.radar-v{position:absolute;background:var(--panel-muted);opacity:.08}
.radar-h{width:100%;height:1px;top:50%;left:0}
.radar-v{width:1px;height:100%;left:50%;top:0}
.radar-sweep{position:absolute;inset:0;border-radius:50%;background:conic-gradient(from 0deg,transparent 0%,var(--panel-muted) 6%,transparent 14%);animation:radar-spin 3.2s linear infinite;opacity:.45}
.radar-center{position:absolute;top:50%;left:50%;width:8px;height:8px;border-radius:50%;background:var(--panel-muted);transform:translate(-50%,-50%);box-shadow:0 0 16px var(--panel-muted)}
.radar-blip{position:absolute;width:5px;height:5px;border-radius:50%;background:var(--panel-muted)}
.radar-blip::after{content:'';position:absolute;inset:-4px;border-radius:50%;border:1px solid var(--panel-muted);animation:blip-ping 2.2s ease-out infinite}
@keyframes radar-spin{to{transform:rotate(360deg)}}
@keyframes blip-ping{0%{transform:scale(1);opacity:.4}100%{transform:scale(3);opacity:0}}

/* Particles */
.ptcl{position:absolute;width:2px;height:2px;border-radius:50%;background:var(--panel-muted);opacity:0;animation:ptcl-rise linear infinite}
@keyframes ptcl-rise{0%{transform:translateY(0) translateX(0);opacity:0}8%{opacity:.25}85%{opacity:.25}100%{transform:translateY(-220px) translateX(15px);opacity:0}}

/* ── Form side blobs ── */
.form-blob{position:absolute;border-radius:50%;filter:blur(110px);pointer-events:none;opacity:.45}

/* ── Card ── */
.form-card{width:100%;max-width:400px;position:relative;z-index:2}
.card-enter{animation:card-slide .65s cubic-bezier(.22,1,.36,1) .15s both}
@keyframes card-slide{from{opacity:0;transform:translateX(28px)}to{opacity:1;transform:translateX(0)}}
.panel-enter{animation:panel-in .9s ease both}
@keyframes panel-in{from{opacity:0}to{opacity:1}}

/* ── Fields ── */
.field-group{margin-bottom:1.5rem}
.field-label{font-size:11.5px;font-weight:600;letter-spacing:.09em;text-transform:uppercase;color:var(--fg-muted);margin-bottom:8px;display:flex;align-items:center;gap:6px}
.input-wrap{position:relative;display:flex;align-items:center}
.input-ico{position:absolute;left:14px;color:var(--fg-faint);font-size:20px;pointer-events:none;transition:color .25s}
.input-wrap:focus-within .input-ico{color:var(--accent)}
.auth-input{width:100%;padding:14px 16px 14px 48px;border-radius:12px;background:var(--input-bg);border:1.5px solid var(--border);color:var(--fg);font-size:15px;font-family:'Space Grotesk',sans-serif;transition:all .25s;outline:none}
.auth-input::placeholder{color:var(--fg-faint)}
.auth-input:focus{border-color:var(--border-focus);box-shadow:0 0 0 3px var(--accent-glow);background:var(--card)}
.pw-toggle{position:absolute;right:10px;background:none;border:none;cursor:pointer;color:var(--fg-faint);font-size:20px;padding:5px;display:flex;align-items:center;border-radius:8px;transition:color .2s,background .2s}
.pw-toggle:hover{color:var(--accent);background:var(--accent-soft)}

/* ── Checkbox ── */
.chk-label{display:flex;align-items:center;gap:10px;font-size:13.5px;color:var(--fg-muted);cursor:pointer;user-select:none}
.chk-label input[type=checkbox]{width:16px;height:16px;accent-color:var(--accent);cursor:pointer}

/* ── Submit ── */
.submit-btn{width:100%;padding:15px 24px;background:var(--accent);color:var(--btn-text);font-size:15px;font-weight:600;font-family:'Space Grotesk',sans-serif;border:none;border-radius:12px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .25s;box-shadow:0 4px 24px var(--accent-glow);position:relative;overflow:hidden;letter-spacing:.02em}
.submit-btn:hover{background:var(--accent-hover);transform:translateY(-1px);box-shadow:0 8px 32px var(--accent-glow)}
.submit-btn:active{transform:scale(.98)}
.submit-btn:disabled{opacity:.55;cursor:not-allowed;transform:none}
.ripple{position:absolute;border-radius:50%;background:rgba(255,255,255,.2);transform:scale(0);animation:rip .6s linear;pointer-events:none}
.dark .ripple{background:rgba(0,0,0,.12)}
@keyframes rip{to{transform:scale(4);opacity:0}}
.spinner{width:20px;height:20px;border:2.5px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .65s linear infinite}
.dark .spinner{border-color:rgba(2,44,34,.18);border-top-color:var(--btn-text)}
@keyframes spin{to{transform:rotate(360deg)}}

/* ── Alert ── */
.alert{padding:12px 16px;border-radius:10px;font-size:13.5px;display:flex;align-items:flex-start;gap:10px;animation:alert-in .35s cubic-bezier(.22,1,.36,1);margin-bottom:1.5rem;background:var(--err-bg);color:var(--err-fg);border:1px solid var(--err-bdr)}
@keyframes alert-in{from{opacity:0;transform:translateY(-8px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}

/* ── Theme toggle ── */
.theme-btn{position:fixed;top:1.25rem;right:1.25rem;width:42px;height:42px;border-radius:12px;background:var(--card);border:1px solid var(--border);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);transition:all .25s;z-index:100;box-shadow:0 2px 10px rgba(0,0,0,.06)}
.theme-btn:hover{color:var(--accent);border-color:var(--accent);box-shadow:0 2px 14px var(--accent-glow)}

/* ── Status ── */
.status-bar{font-family:'JetBrains Mono',monospace;font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--fg-faint);display:flex;align-items:center;gap:8px}
.status-dot{width:6px;height:6px;border-radius:50%;background:var(--accent);animation:sdot 2s ease infinite}
@keyframes sdot{0%,100%{opacity:1;box-shadow:0 0 0 0 var(--accent-glow)}50%{opacity:.4;box-shadow:0 0 0 5px transparent}}
.divider{width:100%;height:1px;background:var(--border);margin:1.75rem 0}

/* ── Mobile ── */
.mobile-brand{display:none}
@media(max-width:900px){
  .brand-side{display:none}
  .mobile-brand{display:flex;flex-direction:column;align-items:center;margin-bottom:2rem}
  .form-side{padding:1.5rem}
  .form-card{max-width:100%}
  .theme-btn{top:.75rem;right:.75rem}
}
@media(prefers-reduced-motion:reduce){
  *,*::before,*::after{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important}
}
</style>
</head>
<body>

<div class="login-wrap">

  <!-- ═══ Brand Panel ═══ -->
  <aside class="brand-side panel-enter" id="brand-side">
    <div class="brand-grid"></div>
    <div class="brand-glow"></div>

    <div class="radar" aria-hidden="true">
      <div class="radar-ring"></div>
      <div class="radar-ring"></div>
      <div class="radar-ring"></div>
      <div class="radar-ring"></div>
      <div class="radar-h"></div>
      <div class="radar-v"></div>
      <div class="radar-sweep"></div>
      <div class="radar-blip" style="top:28%;left:67%"></div>
      <div class="radar-blip" style="top:72%;left:32%;animation-delay:.8s"></div>
      <div class="radar-blip" style="top:42%;left:22%;animation-delay:1.4s"></div>
      <div class="radar-center"></div>
    </div>

    <h1 style="font-size:38px;font-weight:700;color:var(--panel-fg);letter-spacing:-.03em;line-height:1">CamScan</h1>
    <p style="font-family:'JetBrains Mono',monospace;font-size:11.5px;color:var(--panel-muted);margin-top:12px;letter-spacing:.16em;text-transform:uppercase">Biometric Security Intelligence</p>

    <div style="position:absolute;bottom:2.5rem;left:3rem;right:3rem;display:flex;align-items:center;gap:8px;font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--panel-muted);opacity:.45;letter-spacing:.1em">
      <span class="status-dot"></span>
      SYS.ACTIVE&nbsp;//&nbsp;SECURE&nbsp;CHANNEL
    </div>
  </aside>

  <!-- ═══ Form Panel ═══ -->
  <section class="form-side">
    <div class="form-blob" style="width:280px;height:280px;background:var(--accent-glow);top:8%;right:2%"></div>
    <div class="form-blob" style="width:220px;height:220px;background:var(--accent-glow);bottom:12%;left:-2%"></div>

    <div class="form-card card-enter">

      <!-- Mobile-only brand -->
      <div class="mobile-brand">
        <div style="width:54px;height:54px;border-radius:14px;background:var(--accent);display:flex;align-items:center;justify-content:center;margin-bottom:1rem;box-shadow:0 4px 24px var(--accent-glow)">
          <span class="material-symbols-outlined" style="color:#fff;font-size:28px;font-variation-settings:'FILL' 1,'wght' 400">videocam</span>
        </div>
        <h1 style="font-size:28px;font-weight:700;color:var(--fg);letter-spacing:-.02em">CamScan</h1>
        <p style="font-family:'JetBrains Mono',monospace;font-size:10.5px;color:var(--fg-faint);margin-top:5px;letter-spacing:.14em;text-transform:uppercase">Biometric Security</p>
      </div>

      <!-- Heading -->
      <div style="margin-bottom:2rem">
        <h2 style="font-size:26px;font-weight:700;color:var(--fg);letter-spacing:-.025em">Welcome back</h2>
        <p style="font-size:14px;color:var(--fg-muted);margin-top:6px">Enter your credentials to access the system</p>
      </div>

      <!-- Laravel errors -->
      @if ($errors->any())
        <div class="alert">
          <span class="material-symbols-outlined" style="font-size:18px;flex-shrink:0">error</span>
          <span>{{ $errors->first() }}</span>
        </div>
      @endif

      <!-- Form -->
      <form method="POST" action="{{ route('login.post') }}" id="login-form" novalidate>
        @csrf

        <div class="field-group">
          <label class="field-label" for="name">
            <span class="material-symbols-outlined" style="font-size:15px">person</span>
            Username
          </label>
          <div class="input-wrap">
            <span class="material-symbols-outlined input-ico">badge</span>
            <input class="auth-input" type="text" id="name" name="name"
              value="{{ old('name') }}"
              placeholder="e.g. admin_security"
              autocomplete="username" required/>
          </div>
        </div>

        <div class="field-group">
          <label class="field-label" for="password">
            <span class="material-symbols-outlined" style="font-size:15px">lock</span>
            Password
          </label>
          <div class="input-wrap">
            <span class="material-symbols-outlined input-ico">key</span>
            <input class="auth-input" type="password" id="password" name="password"
              style="padding-right:50px"
              placeholder="Enter your password"
              autocomplete="current-password" required/>
            <button type="button" class="pw-toggle" onclick="toggleEye()" aria-label="Toggle password visibility">
              <span class="material-symbols-outlined" id="eye-icon">visibility</span>
            </button>
          </div>
        </div>

        <div style="margin-bottom:2rem">
          <label class="chk-label">
            <input type="checkbox" name="remember"/>
            Remember me on this device
          </label>
        </div>

        <button type="submit" class="submit-btn" id="submit-btn" onclick="handleRipple(event)">
          <span id="btn-content" style="display:flex;align-items:center;gap:8px">
            <span id="btn-text">Sign in to system</span>
            <span class="material-symbols-outlined" style="font-size:18px" id="btn-arrow">arrow_forward</span>
          </span>
          <div class="spinner" id="btn-spinner" style="display:none"></div>
        </button>
      </form>

      <div class="divider"></div>

      <div style="display:flex;align-items:center;justify-content:space-between">
        <div class="status-bar">
          <span class="status-dot"></span>
          <span>Secure</span>
        </div>
        <p style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--fg-faint)">v2.4.1</p>
      </div>
    </div>
  </section>
</div>

<!-- Theme Toggle -->
<button class="theme-btn" onclick="toggleTheme()" aria-label="Toggle theme">
  <span class="material-symbols-outlined" id="theme-icon" style="font-size:20px">dark_mode</span>
</button>

<script>
/* ── Theme ── */
function toggleTheme(){
  const h=document.documentElement,i=document.getElementById('theme-icon');
  h.classList.toggle('dark');
  i.innerText=h.classList.contains('dark')?'light_mode':'dark_mode';
}
/* Respect system preference on load */
if(window.matchMedia&&window.matchMedia('(prefers-color-scheme:dark)').matches){
  document.documentElement.classList.add('dark');
  document.getElementById('theme-icon').innerText='light_mode';
}

/* ── Password toggle ── */
function toggleEye(){
  const p=document.getElementById('password'),i=document.getElementById('eye-icon');
  p.type=p.type==='password'?'text':'password';
  i.innerText=p.type==='password'?'visibility':'visibility_off';
}

/* ── Ripple ── */
function handleRipple(e){
  const b=e.currentTarget,r=b.getBoundingClientRect(),s=document.createElement('span');
  const d=Math.max(b.clientWidth,b.clientHeight);
  s.className='ripple';
  s.style.cssText='width:'+d+'px;height:'+d+'px;left:'+(e.clientX-r.left-d/2)+'px;top:'+(e.clientY-r.top-d/2)+'px';
  b.appendChild(s);
  setTimeout(()=>s.remove(),650);
}

/* ── Sound ── */
function tone(f,dur,type,vol){
  try{const c=new(window.AudioContext||window.webkitAudioContext)(),o=c.createOscillator(),g=c.createGain();o.connect(g);g.connect(c.destination);o.type=type||'sine';o.frequency.value=f;g.gain.setValueAtTime(vol||.1,c.currentTime);g.gain.exponentialRampToValueAtTime(.001,c.currentTime+dur);o.start();o.stop(c.currentTime+dur)}catch(e){}
}
function sndClick(){tone(700,.035,'sine',.05)}
function sndError(){tone(260,.09,'sawtooth',.09);setTimeout(()=>tone(200,.16,'sawtooth',.07),100)}
function sndSuccess(){tone(523,.1,'sine',.09);setTimeout(()=>tone(659,.1,'sine',.08),110);setTimeout(()=>tone(784,.2,'sine',.08),220)}

/* ── Focus sounds ── */
document.getElementById('name').addEventListener('focus',()=>tone(580,.025,'sine',.03));
document.getElementById('password').addEventListener('focus',()=>tone(580,.025,'sine',.03));

/* ── Form submit ── */
document.getElementById('login-form').addEventListener('submit',function(){
  sndClick();
  const btn=document.getElementById('submit-btn'),
        c=document.getElementById('btn-content'),
        sp=document.getElementById('btn-spinner');
  btn.disabled=true;
  c.style.display='none';
  sp.style.display='block';
});

/* ── Error sound ── */
@if ($errors->any())
  document.addEventListener('DOMContentLoaded',()=>sndError());
@endif

/* ── Brand panel particles ── */
(function(){
  const panel=document.getElementById('brand-side');
  if(!panel)return;
  for(let i=0;i<24;i++){
    const p=document.createElement('div');
    p.className='ptcl';
    p.style.left=Math.random()*100+'%';
    p.style.bottom=Math.random()*40+'%';
    p.style.animationDuration=(3.5+Math.random()*4.5)+'s';
    p.style.animationDelay=(Math.random()*6)+'s';
    p.style.width=p.style.height=(1+Math.random()*2)+'px';
    panel.appendChild(p);
  }
})();

/* ── Hover arrow nudge ── */
const arrow=document.getElementById('btn-arrow');
const btn=document.getElementById('submit-btn');
if(arrow&&btn){
  btn.addEventListener('mouseenter',()=>{arrow.style.transform='translateX(3px)'});
  btn.addEventListener('mouseleave',()=>{arrow.style.transform='translateX(0)'});
  arrow.style.transition='transform .2s';
}
</script>
</body>
</html>