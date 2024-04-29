<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
            ],
            [
                'name' => 'Admin',
            ],
            [
                'name' => 'Employee',
            ]
        ];

        Role::insert($roles);

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'test@gmail.com',
                'password' => bcrypt('adminpass'),
                'role_id' => 1,
                'status' => 'active'
            ]
        ];

        User::insert($users);

        $leave_types = [
            [
                'name' => 'Casual Leave',
            ],
            [
                'name' => 'Sick Leave',
            ],
            [
                'name' => 'Emergency Leave',
            ]
        ];

        LeaveType::insert($leave_types);

        $this->command->info('Data inserted successfully');
    }
}
