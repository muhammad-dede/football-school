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
            // Role
            ['guard_name' => 'web', 'name' => 'role-index'],
            ['guard_name' => 'web', 'name' => 'role-create'],
            ['guard_name' => 'web', 'name' => 'role-edit'],
            ['guard_name' => 'web', 'name' => 'role-delete'],
            // User
            ['guard_name' => 'web', 'name' => 'user-index'],
            ['guard_name' => 'web', 'name' => 'user-create'],
            ['guard_name' => 'web', 'name' => 'user-edit'],
            ['guard_name' => 'web', 'name' => 'user-delete'],
            // Period
            ['guard_name' => 'web', 'name' => 'period-index'],
            ['guard_name' => 'web', 'name' => 'period-create'],
            ['guard_name' => 'web', 'name' => 'period-edit'],
            ['guard_name' => 'web', 'name' => 'period-delete'],
            ['guard_name' => 'web', 'name' => 'period-status'],
            // Class
            ['guard_name' => 'web', 'name' => 'class-index'],
            ['guard_name' => 'web', 'name' => 'class-create'],
            ['guard_name' => 'web', 'name' => 'class-edit'],
            ['guard_name' => 'web', 'name' => 'class-delete'],
            ['guard_name' => 'web', 'name' => 'class-status'],
            // Training
            ['guard_name' => 'web', 'name' => 'training-index'],
            ['guard_name' => 'web', 'name' => 'training-create'],
            ['guard_name' => 'web', 'name' => 'training-edit'],
            ['guard_name' => 'web', 'name' => 'training-delete'],
            ['guard_name' => 'web', 'name' => 'training-status'],
            // Match
            ['guard_name' => 'web', 'name' => 'match-index'],
            ['guard_name' => 'web', 'name' => 'match-create'],
            ['guard_name' => 'web', 'name' => 'match-edit'],
            ['guard_name' => 'web', 'name' => 'match-delete'],
            ['guard_name' => 'web', 'name' => 'match-status'],
            // Coach
            ['guard_name' => 'web', 'name' => 'coach-index'],
            ['guard_name' => 'web', 'name' => 'coach-create'],
            ['guard_name' => 'web', 'name' => 'coach-edit'],
            ['guard_name' => 'web', 'name' => 'coach-delete'],
            ['guard_name' => 'web', 'name' => 'coach-status'],
            ['guard_name' => 'web', 'name' => 'coach-show'],
            // Student
            ['guard_name' => 'web', 'name' => 'student-index'],
            ['guard_name' => 'web', 'name' => 'student-create'],
            ['guard_name' => 'web', 'name' => 'student-edit'],
            ['guard_name' => 'web', 'name' => 'student-delete'],
            ['guard_name' => 'web', 'name' => 'student-status'],
            ['guard_name' => 'web', 'name' => 'student-show'],
        ];

        foreach ($permissions as $key => $value) {
            Permission::create($value);
        }

        $roles = [
            ['guard_name' => 'web', 'name' => 'Super Admin'],
            ['guard_name' => 'web', 'name' => 'Admin'],
            ['guard_name' => 'web', 'name' => 'Coach'],
            ['guard_name' => 'web', 'name' => 'Student'],
            ['guard_name' => 'web', 'name' => 'Leader'],
        ];

        foreach ($roles as $key => $value) {
            $role =  Role::create($value);

            if ($role->name === 'Admin') {
                // Period
                $role->givePermissionTo('period-index');
                $role->givePermissionTo('period-create');
                $role->givePermissionTo('period-edit');
                $role->givePermissionTo('period-delete');
                $role->givePermissionTo('period-status');
                // Class
                $role->givePermissionTo('class-index');
                $role->givePermissionTo('class-create');
                $role->givePermissionTo('class-edit');
                $role->givePermissionTo('class-delete');
                $role->givePermissionTo('class-status');
                // Training
                $role->givePermissionTo('training-index');
                $role->givePermissionTo('training-create');
                $role->givePermissionTo('training-edit');
                $role->givePermissionTo('training-delete');
                $role->givePermissionTo('training-status');
                // Match
                $role->givePermissionTo('match-index');
                $role->givePermissionTo('match-create');
                $role->givePermissionTo('match-edit');
                $role->givePermissionTo('match-delete');
                $role->givePermissionTo('match-status');
                // Coach
                $role->givePermissionTo('coach-index');
                $role->givePermissionTo('coach-create');
                $role->givePermissionTo('coach-edit');
                $role->givePermissionTo('coach-delete');
                $role->givePermissionTo('coach-status');
                $role->givePermissionTo('coach-show');
                // Student
                $role->givePermissionTo('student-index');
                $role->givePermissionTo('student-create');
                $role->givePermissionTo('student-edit');
                $role->givePermissionTo('student-delete');
                $role->givePermissionTo('student-status');
                $role->givePermissionTo('student-show');
            }
        }

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'super.admin@ssb.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ],
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
            if ($user->name === 'Super Admin') {
                $user->assignRole('Super Admin');
            }
            if ($user->name === 'Admin SSB') {
                $user->assignRole('Admin');
            }
            if ($user->name === 'Pimpinan SSB') {
                $user->assignRole('Leader');
            }
        }
    }
}
