<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@telko.local'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('AdminSecret123!'),
                'is_admin' => true,
            ]
        );
    }
}
