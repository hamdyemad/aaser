<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Admin;
use App\Models\Image;
use App\Models\AdminRole;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'Episodes',
            'Tourist-Attractions',
            'Sponsors',
            'Participants',
            'Your-Guide',
            'Contact-us',
            'History-of-Asir',
            'Exhibitions-and-conferences',
            'Entertainment-activities',
            'Points-and-ads',
            'Powers',
            'Service-provider',
            'users',
            'admins',
            'advertisements',
            'settings',
            'Points-store',
            'Exchanging-points-rewards'
        ];
        foreach($permissions as $permission)
        {
            Permission::create([
                'name' => $permission,
            ]);
        }
        $role = Role::create([
            'name' => 'Super Admin'
        ]);
        $all_permissions = Permission::all();
        foreach($all_permissions as $permission)
        {
            RolePermission::create([
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ]);
        }
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'phone' => '01015864793',
        ]);
        AdminRole::create([
            'role_id' => $role->id,
            'admin_id' => $admin->id,
        ]);
        Image::create([
            'id' => 1,
            'image' => null,
        ]);
        Image::create([
            'id' => 2,
            'image' => null,
        ]);
        Image::create([
            'id' => 3,
            'image' => null,
        ]);
        Image::create([
            'id' => 4,
            'image' => null,
        ]);
    }
}
