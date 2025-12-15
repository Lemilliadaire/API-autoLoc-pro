<?php

namespace Database\Seeders;


use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $user = User::create([
            'name' => 'NAKIE',
            'lastname' => 'K.jean',
            'telephone' => '+0022879808915',
            'email' => 'jean@example.com',
            'password' => Hash::make('jeannot123'),
            'password-confirm' => Hash::make('jeannot123'),
            'role' => 'admin',
        ]);

        Admin::create([
            'user_id' => $user->id,
        ]);
    }
}
