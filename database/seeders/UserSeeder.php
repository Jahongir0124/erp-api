<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $user = User::create([
            'name' => 'admin',
            'email' => 'example@gmail.com',
            'password' => 'hacker'
        ]);
        $user->assignRole('super-admin');
    }
}
