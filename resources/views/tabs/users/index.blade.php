@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Users')

@push('styles')
<style>
.tcard{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden}
.tcard-head{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.search-wrap{position:relative;display:flex;align-items:center}
.search-wrap .material-symbols-outlined{position:absolute;left:10px;font-size:16px;color:var(--fg-faint);pointer-events:none}
.search-input{padding:8px 12px 8px 34px;border-radius:8px;background:var(--input-bg);border:1px solid var(--input-border);color:var(--fg);font-size:13.5px;font-family:'Space Grotesk',sans-serif;outline:none;width:210px;transition:border .2s}
.search-input:focus{border-color:var(--accent)}
.search-input::placeholder{color:var(--fg-faint)}
table{width:100%;border-collapse:collapse}
thead th{padding:11px 16px;font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-faint);text-align:left;border-bottom:1px solid var(--border);background:var(--input-bg);font-family:'JetBrains Mono',monospace;white-space:nowrap}
tbody tr{border-bottom:1px solid var(--border);transition:background .15s}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:var(--input-bg)}
td{padding:12px 16px;font-size:13.5px;vertical-align:middle}
.avatar{width:34px;height:34px;border-radius:50%;background:var(--accent-soft);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--accent-fg);flex-shrink:0;overflow:hidden}
.avatar img{width:100%;height:100%;object-fit:cover;border-radius:50%}
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:11.5px;font-weight:600;white-space:nowrap}
.badge-active{background:var(--accent-soft);color:var(--accent-fg)}
.dark .badge-active{color:var(--accent)}
.badge-inactive{background:var(--err-bg);color:var(--err)}
.badge-verified{background:var(--accent-soft);color:var(--accent-fg)}
.dark .badge-verified{color:var(--accent)}
.badge-unverified{background:var(--warn-bg);color:var(--warn)}
.actions{display:flex;align-items:center;gap:6px}
.icon-btn{width:30px;height:30px;border-radius:7px;background:none;border:1px solid var(--border);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);transition:all .2s}
.icon-btn:hover{border-color:var(--accent);color:var(--accent);background:var(--accent-soft)}
.icon-btn.danger:hover{border-color:var(--err-border);color:var(--err);background:var(--err-bg)}
.verify-btn{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:7px;font-size:12px;font-weight:600;font-family:'Space Grotesk',sans-serif;cursor:pointer;transition:all .2s;border:none;white-space:nowrap}
.verify-btn-do{background:var(--warn-bg);color:var(--warn);border:1px solid var(--warn-border)}
.verify-btn-do:hover{filter:brightness(.94)}
.verify-btn-done{background:var(--accent-soft);color:var(--accent-fg);border:1px solid var(--border)}
.dark .verify-btn-done{color:var(--accent)}
.overlay{position:fixed;inset:0;background:rgba(0,0,0,.48);z-index:50;display:flex;align-items:center;justify-content:center;padding:1rem;opacity:0;pointer-events:none;transition:opacity .25s}
.overlay.open{opacity:1;pointer-events:auto}
.modal{background:var(--card);border:1px solid var(--border);border-radius:16px;width:100%;max-width:480px;max-height:92vh;overflow-y:auto;animation:modal-in .3s cubic-bezier(.22,1,.36,1) both}
@keyframes modal-in{from{opacity:0;transform:translateY(16px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}
.modal-head{padding:18px 22px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:var(--card);z-index:2}
.modal-head h2{font-size:17px;font-weight:700;color:var(--fg);letter-spacing:-.02em}
.modal-body{padding:20px 22px}
.modal-foot{padding:14px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;position:sticky;bottom:0;background:var(--card)}
.field{margin-bottom:18px}
.field label{display:block;font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-muted);margin-bottom:7px;font-family:'JetBrains Mono',monospace}
.field input[type=text],.field input[type=password],.field select{width:100%;padding:10px 14px;border-radius:9px;background:var(--input-bg);border:1.5px solid var(--input-border);color:var(--fg);font-size:14px;font-family:'Space Grotesk',sans-serif;outline:none;transition:border .2s}
.field input:focus,.field select:focus{border-color:var(--accent)}
.field input::placeholder{color:var(--fg-faint)}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.photo-upload{display:flex;align-items:center;gap:14px}
.photo-preview{width:56px;height:56px;border-radius:50%;background:var(--input-bg);border:2px dashed var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;cursor:pointer;transition:border .2s;flex-shrink:0}
.photo-preview:hover{border-color:var(--accent)}
.photo-preview img{width:100%;height:100%;object-fit:cover;border-radius:50%}
.toggle-wrap{display:flex;align-items:center;gap:10px}
.toggle{position:relative;width:40px;height:22px;flex-shrink:0}
.toggle input{opacity:0;width:0;height:0;position:absolute}
.toggle-track{position:absolute;inset:0;background:var(--border-strong);border-radius:999px;cursor:pointer;transition:background .2s}
.toggle input:checked+.toggle-track{background:var(--accent)}
.toggle-track::after{content:'';position:absolute;width:16px;height:16px;border-radius:50%;background:#fff;top:3px;left:3px;transition:transform .2s}
.toggle input:checked+.toggle-track::after{transform:translateX(18px)}
.toggle-label{font-size:13.5px;color:var(--fg-muted)}
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:9px;font-size:13.5px;font-weight:600;font-family:'Space Grotesk',sans-serif;cursor:pointer;transition:all .2s;border:none;text-decoration:none}
.btn-primary{background:var(--accent);color:#fff}
.dark .btn-primary{color:#022c22}
.btn-primary:hover{background:#047857}
.dark .btn-primary:hover{background:#6ee7b7}
.btn-primary:disabled{opacity:.6;cursor:not-allowed}
.btn-ghost{background:none;border:1px solid var(--border);color:var(--fg-muted)}
.btn-ghost:hover{border-color:var(--accent);color:var(--accent);background:var(--accent-soft)}
.btn-danger{background:none;border:1px solid var(--err-border);color:var(--err)}
.btn-danger:hover{background:var(--err-bg)}
.btn-sm{padding:6px 12px;font-size:12.5px}
.empty-state{padding:52px 24px;text-align:center;color:var(--fg-faint)}
.empty-state .material-symbols-outlined{font-size:42px;display:block;margin-bottom:12px;color:var(--fg-faint)}
.empty-state p{font-size:14px}
.cam-frame{position:relative;width:100%;border-radius:10px;overflow:hidden;background:#000;aspect-ratio:4/3;margin-bottom:14px}
.cam-guide{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none}
.cam-guide-oval{width:180px;height:220px;border:2px solid rgba(5,150,105,.55);border-radius:50%}
.toast-stack{position:fixed;top:1rem;right:1rem;z-index:999;display:flex;flex-direction:column;gap:8px;pointer-events:none}
.toast{padding:11px 16px;border-radius:10px;font-size:13.5px;display:flex;align-items:center;gap:9px;background:var(--card);border:1px solid var(--border);color:var(--fg);pointer-events:auto;animation:toast-in .3s cubic-bezier(.22,1,.36,1);min-width:240px;font-weight:500;transition:opacity .3s,transform .3s}
@keyframes toast-in{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}
.toast.success{border-color:var(--accent)}
.toast.error{border-color:var(--err-border);color:var(--err)}
@keyframes spin{to{transform:rotate(360deg)}}
.spin{animation:spin .65s linear infinite;display:inline-block}
@media(max-width:640px){
  .field-row{grid-template-columns:1fr}
  td,th{padding:10px 10px}
  .search-input{width:150px}
  .modal{max-width:100%;border-radius:16px 16px 0 0;position:fixed;bottom:0;left:0;right:0;max-height:95vh}
  .overlay{align-items:flex-end;padding:0}
}
</style>
@endpush

@section('content')

<div class="toast-stack" id="toasts"></div>

{{-- Page header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
  <div>
    <h1 style="font-size:22px;font-weight:700;color:var(--fg);letter-spacing:-.02em">Users</h1>
    <p style="font-size:13.5px;color:var(--fg-muted);margin-top:4px">
      Manage accounts, roles, and face verification.
    </p>
  </div>
  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
    <button class="btn btn-ghost" onclick="openSearchCapture()">
      <span class="material-symbols-outlined" style="font-size:17px">photo_camera</span>
      Search & Identify Face
    </button>
    <button class="btn btn-primary" onclick="openCreate()">
      <span class="material-symbols-outlined" style="font-size:17px">person_add</span>
      Add user
    </button>
  </div>
</div>

{{-- Stats row --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;margin-bottom:24px">
  <div style="background:var(--card);border:1px solid var(--border);border-radius:10px;padding:14px 16px">
    <div style="font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--fg-faint);margin-bottom:6px;font-family:'JetBrains Mono',monospace">Total</div>
    <div style="font-size:24px;font-weight:700;color:var(--fg)">{{ $users->count() }}</div>
  </div>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:10px;padding:14px 16px">
    <div style="font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--fg-faint);margin-bottom:6px;font-family:'JetBrains Mono',monospace">Active</div>
    <div style="font-size:24px;font-weight:700;color:var(--accent)">{{ $users->where('active', true)->count() }}</div>
  </div>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:10px;padding:14px 16px">
    <div style="font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--fg-faint);margin-bottom:6px;font-family:'JetBrains Mono',monospace">Verified</div>
    <div style="font-size:24px;font-weight:700;color:var(--accent)">{{ $users->where('face_verified', true)->count() }}</div>
  </div>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:10px;padding:14px 16px">
    <div style="font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--fg-faint);margin-bottom:6px;font-family:'JetBrains Mono',monospace">Unverified</div>
    <div style="font-size:24px;font-weight:700;color:var(--warn)">{{ $users->where('face_verified', false)->count() }}</div>
  </div>
</div>

{{-- Users table --}}
<div class="tcard">
  <div class="tcard-head">
    <div class="search-wrap">
      <span class="material-symbols-outlined">search</span>
      <input class="search-input" type="text" placeholder="Search name, role…" oninput="filterTable(this.value)" aria-label="Search users"/>
    </div>
    <span style="font-size:11.5px;color:var(--fg-faint);font-family:'JetBrains Mono',monospace" id="user-count">
      {{ $users->count() }} {{ Str::plural('user', $users->count()) }}
    </span>
  </div>

  <div style="overflow-x:auto">
    <table id="user-table">
      <thead>
        <tr>
          <th style="width:44px">#</th>
          <th>User</th>
          <th>Age</th>
          <th>Gender</th>
          <th>Status</th>
          <th>Role</th>
          <th>Face</th>
          <th style="width:90px">Actions</th>
        </tr>
      </thead>
      <tbody id="tbody">
        @forelse($users as $i => $user)
        <tr id="row-{{ $user->id }}">

          {{-- # --}}
          <td style="color:var(--fg-faint);font-family:'JetBrains Mono',monospace;font-size:11px">
            {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
          </td>

          {{-- User --}}
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <div class="avatar">
                @if($user->photo)
                  <img src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}"/>
                @else
                  {{ strtoupper(substr($user->name, 0, 2)) }}
                @endif
              </div>
              <span style="font-weight:600;color:var(--fg)">{{ $user->name }}</span>
            </div>
          </td>

          {{-- Age --}}
          <td style="color:var(--fg-muted);font-family:'JetBrains Mono',monospace;font-size:12px">
            {{ $user->age ?? ($user->date_of_birth ? $user->calculateAge() : '—') }}
          </td>

          {{-- Gender --}}
          <td style="text-transform:capitalize;color:var(--fg-muted)">{{ $user->gender }}</td>

          {{-- Status (Note) --}}
          <td>
            @if($user->note)
              <span class="badge @php
                switch($user->note) {
                  case 'work': echo 'badge-active'; break;
                  case 'resign': echo 'badge-inactive'; break;
                  case 'walkout': echo 'badge-unverified'; break;
                  default: echo 'badge-inactive';
                }
              @endphp">
                {{ ucfirst($user->note) }}
              </span>
            @else
              <span style="font-size:12px;color:var(--fg-faint)">—</span>
            @endif
          </td>

          {{-- Role --}}
          <td>
            <span style="font-size:12px;font-family:'JetBrains Mono',monospace;color:var(--fg-muted)">
              {{ $user->role->name ?? '—' }}
            </span>
          </td>

          {{-- Active --}}
          <td>
            <span class="badge {{ $user->active ? 'badge-active' : 'badge-inactive' }}">
              <span class="material-symbols-outlined" style="font-size:12px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">
                {{ $user->active ? 'check_circle' : 'cancel' }}
              </span>
              {{ $user->active ? 'Active' : 'Inactive' }}
            </span>
          </td>

          {{-- Face verify --}}
          <td id="face-cell-{{ $user->id }}">
            @if($user->face_verified)
              <button class="verify-btn verify-btn-done"
                onclick="verifyFace({{ $user->id }})"
                title="Re-verify with CamScan">
                <span class="material-symbols-outlined" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">verified</span>
                Verified
              </button>
            @else
              <button class="verify-btn verify-btn-do"
                onclick="verifyFace({{ $user->id }})"
                title="Verify face via CamScan">
                <span class="material-symbols-outlined" style="font-size:14px">face</span>
                Unverified
              </button>
            @endif
          </td>

          {{-- Actions --}}
          <td>
            <div class="actions">
              <button class="icon-btn"
                onclick="openEdit({{ $user->id }})"
                title="Edit {{ $user->name }}"
                aria-label="Edit {{ $user->name }}">
                <span class="material-symbols-outlined" style="font-size:16px">edit</span>
              </button>
              <button class="icon-btn danger"
                onclick="openDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
                title="Delete {{ $user->name }}"
                aria-label="Delete {{ $user->name }}">
                <span class="material-symbols-outlined" style="font-size:16px">delete</span>
              </button>
            </div>
          </td>

        </tr>
        @empty
        <tr id="empty-row">
          <td colspan="8">
            <div class="empty-state">
              <span class="material-symbols-outlined">group_off</span>
              <p>No users yet. Add one above.</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     SEARCH & IDENTIFY FACE MODAL
═══════════════════════════════════════════════════════════════════ --}}
<div class="overlay" id="search-overlay" onclick="overlayClose(event,'search-overlay')">
  <div class="modal" style="max-width:1000px" role="dialog" aria-modal="true" aria-label="Search & Identify Face">
    <div class="modal-head">
      <h2>Search & Identify Face</h2>
      <button class="icon-btn" onclick="closeSearchCapture()" aria-label="Close">
        <span class="material-symbols-outlined" style="font-size:18px">close</span>
      </button>
    </div>
    <div class="modal-body">
      @include('tabs.users.search_capture')
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     CREATE / EDIT MODAL
═══════════════════════════════════════════════════════════════════ --}}
<div class="overlay" id="form-overlay" onclick="overlayClose(event,'form-overlay')">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">

    <div class="modal-head">
      <h2 id="modal-title">Add user</h2>
      <button class="icon-btn" onclick="closeModal('form-overlay')" aria-label="Close">
        <span class="material-symbols-outlined" style="font-size:18px">close</span>
      </button>
    </div>

    <form id="user-form" method="POST" enctype="multipart/form-data" novalidate>
      @csrf
      <input type="hidden" name="_method" id="f-method" value="POST"/>
      <input type="hidden" id="f-action"/>

      <div class="modal-body">

        {{-- Profile photo --}}
        <div class="field" style="margin-bottom:22px">
          <label>Profile photo</label>
          <div class="photo-upload">
            <div class="photo-preview" id="photo-preview"
              onclick="document.getElementById('f-photo').click()"
              title="Click to upload photo">
              <span class="material-symbols-outlined" style="font-size:22px;color:var(--fg-faint)">add_a_photo</span>
            </div>
            <div>
              <button type="button" class="btn btn-ghost btn-sm"
                onclick="document.getElementById('f-photo').click()">Upload photo</button>
              <p style="font-size:12px;color:var(--fg-faint);margin-top:5px">JPG or PNG, max 2 MB</p>
            </div>
            <input type="file" id="f-photo" name="photo" accept="image/*"
              style="display:none" onchange="previewPhoto(this)"/>
          </div>
        </div>

        {{-- Name + Gender --}}
        <div class="field-row">
          <div class="field">
            <label>Name</label>
            <input type="text" id="f-name" name="name" placeholder="e.g. soung_layhorth" required/>
          </div>
          <div class="field">
            <label>Gender</label>
            <select id="f-gender" name="gender" required>
              <option value="">Select</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>
        </div>

        {{-- Date of Birth + Age --}}
        <div class="field-row">
          <div class="field">
            <label>Date of Birth</label>
            <input type="date" id="f-dob" name="date_of_birth"/>
          </div>
          <div class="field">
            <label>Age</label>
            <input type="number" id="f-age" name="age" placeholder="Auto-calculated" min="1" max="120" readonly/>
          </div>
        </div>

        {{-- Note (walkout/work/resign) --}}
        <div class="field">
          <label>Status Note</label>
          <select id="f-note" name="note">
            <option value="">—</option>
            <option value="work">Working</option>
            <option value="walkout">Walked Out</option>
            <option value="resign">Resigned</option>
          </select>
        </div>

        {{-- Password + Role --}}
        <div class="field-row">
          <div class="field">
            <label>
              Password
              <span id="pw-hint" style="font-size:10px;color:var(--fg-faint);text-transform:none;letter-spacing:0;font-family:'Space Grotesk',sans-serif">
                (leave blank to keep)
              </span>
            </label>
            <input type="password" id="f-password" name="password" placeholder="Min 8 characters"/>
          </div>
          <div class="field">
            <label>Role</label>
            <select id="f-role" name="role_id" required>
              <option value="">Select role</option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Active toggle --}}
        <div class="field" style="margin-bottom:0">
          <label>Account status</label>
          <div class="toggle-wrap">
            <label class="toggle">
              <input type="checkbox" id="f-active" name="active" value="1"/>
              <span class="toggle-track"></span>
            </label>
            <span class="toggle-label" id="active-label">Inactive</span>
          </div>
        </div>

      </div>

      <div class="modal-foot">
        <button type="button" class="btn btn-ghost" onclick="closeModal('form-overlay')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="save-btn">
          <span class="material-symbols-outlined" style="font-size:16px">save</span>
          Save user
        </button>
      </div>
    </form>

  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     DELETE CONFIRM MODAL
═══════════════════════════════════════════════════════════════════ --}}
<div class="overlay" id="del-overlay" onclick="overlayClose(event,'del-overlay')">
  <div class="modal" style="max-width:360px" role="dialog" aria-modal="true">

    <div class="modal-head">
      <h2>Delete user?</h2>
      <button class="icon-btn" onclick="closeModal('del-overlay')" aria-label="Close">
        <span class="material-symbols-outlined" style="font-size:18px">close</span>
      </button>
    </div>

    <div style="padding:22px;text-align:center">
      <span class="material-symbols-outlined" style="font-size:42px;color:var(--err);display:block;margin-bottom:12px">delete_forever</span>
      <p style="font-size:14px;color:var(--fg-muted)">
        <strong id="del-name" style="color:var(--fg)"></strong> will lose all access and be removed from CamScan. This can't be undone.
      </p>
    </div>

    <div class="modal-foot" style="justify-content:center;gap:12px">
      <button class="btn btn-ghost" onclick="closeModal('del-overlay')">Cancel</button>
      <form id="del-form" method="POST" style="margin:0">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete</button>
      </form>
    </div>

  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     FACE CAMERA MODAL
═══════════════════════════════════════════════════════════════════ --}}
<div class="overlay" id="cam-overlay" onclick="overlayClose(event,'cam-overlay')">
  <div class="modal" style="max-width:520px" role="dialog" aria-modal="true" aria-label="Face verification">

    <div class="modal-head">
      <h2>Verify face — CamScan</h2>
      <button class="icon-btn" onclick="closeCameraModal()" aria-label="Close">
        <span class="material-symbols-outlined" style="font-size:18px">close</span>
      </button>
    </div>

    <div class="modal-body" style="text-align:center">

      {{-- Camera frame --}}
      <div class="cam-frame">
        <video id="cam-video" autoplay playsinline muted
          style="width:100%;height:100%;object-fit:cover;display:block"></video>
        <img id="cam-snap" alt="Captured face"
          style="width:100%;height:100%;object-fit:cover;display:none;position:absolute;inset:0"/>
        <div class="cam-guide" aria-hidden="true">
          <div class="cam-guide-oval"></div>
        </div>
      </div>

      <canvas id="cam-canvas" style="display:none"></canvas>

      {{-- Status message --}}
      <p id="cam-status"
        style="font-size:13px;color:var(--fg-muted);margin-bottom:14px;font-family:'JetBrains Mono',monospace;min-height:20px">
        Initialising camera…
      </p>

      {{-- Upload fallback --}}
      <div style="margin-bottom:4px">
        <label style="font-size:12px;color:var(--fg-faint);cursor:pointer;display:inline-flex;align-items:center;gap:5px">
          <span class="material-symbols-outlined" style="font-size:15px">upload_file</span>
          Or upload a photo instead
          <input type="file" id="cam-upload-fallback" name="cam_photo" accept="image/*" style="display:none"/>
        </label>
      </div>

    </div>

    <div class="modal-foot" style="justify-content:center;gap:10px;flex-wrap:wrap">
      <button class="btn btn-ghost" onclick="closeCameraModal()">Cancel</button>
      <button class="btn btn-ghost" id="cam-retake-btn" style="display:none" onclick="retakeFrame()">
        <span class="material-symbols-outlined" style="font-size:16px">replay</span>
        Retake
      </button>
      <button class="btn btn-primary" id="cam-capture-btn" style="display:none" onclick="captureFrame()">
        <span class="material-symbols-outlined" style="font-size:16px">photo_camera</span>
        Capture
      </button>
      <button class="btn btn-primary" id="cam-submit-btn" style="display:none" onclick="submitFacePhoto()">
        <span class="material-symbols-outlined" style="font-size:16px">verified</span>
        Submit to CamScan
      </button>
    </div>

  </div>
</div>

{{-- ─── FIX: compute before @endsection ─── --}}
@php
    $usersJson = $users->map(function ($u) {
        $dob = $u->date_of_birth;
        if ($dob && !($dob instanceof \Carbon\Carbon)) {
            $dob = $u->date_of_birth ? \Carbon\Carbon::parse($u->date_of_birth) : null;
        }
        return [
            'id'            => $u->id,
            'name'          => $u->name,
            'gender'        => $u->gender,
            'age'           => $u->age ?? ($dob ? $dob->diffInYears(now()) : null),
            'date_of_birth' => $dob ? $dob->toDateString() : null,
            'note'          => $u->note,
            'role_id'       => $u->role_id,
            'active'        => (bool) $u->active,
            'face_verified' => (bool) $u->face_verified,
            'photo_url'     => $u->photo ? \Illuminate\Support\Facades\Storage::url($u->photo) : null,
        ];
    });
@endphp

{{-- User data for JS --}}
<script>
const USERS_DATA = @json($usersJson);

const ROUTES = {
    store:        "{{ route('users.store') }}",
    captureStore: "{{ route('users.capture-register') }}",
    update:       (id) => `/users/${id}`,
    destroy:      (id) => `/users/${id}`,
    verifyFace:   (id) => `/users/${id}/verify-face`,    // matches route web.php
    registerFace: (id) => `/users/${id}/register-face`,  // matches route web.php
};
 

const CSRF = "{{ csrf_token() }}";
</script>

@endsection  {{-- <-- @endsection comes AFTER the script block --}}

@push('scripts')
<script>
// ─────────────────────────────────────────────────────────────────────────────
// MODAL HELPERS
// ─────────────────────────────────────────────────────────────────────────────

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

function overlayClose(e, id) {
    if (e.target.id === id) {
        if (id === 'search-overlay') {
            closeSearchCapture();
        } else {
            closeModal(id);
        }
    }
}

function openSearchCapture() {
    document.getElementById('search-overlay').classList.add('open');
    if (typeof startSearchStream === 'function') startSearchStream();
}

function closeSearchCapture() {
    closeModal('search-overlay');
    if (typeof stopSearchStream === 'function') stopSearchStream();
}

// ─────────────────────────────────────────────────────────────────────────────
// CREATE USER
// ─────────────────────────────────────────────────────────────────────────────

function openCreate() {
    document.getElementById('modal-title').textContent   = 'Add user';
    document.getElementById('f-method').value            = 'POST';
    document.getElementById('f-action').value            = '';
    document.getElementById('user-form').action          = ROUTES.store;
    document.getElementById('f-name').value              = '';
    document.getElementById('f-gender').value            = '';
    document.getElementById('f-dob').value               = '';
    document.getElementById('f-age').value               = '';
    document.getElementById('f-note').value              = '';
    document.getElementById('f-password').value          = '';
    document.getElementById('f-role').value              = '';
    document.getElementById('f-active').checked          = false;
    document.getElementById('active-label').textContent  = 'Inactive';
    document.getElementById('pw-hint').style.display     = 'none';
    document.getElementById('f-photo').value             = '';
    resetPhotoPreview();
    document.getElementById('form-overlay').classList.add('open');
    setTimeout(() => document.getElementById('f-name').focus(), 300);
}

// ─────────────────────────────────────────────────────────────────────────────
// EDIT USER
// ─────────────────────────────────────────────────────────────────────────────

function openEdit(id) {
    const u = USERS_DATA.find(x => x.id === id);
    if (!u) return;

    document.getElementById('modal-title').textContent   = 'Edit user';
    document.getElementById('f-method').value            = 'PUT';
    document.getElementById('user-form').action          = ROUTES.update(id);
    document.getElementById('f-name').value              = u.name;
    document.getElementById('f-gender').value            = u.gender;
    document.getElementById('f-dob').value               = u.date_of_birth || '';
    document.getElementById('f-age').value               = u.age || '';
    document.getElementById('f-note').value               = u.note || '';
    document.getElementById('f-password').value          = '';
    document.getElementById('f-role').value              = u.role_id ?? '';
    document.getElementById('f-active').checked          = u.active;
    document.getElementById('active-label').textContent  = u.active ? 'Active' : 'Inactive';
    document.getElementById('pw-hint').style.display     = 'inline';

    const prev = document.getElementById('photo-preview');
    if (u.photo_url) {
        prev.innerHTML = `<img src="${u.photo_url}" alt="${u.name}" style="width:100%;height:100%;object-fit:cover;border-radius:50%"/>`;
    } else {
        resetPhotoPreview();
    }

    document.getElementById('form-overlay').classList.add('open');
}

// ─────────────────────────────────────────────────────────────────────────────
// DELETE USER
// ─────────────────────────────────────────────────────────────────────────────

function openDelete(id, name) {
    document.getElementById('del-name').textContent = name;
    document.getElementById('del-form').action      = ROUTES.destroy(id);
    document.getElementById('del-overlay').classList.add('open');
}

// ─────────────────────────────────────────────────────────────────────────────
// PHOTO PREVIEW
// ─────────────────────────────────────────────────────────────────────────────

function previewPhoto(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('photo-preview').innerHTML =
            `<img src="${e.target.result}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%"/>`;
    };
    reader.readAsDataURL(input.files[0]);
}

function resetPhotoPreview() {
    document.getElementById('photo-preview').innerHTML =
        `<span class="material-symbols-outlined" style="font-size:22px;color:var(--fg-faint)">add_a_photo</span>`;
}

document.getElementById('f-active').addEventListener('change', function () {
    document.getElementById('active-label').textContent = this.checked ? 'Active' : 'Inactive';
});

// ─────────────────────────────────────────────────────────────────────────────
// TABLE SEARCH FILTER
// ─────────────────────────────────────────────────────────────────────────────

function filterTable(q) {
    const rows = document.querySelectorAll('#user-table tbody tr:not(#empty-row)');
    let visible = 0;
    rows.forEach(row => {
        const match = row.textContent.toLowerCase().includes(q.toLowerCase());
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('user-count').textContent =
        visible + ' ' + (visible !== 1 ? 'users' : 'user');
}

// ─────────────────────────────────────────────────────────────────────────────
// FACE VERIFICATION — STEP 1: CHECK CAMSCAN
// ─────────────────────────────────────────────────────────────────────────────

async function verifyFace(userId) {
    const cell = document.getElementById('face-cell-' + userId);
    setFaceCellLoading(cell, 'Checking CamScan…');

    try {
        const res  = await fetch(ROUTES.verifyFace(userId), {
            method:  'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json',
                'Content-Type': 'application/json',
            },
        });

        const data = await res.json();

        if (data.verified) {
            setFaceCellVerified(cell, userId);
            showToast('Face verified — ' + (data.message ?? ''), 'success');
            return;
        }

        if (data.needs_photo) {
            // Open camera modal to capture / upload face photo
            openCameraModal(userId, data.camscan_id ?? null, cell);
            return;
        }

        setFaceCellUnverified(cell, userId);
        showToast(data.message ?? 'Verification failed.', 'error');

    } catch (e) {
        setFaceCellUnverified(cell, userId);
        showToast('Could not reach CamScan service.', 'error');
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// FACE VERIFICATION — STEP 2: CAMERA MODAL
// ─────────────────────────────────────────────────────────────────────────────

let camStream  = null;
let camUserId  = null;
let camScanId  = null;
let camCell    = null;

function openCameraModal(userId, camScanIdVal, cell) {
    camUserId = userId;
    camScanId = camScanIdVal;
    camCell   = cell;
    setFaceCellLoading(cell, 'Opening camera…');
    document.getElementById('cam-overlay').classList.add('open');
    startCamera();
}

async function startCamera() {
    const video = document.getElementById('cam-video');
    const snap  = document.getElementById('cam-snap');

    snap.style.display  = 'none';
    video.style.display = 'block';

    document.getElementById('cam-capture-btn').style.display = 'inline-flex';
    document.getElementById('cam-retake-btn').style.display  = 'none';
    document.getElementById('cam-submit-btn').style.display  = 'none';
    setCamStatus('Position your face inside the oval, then capture.');

    try {
        camStream = await navigator.mediaDevices.getUserMedia({
            video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
        });
        video.srcObject = camStream;
    } catch (err) {
        setCamStatus('Camera unavailable — upload a photo instead.');
        document.getElementById('cam-capture-btn').style.display = 'none';
    }
}

function captureFrame() {
    const video  = document.getElementById('cam-video');
    const snap   = document.getElementById('cam-snap');
    const canvas = document.getElementById('cam-canvas');

    canvas.width  = video.videoWidth  || 640;
    canvas.height = video.videoHeight || 480;
    canvas.getContext('2d').drawImage(video, 0, 0);

    snap.src            = canvas.toDataURL('image/jpeg', 0.92);
    snap.style.display  = 'block';
    video.style.display = 'none';

    // Pause stream (keep tracks alive for retake)
    if (camStream) camStream.getTracks().forEach(t => { t.enabled = false; });

    document.getElementById('cam-capture-btn').style.display = 'none';
    document.getElementById('cam-retake-btn').style.display  = 'inline-flex';
    document.getElementById('cam-submit-btn').style.display  = 'inline-flex';
    setCamStatus('Looking good? Submit or retake.');
}

function retakeFrame() {
    if (camStream) camStream.getTracks().forEach(t => { t.enabled = true; });
    startCamera();
}

 
async function submitFacePhoto() {
    const canvas    = document.getElementById('cam-canvas');
    const submitBtn = document.getElementById('cam-submit-btn');
 
    submitBtn.disabled  = true;
    submitBtn.innerHTML = `<span class="material-symbols-outlined spin" style="font-size:16px">refresh</span> Uploading…`;
    setCamStatus('Sending to CamScan for verification…');
 
    canvas.toBlob(async (blob) => {
        const form = new FormData();
        form.append('photo', blob, 'face.jpg');          // ← 'photo' matches validation
        form.append('_token', CSRF);                     // ← CSRF required for POST
        if (camScanId !== null) {
            form.append('camscan_id', camScanId);
        }
 
        try {
            const res  = await fetch(ROUTES.registerFace(camUserId), {
                method: 'POST',
                body:   form,
                // Do NOT set Content-Type — browser sets multipart boundary automatically
            });
 
            const data = await res.json();
            closeCameraModal();
 
            if (data.verified) {
                setFaceCellVerified(camCell, camUserId);
                showToast('Face registered and verified in CamScan.', 'success');
            } else {
                setFaceCellUnverified(camCell, camUserId);
                showToast(data.message ?? 'Face registration failed.', 'error');
            }
 
        } catch (e) {
            closeCameraModal();
            setFaceCellUnverified(camCell, camUserId);
            showToast('Upload error. Try again.', 'error');
        }
 
    }, 'image/jpeg', 0.92);
}

function closeCameraModal() {
    if (camStream) {
        camStream.getTracks().forEach(t => t.stop());
        camStream = null;
    }
    document.getElementById('cam-overlay').classList.remove('open');
    document.getElementById('cam-submit-btn').disabled  = false;
    document.getElementById('cam-submit-btn').innerHTML =
        `<span class="material-symbols-outlined" style="font-size:16px">verified</span> Submit to CamScan`;
}

// Upload fallback (file input)
document.getElementById('cam-upload-fallback').addEventListener('change', function () {
    if (!this.files || !this.files[0]) return;
    const img = new Image();
    img.onload = () => {
        const canvas = document.getElementById('cam-canvas');
        canvas.width  = img.width;
        canvas.height = img.height;
        canvas.getContext('2d').drawImage(img, 0, 0);
        const snap           = document.getElementById('cam-snap');
        snap.src             = canvas.toDataURL();
        snap.style.display   = 'block';
        document.getElementById('cam-video').style.display  = 'none';
        document.getElementById('cam-capture-btn').style.display = 'none';
        document.getElementById('cam-retake-btn').style.display  = 'inline-flex';
        document.getElementById('cam-submit-btn').style.display  = 'inline-flex';
        setCamStatus('Photo ready — submit or retake.');
    };
    img.src = URL.createObjectURL(this.files[0]);
});

// ─────────────────────────────────────────────────────────────────────────────
// FACE CELL STATE HELPERS
// ─────────────────────────────────────────────────────────────────────────────

function setFaceCellLoading(cell, msg) {
    cell.innerHTML = `
        <span style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:var(--fg-faint)">
            <span class="material-symbols-outlined spin" style="font-size:16px">refresh</span>
            ${msg}
        </span>`;
}

function setFaceCellVerified(cell, userId) {
    cell.innerHTML = `
        <button class="verify-btn verify-btn-done" onclick="verifyFace(${userId})" title="Re-verify">
            <span class="material-symbols-outlined" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">verified</span>
            Verified
        </button>`;
}

function setFaceCellUnverified(cell, userId) {
    cell.innerHTML = `
        <button class="verify-btn verify-btn-do" onclick="verifyFace(${userId})" title="Verify face">
            <span class="material-symbols-outlined" style="font-size:14px">face</span>
            Unverified
        </button>`;
}

function setCamStatus(msg) {
    document.getElementById('cam-status').textContent = msg;
}

// ─────────────────────────────────────────────────────────────────────────────
// TOAST
// ─────────────────────────────────────────────────────────────────────────────

function showToast(msg, type = 'success') {
    const stack = document.getElementById('toasts');
    const t     = document.createElement('div');
    const icon  = type === 'success' ? 'check_circle' : 'error';
    t.className = 'toast ' + type;
    t.innerHTML = `<span class="material-symbols-outlined" style="font-size:17px;flex-shrink:0;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">${icon}</span>${msg}`;
    stack.appendChild(t);
    setTimeout(() => {
        t.style.opacity   = '0';
        t.style.transform = 'translateX(20px)';
        setTimeout(() => t.remove(), 320);
    }, 3500);
}

// Date of Birth to Age calculation
function calculateAgeFromDob(dobStr) {
    if (!dobStr) return null;
    const dob = new Date(dobStr);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const monthDiff = today.getMonth() - dob.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    return age;
}

function updateAgeFromDobModal() {
    const dobInput = document.getElementById('f-dob');
    const ageInput = document.getElementById('f-age');
    if (dobInput && ageInput) {
        const age = calculateAgeFromDob(dobInput.value);
        ageInput.value = age !== null ? age : '';
    }
}

// Initialize date of birth listeners
document.addEventListener('DOMContentLoaded', function() {
    const dobInput = document.getElementById('f-dob');
    if (dobInput) {
        dobInput.addEventListener('change', updateAgeFromDobModal);
        dobInput.addEventListener('input', updateAgeFromDobModal);
    }
});

// Show Laravel session toast on page load
@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast("{{ session('success') }}", 'success'));
@endif
@if(session('error'))
    document.addEventListener('DOMContentLoaded', () => showToast("{{ session('error') }}", 'error'));
@endif
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => showToast("{{ $errors->first() }}", 'error'));
@endif
</script>
@endpush
