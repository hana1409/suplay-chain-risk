<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed admin role and admin account.
     * Uses firstOrCreate — will NEVER overwrite existing data.
     */
    public function run(): void
    {
        // Ensure roles exist
        Role::firstOrCreate(['id' => 1], ['role_name' => 'admin']);
        Role::firstOrCreate(['id' => 2], ['role_name' => 'user']);

        // Create admin account if it doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@handworld.com'],
            [
                'role_id'  => 1,
                'name'     => 'Administrator',
                'password' => Hash::make('admin123'),
            ]
        );

        if ($admin->wasRecentlyCreated) {
            $this->command->info('✅ Admin account created: admin@handworld.com / admin123');
        } else {
            $this->command->info('ℹ️  Admin account already exists: admin@handworld.com');
        }
    }
}
