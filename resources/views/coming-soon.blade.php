{{-- resources/views/coming-soon.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>CamScan | {{ $page }}</title>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
:root{
  --bg:#eef1f0;--card:#ffffff;--fg:#0f1a15;--fg-muted:#536359;--fg-faint:#93a59b;
  --border:#d0d9d4;--accent:#059669;--accent-glow:rgba(5,150,105,0.15);
}
.dark{
  --bg:#070c0a;--card:#101a15;--fg:#e0ebe4;--fg-muted:#7a8f83;--fg-faint:#3e5349;
  --border:#1b2d24;--accent:#34d399;--accent-glow:rgba(52,211,153,0.12);
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Space Grotesk',sans-serif;background:var(--bg);color:var(--fg);min-height:100vh;display:flex;align-items:center;justify-content:center;transition:background .35s,color .35s;padding:2rem 1rem;position:relative;overflow:hidden}
.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;vertical-align:middle;line-height:1}
.dot-grid{position:absolute;inset:0;background-image:radial-gradient(circle at 1.5px 1.5px,var(--border) 1px,transparent 0);background-size:24px 24px;z-index:0;pointer-events:none}
.blob{position:absolute;border-radius:50%;pointer-events:none;z-index:0}
.card{position:relative;z-index:2;background:var(--card);border:1px solid var(--border);border-radius:20px;padding:3rem 3rem 2.5rem;max-width:520px;width:100%;display:flex;flex-direction:column;align-items:center;text-align:center;animation:card-in .6s cubic-bezier(.22,1,.36,1) both}
@keyframes card-in{from{opacity:0;transform:translateY(24px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}
.radar-wrap{position:relative;width:140px;height:140px;margin-bottom:2rem;flex-shrink:0}
.r-ring{position:absolute;border:1px solid var(--accent);border-radius:50%;opacity:.12}
.r-ring:nth-child(1){inset:0}
.r-ring:nth-child(2){inset:22px}
.r-ring:nth-child(3){inset:44px}
.r-ring:nth-child(4){inset:66px}
.r-h,.r-v{position:absolute;background:var(--accent);opacity:.1}
.r-h{width:100%;height:1px;top:50%}
.r-v{width:1px;height:100%;left:50%}
.r-sweep{position:absolute;inset:0;border-radius:50%;background:conic-gradient(from 0deg,transparent 0%,var(--accent) 7%,transparent 16%);animation:sweep 3s linear infinite;opacity:.5}
@keyframes sweep{to{transform:rotate(360deg)}}
.r-center{position:absolute;top:50%;left:50%;width:8px;height:8px;border-radius:50%;background:var(--accent);transform:translate(-50%,-50%);box-shadow:0 0 10px var(--accent)}
.r-blip{position:absolute;width:5px;height:5px;border-radius:50%;background:var(--accent)}
.r-blip::after{content:'';position:absolute;inset:-4px;border-radius:50%;border:1px solid var(--accent);animation:blip 2s ease-out infinite}
@keyframes blip{0%{transform:scale(1);opacity:.5}100%{transform:scale(2.8);opacity:0}}
.eyebrow{font-family:'JetBrains Mono',monospace;font-size:10.5px;letter-spacing:.16em;text-transform:uppercase;color:var(--accent);margin-bottom:.75rem;display:flex;align-items:center;gap:6px}
.sdot{width:5px;height:5px;border-radius:50%;background:var(--accent);animation:sdot 2s ease infinite;display:inline-block}
@keyframes sdot{0%,100%{opacity:1}50%{opacity:.25}}
h1{font-size:28px;font-weight:700;color:var(--fg);letter-spacing:-.025em;line-height:1.2;margin-bottom:.75rem}
.sub{font-size:14.5px;color:var(--fg-muted);line-height:1.6;margin-bottom:2rem;max-width:360px}
.progress-wrap{width:100%;background:var(--border);border-radius:999px;height:4px;overflow:hidden}
.progress-fill{height:100%;background:var(--accent);border-radius:999px;width:0%;animation:fill-progress 2.5s cubic-bezier(.22,1,.36,1) .4s forwards}
@keyframes fill-progress{to{width:68%}}
.progress-label{display:flex;justify-content:space-between;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--fg-faint);margin-top:6px;width:100%;margin-bottom:2rem}
.back-btn{display:inline-flex;align-items:center;gap:8px;padding:11px 22px;border-radius:10px;background:var(--accent);color:#fff;font-size:14px;font-weight:600;font-family:'Space Grotesk',sans-serif;text-decoration:none;transition:background .2s,transform .15s;letter-spacing:.01em}
.dark .back-btn{color:#022c22}
.back-btn:hover{background:#047857;transform:translateY(-1px)}
.dark .back-btn:hover{background:#6ee7b7}
.back-btn:active{transform:scale(.98)}
.divider{width:100%;height:1px;background:var(--border);margin:2rem 0}
.meta{font-family:'JetBrains Mono',monospace;font-size:10.5px;color:var(--fg-faint);letter-spacing:.08em;display:flex;align-items:center;gap:16px;flex-wrap:wrap;justify-content:center}
.theme-btn{position:fixed;top:1rem;right:1rem;z-index:10;width:36px;height:36px;border-radius:9px;background:var(--card);border:1px solid var(--border);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);transition:all .2s}
.theme-btn:hover{border-color:var(--accent);color:var(--accent)}
@media(max-width:480px){.card{padding:2rem 1.5rem}h1{font-size:22px}}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:.01ms!important;animation-iteration-count:1!important}}
</style>
</head>
<body>

<div class="dot-grid"></div>
<div class="blob" style="width:340px;height:340px;background:var(--accent-glow);filter:blur(90px);top:-15%;right:-10%"></div>
<div class="blob" style="width:280px;height:280px;background:var(--accent-glow);filter:blur(80px);bottom:-10%;left:-8%"></div>

<button class="theme-btn" onclick="toggleTheme()" aria-label="Toggle theme">
  <span class="material-symbols-outlined" style="font-size:18px" id="theme-icon">dark_mode</span>
</button>

<div class="card">

  <div class="radar-wrap" aria-hidden="true">
    <div class="r-ring"></div>
    <div class="r-ring"></div>
    <div class="r-ring"></div>
    <div class="r-ring"></div>
    <div class="r-h"></div>
    <div class="r-v"></div>
    <div class="r-sweep"></div>
    <div class="r-blip" style="top:28%;left:65%"></div>
    <div class="r-blip" style="top:64%;left:30%;animation-delay:.9s"></div>
    <div class="r-center"></div>
  </div>

  <div class="eyebrow">
    <span class="sdot"></span>
    Module in development
  </div>

  <h1>{{ $page }}</h1>

  <p class="sub">
    This section is being built. It'll be ready soon — head back to the dashboard for now.
  </p>

  <div style="width:100%">
    <div class="progress-wrap">
      <div class="progress-fill"></div>
    </div>
    <div class="progress-label">
      <span>Build progress</span>
      <span>68%</span>
    </div>
  </div>

  <a href="{{ route('dashboard') }}" class="back-btn">
    <span class="material-symbols-outlined" style="font-size:17px">arrow_back</span>
    Back to dashboard
  </a>

  <div class="divider"></div>

  <div class="meta">
    <span><span class="sdot" style="margin-right:5px"></span>Sys.active</span>
    <span>CamScan v2.4.1</span>
    <span>Secure channel</span>
  </div>

</div>

<script>
function toggleTheme(){
  const h=document.documentElement,i=document.getElementById('theme-icon');
  h.classList.toggle('dark');
  i.innerText=h.classList.contains('dark')?'light_mode':'dark_mode';
  localStorage.setItem('theme',h.classList.contains('dark')?'dark':'light');
}
(function(){
  const saved=localStorage.getItem('theme');
  const prefersDark=window.matchMedia&&window.matchMedia('(prefers-color-scheme:dark)').matches;
  if(saved==='dark'||(!saved&&prefersDark)){
    document.documentElement.classList.add('dark');
    document.getElementById('theme-icon').innerText='light_mode';
  }
})();
</script>
</body>
</html>