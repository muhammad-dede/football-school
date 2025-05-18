<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['guard_name' => 'web', 'name' => 'role-index'],
            ['guard_name' => 'web', 'name' => 'role-create'],
            ['guard_name' => 'web', 'name' => 'role-edit'],
            ['guard_name' => 'web', 'name' => 'role-delete'],
        ];

        foreach ($permissions as $key => $value) {
            Permission::create($value);
        }

        $roles = [
            ['guard_name' => 'web', 'name' => 'Admin'],
            ['guard_name' => 'web', 'name' => 'Coach'],
            ['guard_name' => 'web', 'name' => 'Student'],
            ['guard_name' => 'web', 'name' => 'Leader'],
        ];

        foreach ($roles as $key => $value) {
            $role =  Role::create($value);

            if ($role->name === 'Admin') {
                $role->givePermissionTo('role-index');
                $role->givePermissionTo('role-create');
                $role->givePermissionTo('role-edit');
                $role->givePermissionTo('role-delete');
            }
        }

        $users = [
            [
                'name' => 'Admin SSB',
                'email' => 'admin@ssb.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Pimpinan SSB',
                'email' => 'leader@ssb.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ],
        ];

        foreach ($users as $key => $value) {
            $user = User::create($value);
            if ($user->name === 'Admin SSB') {
                $user->assignRole('Admin');
            }
            if ($user->name === 'Pimpinan SSB') {
                $user->assignRole('Leader');
            }
        }
    }
}
