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
            'email' => 'ekam51@gmail.com',
            'password' => Hash::make('admin'), 
            'role' => 'admin',
        ]);
        User::create([
            'email' => 'jurajpalenkas2003@gmail.com',
            'password' => Hash::make('admin'), 
            'role' => 'admin',
        ]);

        User::create([
            'email' => 'juraj.palenkas@student.ukf.sk',
            'password' => Hash::make('editor'),
            'role' => 'editor',
        ]);
        User::create([
            'email' => 'tomas.katzenbach@student.ukf.sk',
            'password' => Hash::make('editor'),
            'role' => 'editor',
        ]);

         User::create([
            'email' => 'jpalenkas2003@gmail.com',
            'password' => Hash::make('editor2'),
            'role' => 'editor',
        ]);

         User::create([
            'email' => 'jurajpalenkas10903@gmail.com',
            'password' => Hash::make('editor3'),
            'role' => 'editor',
        ]);
    }
}

