<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->latest()->get();
        $roles = Role::orderBy('name')->get();
        return view('tabs.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255', 'unique:users'],
            'gender'   => ['required', 'in:male,female,other'],
            'password' => ['required', 'string', 'min:8'],
            'role_id'  => ['required', 'exists:roles,id'],
            'active'   => ['boolean'],
            'photo'    => ['nullable', 'image', 'max:2048'],
        ]);

        $data['active']        = $request->boolean('active');
        $data['face_verified'] = false;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        User::create($data);
        return back()->with('success', "User '{$data['name']}' created.");
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255', 'unique:users,name,' . $user->id],
            'gender'   => ['required', 'in:male,female,other'],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id'  => ['required', 'exists:roles,id'],
            'active'   => ['boolean'],
            'photo'    => ['nullable', 'image', 'max:2048'],
        ]);

        $data['active'] = $request->boolean('active');

        if ($request->hasFile('photo')) {
            if ($user->photo) Storage::disk('public')->delete($user->photo);
            $data['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        if (empty($data['password'])) unset($data['password']);

        $user->update($data);
        return back()->with('success', "User '{$user->name}' updated.");
    }

    public function destroy(User $user)
    {
        $this->deleteFastapiUser($user);
        if ($user->photo) Storage::disk('public')->delete($user->photo);
        $user->delete();
        return back()->with('success', 'User deleted.');
    }

    // ─── STEP 1: Check if user exists in CamScan ─────────────────────────────

    public function verifyFace(Request $request, User $user)
    {
        $baseUrl = config('services.fastapi.url');

        try {
            $listRes = Http::timeout(10)->get("{$baseUrl}/register/users");

            if (! $listRes->successful()) {
                return response()->json([
                    'verified' => false,
                    'message'  => 'Cannot reach CamScan service.',
                ], 503);
            }

            $camScanUsers = collect($listRes->json());

            $matched = $camScanUsers->first(
                fn($u) => strtolower(trim($u['name'])) === strtolower(trim($user->name))
            );

            if ($matched) {
                // User found — check if embedding exists
                if (! empty($matched['face_embeding'])) {
                    $user->update(['face_verified' => true]);
                    return response()->json([
                        'verified'   => true,
                        'camscan_id' => $matched['id'],
                        'message'    => 'Face verified via CamScan.',
                    ]);
                }

                // User exists but no face photo yet
                return response()->json([
                    'verified'    => false,
                    'needs_photo' => true,
                    'camscan_id'  => $matched['id'],
                    'message'     => 'User found in CamScan but has no face photo yet.',
                ]);
            }

            // Not in CamScan at all — need full registration
            return response()->json([
                'verified'    => false,
                'needs_photo' => true,
                'camscan_id'  => null,
                'message'     => 'User not registered in CamScan yet.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'verified' => false,
                'message'  => 'CamScan error: ' . $e->getMessage(),
            ], 503);
        }
    }

    // ─── STEP 2: Upload face photo to CamScan ────────────────────────────────

    public function registerFace(Request $request, User $user)
    {
        $request->validate([
            'photo'      => ['required', 'image', 'mimes:jpeg,png,webp', 'max:10240'],
            'camscan_id' => ['nullable', 'integer'],
        ]);

        $baseUrl    = config('services.fastapi.url');
        $photoFile  = $request->file('photo');
        $camScanId  = $request->input('camscan_id');
        $fileBytes  = file_get_contents($photoFile->getRealPath());
        $fileName   = $photoFile->getClientOriginalName() ?: 'face.jpg';

        try {
            if ($camScanId) {
                // ── User exists in CamScan → just add face photo ──────────────
                $response = Http::timeout(30)
                    ->asMultipart()
                    ->attach('face_image', $fileBytes, $fileName)   // ← key must be 'face_image'
                    ->post("{$baseUrl}/register/user/{$camScanId}/face");
            } else {
                // ── User not in CamScan → full registration ───────────────────
                $response = Http::timeout(30)
                    ->asMultipart()
                    ->attach('face_image', $fileBytes, $fileName)   // ← key must be 'face_image'
                    ->attach('name',       $user->name,    '')
                    ->attach('position',   $user->role?->name ?? '', '')
                    ->post("{$baseUrl}/register/user");
            }

            if ($response->successful()) {
                // Save photo locally too
                $path = $photoFile->store('users/photos', 'public');
                $user->update([
                    'face_verified' => true,
                    'photo'         => $user->photo ?? $path,
                ]);

                return response()->json([
                    'verified' => true,
                    'message'  => 'Face registered and verified in CamScan.',
                ]);
            }

            // FastAPI returned an error — surface the detail
            $detail = $response->json('detail') ?? $response->body();

            return response()->json([
                'verified' => false,
                'message'  => 'CamScan rejected the photo: ' . $detail,
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'verified' => false,
                'message'  => 'Upload failed: ' . $e->getMessage(),
            ], 503);
        }
    }

    // ─── Delete from CamScan when deleting from Laravel ──────────────────────

    private function deleteFastapiUser(User $user): void
    {
        try {
            $baseUrl = config('services.fastapi.url');
            $listRes = Http::timeout(5)->get("{$baseUrl}/register/users");
            if (! $listRes->successful()) return;

            $matched = collect($listRes->json())->first(
                fn($u) => strtolower(trim($u['name'])) === strtolower(trim($user->name))
            );

            if ($matched) {
                Http::timeout(5)->delete("{$baseUrl}/register/user/{$matched['id']}");
            }
        } catch (\Exception) {
            // Non-fatal — don't block the Laravel delete
        }
    }
}