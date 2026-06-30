<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            'name'         => ['required', 'string', 'max:255', 'unique:users'],
            'gender'       => ['required', 'in:male,female,other'],
            'password'     => ['required', 'string', 'min:8'],
            'role_id'      => ['required', 'exists:roles,id'],
            'active'       => ['boolean'],
            'photo'        => ['nullable', 'image', 'max:2048'],
            'date_of_birth' => ['nullable', 'date'],
            'age'          => ['nullable', 'integer', 'min:1', 'max:120'],
            'note'         => ['nullable', 'in:walkout,work,resign'],
        ]);

        $data['active']        = $request->boolean('active');
        $data['face_verified'] = false;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        // Calculate age from date_of_birth if provided and age is not
        if ($request->filled('date_of_birth') && ! $request->filled('age')) {
            $dob = \Carbon\Carbon::parse($request->date_of_birth);
            $data['age'] = $dob->diffInYears(now());
            $data['date_of_birth'] = $dob;
        }

        $user = User::create($data);
        
        // Sync with FastAPI if configured
        if ($baseUrl = config('services.fastapi.url')) {
            $this->syncUserToFastApi($user);
        }
        
        return back()->with('success', "User '{$data['name']}' created.");
    }

    public function captureRegister(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255', 'unique:users'],
            'gender'       => ['required', 'in:male,female,other'],
            'password'     => ['required', 'string', 'min:8'],
            'role_id'      => ['required', 'exists:roles,id'],
            'active'       => ['boolean'],
            'face_image'   => ['required', 'image', 'mimes:jpeg,png,webp', 'max:10240'],
            'date_of_birth' => ['nullable', 'date'],
            'age'          => ['nullable', 'integer', 'min:1', 'max:120'],
            'note'         => ['nullable', 'in:walkout,work,resign'],
        ]);

        $photoFile = $request->file('face_image');
        $photoPath = $photoFile->store('users/photos', 'public');

        // Calculate age from date_of_birth if provided and age is not
        $age = $data['age'] ?? null;
        $dateOfBirth = $request->date_of_birth ? \Carbon\Carbon::parse($request->date_of_birth) : null;
        $note = $request->note;
        
        if ($dateOfBirth && ! $age) {
            $age = $dateOfBirth->diffInYears(now());
        }

        $user = User::create([
            'name'          => $data['name'],
            'gender'        => $data['gender'],
            'password'      => $data['password'],
            'role_id'       => $data['role_id'],
            'active'        => $request->boolean('active'),
            'photo'         => $photoPath,
            'face_verified' => false,
            'date_of_birth' => $dateOfBirth,
            'age'           => $age,
            'note'          => $note,
        ]);

        $camScanSynced = false;
        $camScanMessage = 'User created locally. CamScan service was not configured.';

        if ($baseUrl = config('services.fastapi.url')) {
            try {
                $fastApiData = [
                    'face_image'   => file_get_contents($photoFile->getRealPath()),
                    'name'         => $user->name,
                    'position'     => $user->role?->name ?? '',
                    'date_of_birth' => $user->date_of_birth ? $user->date_of_birth->toDateString() : null,
                    'age'          => $user->age,
                    'note'         => $user->note,
                ];

                $response = Http::timeout(30)
                    ->asMultipart();

                foreach ($fastApiData as $key => $value) {
                    if ($key === 'face_image') {
                        $response->attach($key, $value, $photoFile->getClientOriginalName() ?: 'face.jpg');
                    } else {
                        $response->attach($key, $value ?? '', '');
                    }
                }

                $response = $response->post("{$baseUrl}/register/user");

                if ($response->successful()) {
                    $camScanSynced = true;
                    $camScanMessage = 'User created and face registered in CamScan.';
                    $user->update(['face_verified' => true]);
                } else {
                    $camScanMessage = 'User created locally, but CamScan rejected the photo: '
                        . ($response->json('detail') ?? $response->body());
                }
            } catch (\Exception $e) {
                $camScanMessage = 'User created locally, but CamScan upload failed: ' . $e->getMessage();
            }
        }

        return response()->json([
            'success'       => true,
            'user_id'       => $user->id,
            'name'          => $user->name,
            'age'           => $user->age,
            'date_of_birth' => $user->date_of_birth ? $user->date_of_birth->toDateString() : null,
            'note'          => $user->note,
            'face_verified' => $camScanSynced,
            'message'       => $camScanMessage,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255', 'unique:users,name,' . $user->id],
            'gender'       => ['required', 'in:male,female,other'],
            'password'     => ['nullable', 'string', 'min:8'],
            'role_id'      => ['required', 'exists:roles,id'],
            'active'       => ['boolean'],
            'photo'        => ['nullable', 'image', 'max:2048'],
            'date_of_birth' => ['nullable', 'date'],
            'age'          => ['nullable', 'integer', 'min:1', 'max:120'],
            'note'         => ['nullable', 'in:walkout,work,resign'],
        ]);

        $data['active'] = $request->boolean('active');

        if ($request->hasFile('photo')) {
            if ($user->photo) Storage::disk('public')->delete($user->photo);
            $data['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        // Calculate age from date_of_birth if provided
        if ($request->filled('date_of_birth')) {
            $dob = \Carbon\Carbon::parse($request->date_of_birth);
            $data['age'] = $dob->diffInYears(now());
            $data['date_of_birth'] = $dob;
        }

        if (empty($data['password'])) unset($data['password']);

        // Capture original name before updating database
        $oldName = $user->name;

        $user->update($data);
        
        // Sync with FastAPI if name, date_of_birth, or note changed
        $this->syncUserToFastApi($user, $oldName);
        
        return back()->with('success', "User '{$user->name}' updated.");
    }

    public function destroy(User $user)
    {
        $this->deleteFastapiUser($user);
        if ($user->photo) Storage::disk('public')->delete($user->photo);
        $user->delete();
        return back()->with('success', 'User deleted.');
    }

    /**
     * API endpoint for FastAPI to update user info (name, dob, age)
     * Called by FastAPI when user is updated directly in Python
     */
    public function apiUpdateFromFastApi(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'age'           => ['nullable', 'integer', 'min:1', 'max:120'],
            'note'          => ['nullable', 'in:walkout,work,resign'],
        ]);

        // Find user by name
        $user = User::where('name', $data['name'])->first();
        
        if (!$user) {
            // If user doesn't exist in Laravel, create them
            $user = User::create([
                'name'          => $data['name'],
                'gender'        => 'other', // default
                'password'      => bcrypt('temp_password'), // default password
                'role_id'       => 1, // default role
                'active'        => true,
                'face_verified' => false,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'age'           => $data['age'] ?? null,
                'note'          => $data['note'] ?? null,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'User created from FastAPI',
                'user_id' => $user->id,
            ]);
        }

        // Update existing user
        if (isset($data['date_of_birth'])) {
            $user->date_of_birth = $data['date_of_birth'];
            // Recalculate age from date_of_birth
            if ($user->date_of_birth) {
                $user->age = $user->calculateAge();
            }
        }
        if (isset($data['age'])) {
            $user->age = $data['age'];
        }
        if (isset($data['note'])) {
            $user->note = $data['note'];
        }
        // Name is used for lookup, don't change it here
        
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'User synced from FastAPI',
            'user_id' => $user->id,
        ]);
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
                $dateOfBirth = $user->date_of_birth ? ($user->date_of_birth instanceof \Carbon\Carbon ? $user->date_of_birth : \Carbon\Carbon::parse($user->date_of_birth)) : null;
                
                $fastApiData = [
                    'face_image'   => $fileBytes,
                    'name'         => $user->name,
                    'position'     => $user->role?->name ?? '',
                    'date_of_birth' => $dateOfBirth ? $dateOfBirth->toDateString() : null,
                    'age'          => $user->age,
                    'note'         => $user->note,
                ];

                $response = Http::timeout(30)
                    ->asMultipart();

                foreach ($fastApiData as $key => $value) {
                    if ($key === 'face_image') {
                        $response->attach($key, $value, $fileName);
                    } else {
                        $response->attach($key, $value ?? '', '');
                    }
                }

                $response = $response->post("{$baseUrl}/register/user");
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

    // ─── Sync user to FastAPI ───────────────────────────────────────────────

    private function syncUserToFastApi(User $user, ?string $oldName = null): void
    {
        $baseUrl = config('services.fastapi.url');
        if (! $baseUrl) return;

        try {
            // Find the user in FastAPI by original/current name
            $listRes = Http::timeout(5)->get("{$baseUrl}/register/users");
            if (! $listRes->successful()) return;

            $searchName = $oldName ?? $user->name;
            $matched = collect($listRes->json())->first(
                fn($u) => strtolower(trim($u['name'])) === strtolower(trim($searchName))
            );

            if ($matched && $matched['id']) {
                // Build update data - always sync all fields from user model
                $updateData = [];
                
                // Always include name
                $updateData['name'] = $user->name;
                
                // Include date_of_birth if user has it
                if ($user->date_of_birth) {
                    $updateData['date_of_birth'] = $user->date_of_birth instanceof \Carbon\Carbon
                        ? $user->date_of_birth->toDateString()
                        : \Carbon\Carbon::parse($user->date_of_birth)->toDateString();
                }
                
                // Include age if user has it
                if ($user->age !== null) {
                    $updateData['age'] = $user->age;
                }
                
                // Include note if user has it
                if ($user->note !== null) {
                    $updateData['note'] = $user->note;
                }
                
                if (!empty($updateData)) {
                    Http::timeout(10)->put("{$baseUrl}/register/user/{$matched['id']}", $updateData);
                }
            }
        } catch (\Exception $e) {
            // Log error but don't block the Laravel update
            Log::error("Failed to sync user to FastAPI: " . $e->getMessage());
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
