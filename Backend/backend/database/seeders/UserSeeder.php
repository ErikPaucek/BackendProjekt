<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'), // zmeň heslo podľa potreby
            'role' => 'admin',
        ]);

        User::create([
            'email' => 'editor@example.com',
            'password' => Hash::make('editor'),
            'role' => 'editor',
        ]);

         User::create([
            'email' => 'editor2@example.com',
            'password' => Hash::make('editor'),
            'role' => 'editor',
        ]);

         User::create([
            'email' => 'editor3@example.com',
            'password' => Hash::make('editor'),
            'role' => 'editor',
        ]);
    }
}

