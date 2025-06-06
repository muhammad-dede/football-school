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
            // Dashboard
            ['guard_name' => 'web', 'name' => 'dashboard'],
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
            ['guard_name' => 'web', 'name' => 'user-status'],
            // Period
            ['guard_name' => 'web', 'name' => 'period-index'],
            ['guard_name' => 'web', 'name' => 'period-create'],
            ['guard_name' => 'web', 'name' => 'period-edit'],
            ['guard_name' => 'web', 'name' => 'period-delete'],
            ['guard_name' => 'web', 'name' => 'period-status'],
            // Group
            ['guard_name' => 'web', 'name' => 'group-index'],
            ['guard_name' => 'web', 'name' => 'group-create'],
            ['guard_name' => 'web', 'name' => 'group-edit'],
            ['guard_name' => 'web', 'name' => 'group-delete'],
            ['guard_name' => 'web', 'name' => 'group-status'],
            // Coach
            ['guard_name' => 'web', 'name' => 'coach-index'],
            ['guard_name' => 'web', 'name' => 'coach-create'],
            ['guard_name' => 'web', 'name' => 'coach-edit'],
            ['guard_name' => 'web', 'name' => 'coach-delete'],
            ['guard_name' => 'web', 'name' => 'coach-show'],
            ['guard_name' => 'web', 'name' => 'coach-status'],
            // Student
            ['guard_name' => 'web', 'name' => 'student-index'],
            ['guard_name' => 'web', 'name' => 'student-create'],
            ['guard_name' => 'web', 'name' => 'student-edit'],
            ['guard_name' => 'web', 'name' => 'student-delete'],
            ['guard_name' => 'web', 'name' => 'student-show'],
            ['guard_name' => 'web', 'name' => 'student-status'],
            ['guard_name' => 'web', 'name' => 'student-program-create'],
            ['guard_name' => 'web', 'name' => 'student-program-edit'],
            ['guard_name' => 'web', 'name' => 'student-program-delete'],
            ['guard_name' => 'web', 'name' => 'student-payment-create'],
            // Billing
            ['guard_name' => 'web', 'name' => 'billing-index'],
            ['guard_name' => 'web', 'name' => 'billing-payment-create'],
            // Training
            ['guard_name' => 'web', 'name' => 'training-index'],
            ['guard_name' => 'web', 'name' => 'training-create'],
            ['guard_name' => 'web', 'name' => 'training-edit'],
            ['guard_name' => 'web', 'name' => 'training-delete'],
            ['guard_name' => 'web', 'name' => 'training-show'],
            ['guard_name' => 'web', 'name' => 'training-attendance'],
            // Match event
            ['guard_name' => 'web', 'name' => 'match-event-index'],
            ['guard_name' => 'web', 'name' => 'match-event-create'],
            ['guard_name' => 'web', 'name' => 'match-event-edit'],
            ['guard_name' => 'web', 'name' => 'match-event-delete'],
            ['guard_name' => 'web', 'name' => 'match-event-show'],
            ['guard_name' => 'web', 'name' => 'match-event-attendance'],
            // Report
            ['guard_name' => 'web', 'name' => 'report-student-index'],
            ['guard_name' => 'web', 'name' => 'report-student-score'],
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
                // Dashboard
                $role->givePermissionTo('dashboard');
                // Period
                $role->givePermissionTo('period-index');
                $role->givePermissionTo('period-create');
                $role->givePermissionTo('period-edit');
                $role->givePermissionTo('period-delete');
                $role->givePermissionTo('period-status');
                // Group
                $role->givePermissionTo('group-index');
                $role->givePermissionTo('group-create');
                $role->givePermissionTo('group-edit');
                $role->givePermissionTo('group-delete');
                $role->givePermissionTo('group-status');
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
                $role->givePermissionTo('student-program-create');
                $role->givePermissionTo('student-program-edit');
                $role->givePermissionTo('student-program-delete');
                $role->givePermissionTo('student-payment-create');
                // Billing
                $role->givePermissionTo('billing-index');
                $role->givePermissionTo('billing-payment-create');
                // Training
                $role->givePermissionTo('training-index');
                $role->givePermissionTo('training-create');
                $role->givePermissionTo('training-edit');
                $role->givePermissionTo('training-delete');
                $role->givePermissionTo('training-show');
                $role->givePermissionTo('training-attendance');
                // Match Event
                $role->givePermissionTo('match-event-index');
                $role->givePermissionTo('match-event-create');
                $role->givePermissionTo('match-event-edit');
                $role->givePermissionTo('match-event-delete');
                $role->givePermissionTo('match-event-show');
                $role->givePermissionTo('match-event-attendance');
                // Report
                $role->givePermissionTo('report-student-index');
                $role->givePermissionTo('report-student-score');
            }
            if ($role->name === 'Leader') {
                // Dashboard
                $role->givePermissionTo('dashboard');
            }
            if ($role->name === 'Student') {
                // Dashboard
                $role->givePermissionTo('dashboard');
            }
            if ($role->name === 'Coach') {
                // Dashboard
                $role->givePermissionTo('dashboard');
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
