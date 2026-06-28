@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div style="margin-bottom:24px">
  <h1 style="font-size:22px;font-weight:700;color:var(--fg);letter-spacing:-.02em">Dashboard</h1>
  <p style="font-size:13.5px;color:var(--fg-muted);margin-top:5px">
    Welcome back, {{ auth()->user()->name }} — here's what's happening today.
  </p>
</div>

{{-- Stat cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin-bottom:28px">
  <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px 18px">
    <div style="font-size:11.5px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-faint);margin-bottom:8px">Your role</div>
    <div style="font-size:18px;font-weight:700;color:var(--fg)">{{ auth()->user()->role->name ?? '—' }}</div>
  </div>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px 18px">
    <div style="font-size:11.5px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-faint);margin-bottom:8px">Accessible tabs</div>
    <div style="font-size:26px;font-weight:700;color:var(--accent)">{{ auth()->user()->accessibleTabs()->count() }}</div>
  </div>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px 18px">
    <div style="font-size:11.5px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-faint);margin-bottom:8px">Gender</div>
    <div style="font-size:18px;font-weight:700;color:var(--fg);text-transform:capitalize">{{ auth()->user()->gender }}</div>
  </div>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px 18px">
    <div style="font-size:11.5px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-faint);margin-bottom:8px">Status</div>
    <div style="font-size:14px;font-weight:700;color:var(--accent);display:flex;align-items:center;gap:6px">
      <span style="width:7px;height:7px;border-radius:50%;background:var(--accent);display:inline-block"></span>
      Active
    </div>
  </div>
</div>

{{-- Accessible tabs list --}}
<div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:20px 22px">
  <div style="font-size:14px;font-weight:600;color:var(--fg);margin-bottom:14px">Your accessible sections</div>
  @foreach(auth()->user()->accessibleTabs() as $tab)
    <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)">
      <span class="material-symbols-outlined" style="font-size:18px;color:var(--accent)">{{ $tab->icon ?? 'circle' }}</span>
      <span style="font-size:14px;font-weight:500;color:var(--fg);flex:1">{{ $tab->name }}</span>
      <a href="{{ route($tab->route) }}" style="font-size:12px;color:var(--accent);text-decoration:none;font-family:'JetBrains Mono',monospace;letter-spacing:.04em">
        Open →
      </a>
    </div>
  @endforeach
</div>
@endsection