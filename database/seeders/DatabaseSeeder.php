<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tab;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
   // Tabs
        $tabs = [
            ['name' => 'Dashboard',       'slug' => 'dashboard',       'route' => 'dashboard',   'order' => 1],
            ['name' => 'Detection',      'slug' => 'detection',      'route' => 'detection',  'order' => 2],
            ['name' => 'Reports',         'slug' => 'reports',         'route' => 'reports',     'order' => 3],
            ['name' => 'Users',           'slug' => 'users',           'route' => 'users',       'order' => 4],
            ['name' => 'Settings',        'slug' => 'settings',        'route' => 'settings',    'order' => 5],
            ['name' => 'Role Management', 'slug' => 'role-management', 'route' => 'roles.index', 'order' => 6],
            ['name' => 'Video Management', 'slug' => 'video-management', 'route' => 'videos.index', 'order' => 7],
        ];

        foreach ($tabs as $tab) {
            Tab::create($tab);
        }

        // Roles
        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Full access',
        ]);

        $staffRole = Role::create([
            'name'        => 'Staff',
            'slug'        => 'staff',
            'description' => 'Attendance and dashboard only',
        ]);

        // Assign tabs
        $adminRole->tabs()->sync(Tab::all()->pluck('id'));
        $staffRole->tabs()->sync(
            Tab::whereIn('slug', ['dashboard', 'attendance'])->pluck('id')
        );

        // Users
        User::create([
            'name'     => 'admin',
            'gender'   => 'male',
            'password' => bcrypt('password'),
            'active'   => true,
            'role_id'  => $adminRole->id,
        ]);

        User::create([
            'name'     => 'jane',
            'gender'   => 'female',
            'password' => bcrypt('password'),
            'active'   => true,
            'role_id'  => $staffRole->id,
        ]);

        User::create([
            'name'     => 'john',
            'gender'   => 'male',
            'password' => bcrypt('password'),
            'active'   => false,
            'role_id'  => $staffRole->id,
        ]);
    }
}
