<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<title>CamScan | @yield('title', 'Dashboard')</title>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
:root{
  --bg:#eef1f0;--sidebar-bg:#041f17;--sidebar-border:#0e2d1f;
  --fg:#0f1a15;--fg-muted:#536359;--fg-faint:#93a59b;
  --border:#d0d9d4;--card:#ffffff;--input-bg:#f4f7f6;
  --accent:#059669;--accent-hover:#047857;
  --accent-soft:#ecfdf5;--accent-glow:rgba(5,150,105,0.18);
  --panel-fg:#d1fae5;--panel-muted:#6ee7b7;--panel-faint:#1a3d2b;
  --topbar:#ffffff;--topbar-border:#e2e8e4;
  --err-bg:#fef2f2;--err-fg:#991b1b;--err-bdr:#fecaca;
}
.dark{
  --bg:#070c0a;--sidebar-bg:#060f0b;--sidebar-border:#0d1f16;
  --fg:#e0ebe4;--fg-muted:#7a8f83;--fg-faint:#3e5349;
  --border:#1b2d24;--card:#101a15;--input-bg:#0c1611;
  --accent:#34d399;--accent-hover:#6ee7b7;
  --accent-soft:rgba(52,211,153,0.08);--accent-glow:rgba(52,211,153,0.14);
  --panel-fg:#a7f3d0;--panel-muted:#34d399;--panel-faint:#071a10;
  --topbar:#0c1510;--topbar-border:#1a2e21;
  --err-bg:rgba(239,68,68,0.1);--err-fg:#fca5a5;--err-bdr:rgba(239,68,68,0.25);
}
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{
  font-family:'Space Grotesk',sans-serif;
  background:var(--bg);color:var(--fg);
  min-height:100vh;display:flex;flex-direction:column;
  transition:background .35s,color .35s;
}
.material-symbols-outlined{
  font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;
  vertical-align:middle;line-height:1;
}

/* ── App shell ── */
.app-shell{display:flex;height:100vh;overflow:hidden}

/* ── Sidebar ── */
.sidebar{
  width:260px;flex-shrink:0;
  background:var(--sidebar-bg);
  border-right:1px solid var(--sidebar-border);
  display:flex;flex-direction:column;
  transition:width .3s cubic-bezier(.22,1,.36,1),transform .3s cubic-bezier(.22,1,.36,1);
  position:relative;z-index:20;overflow:hidden;
}
.sidebar.collapsed{width:68px}

/* Brand */
.sidebar-brand{
  display:flex;align-items:center;gap:12px;
  padding:20px 18px 18px;
  border-bottom:1px solid var(--panel-faint);flex-shrink:0;
}
.brand-icon{
  width:36px;height:36px;border-radius:10px;
  background:var(--accent);flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 2px 12px var(--accent-glow);
}
.brand-name{
  font-size:17px;font-weight:700;color:var(--panel-fg);
  letter-spacing:-.025em;white-space:nowrap;
  opacity:1;transition:opacity .2s;
}
.sidebar.collapsed .brand-name{opacity:0;pointer-events:none}

/* Nav */
.sidebar-nav{flex:1;overflow-y:auto;padding:12px 10px;scrollbar-width:none}
.sidebar-nav::-webkit-scrollbar{display:none}
.nav-section-label{
  font-family:'JetBrains Mono',monospace;
  font-size:9.5px;letter-spacing:.14em;text-transform:uppercase;
  color:var(--panel-faint);padding:0 8px;margin:18px 0 6px;
  white-space:nowrap;opacity:1;transition:opacity .2s;
}
.sidebar.collapsed .nav-section-label{opacity:0}
.nav-item{
  display:flex;align-items:center;gap:11px;
  padding:10px 10px;border-radius:10px;
  cursor:pointer;transition:background .2s,color .2s;
  text-decoration:none;color:var(--fg-faint);
  font-size:14px;font-weight:500;white-space:nowrap;
  position:relative;margin-bottom:2px;
}
.nav-item:hover{background:var(--panel-faint);color:var(--panel-muted)}
.nav-item.active{background:rgba(5,150,105,0.18);color:var(--accent)}
.dark .nav-item.active{background:rgba(52,211,153,0.12)}
.nav-item .nav-icon{
  font-size:20px;flex-shrink:0;
  font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 24;
}
.nav-item.active .nav-icon{
  font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;
}
.nav-label{opacity:1;transition:opacity .2s}
.sidebar.collapsed .nav-label{opacity:0;pointer-events:none}
.nav-badge{
  margin-left:auto;font-size:10px;font-weight:600;
  background:var(--accent);color:#fff;
  padding:1px 6px;border-radius:20px;
  opacity:1;transition:opacity .2s;
}
.dark .nav-badge{color:#022c22}
.sidebar.collapsed .nav-badge{opacity:0}
.nav-item.active::before{
  content:'';position:absolute;left:0;top:25%;bottom:25%;
  width:3px;border-radius:0 3px 3px 0;background:var(--accent);
}

/* Tooltip for collapsed sidebar */
.sidebar.collapsed .nav-item{position:relative}
.sidebar.collapsed .nav-item:hover::after{
  content:attr(data-label);
  position:absolute;left:calc(100% + 10px);top:50%;transform:translateY(-50%);
  background:var(--card);color:var(--fg);
  font-size:12.5px;font-weight:500;white-space:nowrap;
  padding:5px 10px;border-radius:8px;
  border:1px solid var(--border);
  pointer-events:none;z-index:99;
  box-shadow:0 4px 16px rgba(0,0,0,.1);
}

/* Sidebar footer */
.sidebar-footer{
  padding:12px 10px;
  border-top:1px solid var(--panel-faint);flex-shrink:0;
}
.user-row{
  display:flex;align-items:center;gap:10px;
  padding:8px 10px;border-radius:10px;cursor:pointer;transition:background .2s;
}
.user-row:hover{background:var(--panel-faint)}
.avatar{
  width:32px;height:32px;border-radius:50%;
  background:var(--accent);flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-size:12px;font-weight:700;color:#fff;letter-spacing:.02em;
}
.dark .avatar{color:#022c22}
.user-info{min-width:0;flex:1;opacity:1;transition:opacity .2s}
.sidebar.collapsed .user-info{opacity:0;pointer-events:none}
.user-name{font-size:13px;font-weight:600;color:var(--panel-fg);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.user-role{font-size:11px;color:var(--panel-muted);font-family:'JetBrains Mono',monospace;letter-spacing:.04em}

/* ── Main area ── */
.main-area{flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0}

/* Topbar */
.topbar{
  height:58px;flex-shrink:0;
  background:var(--topbar);border-bottom:1px solid var(--topbar-border);
  display:flex;align-items:center;padding:0 24px;gap:16px;
}
.topbar-toggle{
  width:34px;height:34px;border-radius:8px;
  background:none;border:1px solid var(--border);
  cursor:pointer;display:flex;align-items:center;justify-content:center;
  color:var(--fg-muted);transition:all .2s;flex-shrink:0;
}
.topbar-toggle:hover{border-color:var(--accent);color:var(--accent);background:var(--accent-soft)}
.topbar-breadcrumb{
  display:flex;align-items:center;gap:8px;
  font-size:13.5px;font-weight:500;flex:1;min-width:0;
}
.breadcrumb-root{color:var(--fg-faint)}
.breadcrumb-sep{color:var(--fg-faint);font-size:14px}
.breadcrumb-cur{color:var(--fg)}
.topbar-right{display:flex;align-items:center;gap:10px;margin-left:auto}
.icon-btn{
  width:34px;height:34px;border-radius:8px;
  background:none;border:1px solid var(--border);
  cursor:pointer;display:flex;align-items:center;justify-content:center;
  color:var(--fg-muted);transition:all .2s;position:relative;
}
.icon-btn:hover{border-color:var(--accent);color:var(--accent);background:var(--accent-soft)}
.notif-dot{
  position:absolute;top:6px;right:6px;
  width:6px;height:6px;border-radius:50%;background:var(--accent);
}
.theme-toggle{
  width:34px;height:34px;border-radius:8px;
  background:none;border:1px solid var(--border);
  cursor:pointer;display:flex;align-items:center;justify-content:center;
  color:var(--fg-muted);transition:all .2s;
}
.theme-toggle:hover{border-color:var(--accent);color:var(--accent);background:var(--accent-soft)}

/* Content */
.content-area{
  flex:1;overflow-y:auto;padding:28px;
  scrollbar-width:thin;scrollbar-color:var(--border) transparent;
}

/* Status footer */
.status-bar-bottom{
  font-family:'JetBrains Mono',monospace;font-size:10px;
  letter-spacing:.1em;text-transform:uppercase;
  color:var(--fg-faint);display:flex;align-items:center;gap:8px;
  margin-top:24px;padding-top:16px;border-top:1px solid var(--border);
}
.sdot{width:5px;height:5px;border-radius:50%;background:var(--accent);animation:sdot 2s ease infinite}
@keyframes sdot{0%,100%{opacity:1}50%{opacity:.3}}

/* Toast */
.toast-wrap{position:fixed;top:1rem;right:1rem;z-index:999;display:flex;flex-direction:column;gap:8px}
.toast{
  padding:12px 16px;border-radius:10px;
  font-size:13.5px;display:flex;align-items:center;gap:10px;
  border:1px solid var(--border);background:var(--card);color:var(--fg);
  animation:toast-in .35s cubic-bezier(.22,1,.36,1);
  box-shadow:0 4px 20px rgba(0,0,0,.1);
  max-width:340px;
}
@keyframes toast-in{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}

/* Mobile */
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:15}
@media(max-width:768px){
  .sidebar{position:fixed;top:0;bottom:0;left:0;transform:translateX(-100%)}
  .sidebar.mobile-open{transform:translateX(0)}
  .sidebar.collapsed{width:260px}
  .sidebar.collapsed .brand-name,.sidebar.collapsed .nav-label,
  .sidebar.collapsed .nav-badge,.sidebar.collapsed .user-info,
  .sidebar.collapsed .nav-section-label{opacity:1;pointer-events:auto}
  .sidebar-overlay.active{display:block}
  .content-area{padding:20px 16px}
}
@media(prefers-reduced-motion:reduce){
  *,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important}
}
</style>
@stack('styles')
</head>
<body>

<div class="app-shell">

  {{-- ═══ Sidebar ═══ --}}
  <aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
      <div class="brand-icon">
        <span class="material-symbols-outlined" style="color:#fff;font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">videocam</span>
      </div>
      <span class="brand-name">CamScan</span>
    </div>

    {{-- Navigation — only tabs user can access --}}
    <nav class="sidebar-nav" aria-label="Main navigation">

      <div class="nav-section-label">Main</div>

      <a href="{{ route('dashboard') }}"
         data-label="Dashboard"
         class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <span class="material-symbols-outlined nav-icon">dashboard</span>
        <span class="nav-label">Dashboard</span>
      </a>

      @foreach(auth()->user()->accessibleTabs() as $tab)
        @if($tab->slug !== 'dashboard')
          <a href="{{ route($tab->route) }}"
             data-label="{{ $tab->name }}"
             class="nav-item {{ request()->routeIs($tab->route) ? 'active' : '' }}">
            <span class="material-symbols-outlined nav-icon">{{ $tab->icon ?? 'circle' }}</span>
            <span class="nav-label">{{ $tab->name }}</span>
          </a>
        @endif
      @endforeach

    </nav>

    {{-- User footer --}}
    <div class="sidebar-footer">
      <form method="POST" action="{{ route('logout') }}" id="logout-form">@csrf</form>
      <div class="user-row" onclick="document.getElementById('logout-form').submit()" title="Sign out">
        <div class="avatar">
          {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div class="user-info">
          <div class="user-name">{{ auth()->user()->name }}</div>
          <div class="user-role">{{ auth()->user()->role->name ?? 'No role' }}</div>
        </div>
        <span class="material-symbols-outlined" style="font-size:16px;color:var(--panel-muted);flex-shrink:0">logout</span>
      </div>
    </div>

  </aside>

  <div class="sidebar-overlay" id="overlay" onclick="closeMobile()"></div>

  {{-- ═══ Main area ═══ --}}
  <div class="main-area">

    {{-- Topbar --}}
    <header class="topbar">
      <button class="topbar-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
        <span class="material-symbols-outlined" style="font-size:20px">menu</span>
      </button>

      <div class="topbar-breadcrumb">
        <span class="breadcrumb-root">CamScan</span>
        <span class="material-symbols-outlined breadcrumb-sep">chevron_right</span>
        <span class="breadcrumb-cur">@yield('page-title', 'Dashboard')</span>
      </div>

      <div class="topbar-right">
        <button class="icon-btn" aria-label="Notifications">
          <span class="material-symbols-outlined" style="font-size:18px">notifications</span>
          <span class="notif-dot"></span>
        </button>
        <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
          <span class="material-symbols-outlined" style="font-size:18px" id="theme-icon">dark_mode</span>
        </button>
      </div>
    </header>

    {{-- Page content --}}
    <main class="content-area">
      @yield('content')

      <div class="status-bar-bottom">
        <span class="sdot"></span>
        <span>Sys.active // secure channel // v2.4.1</span>
      </div>
    </main>

  </div>
</div>

{{-- Toast for session messages --}}
@if(session('success'))
  <div class="toast-wrap">
    <div class="toast" style="border-color:var(--accent)">
      <span class="material-symbols-outlined" style="font-size:18px;color:var(--accent)">check_circle</span>
      <span>{{ session('success') }}</span>
    </div>
  </div>
@endif

<script>
let collapsed = false;

function toggleSidebar() {
  const s = document.getElementById('sidebar');
  const ov = document.getElementById('overlay');
  if (window.innerWidth <= 768) {
    s.classList.toggle('mobile-open');
    ov.classList.toggle('active');
  } else {
    collapsed = !collapsed;
    s.classList.toggle('collapsed', collapsed);
  }
}

function closeMobile() {
  document.getElementById('sidebar').classList.remove('mobile-open');
  document.getElementById('overlay').classList.remove('active');
}

function toggleTheme() {
  const h = document.documentElement;
  const i = document.getElementById('theme-icon');
  h.classList.toggle('dark');
  i.innerText = h.classList.contains('dark') ? 'light_mode' : 'dark_mode';
  localStorage.setItem('theme', h.classList.contains('dark') ? 'dark' : 'light');
}

// Persist theme across pages
(function() {
  const saved = localStorage.getItem('theme');
  const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  if (saved === 'dark' || (!saved && prefersDark)) {
    document.documentElement.classList.add('dark');
    const i = document.getElementById('theme-icon');
    if (i) i.innerText = 'light_mode';
  }
})();

// Auto-dismiss toast
document.addEventListener('DOMContentLoaded', function() {
  const toasts = document.querySelectorAll('.toast');
  toasts.forEach(t => setTimeout(() => t.style.opacity = '0', 3000));
});
</script>

@stack('scripts')
</body>
</html>