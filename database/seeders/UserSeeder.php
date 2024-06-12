<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'janedoe',
            'password' => Hash::make('password'),
            'token' => 'janedoe_token'
        ]);

        User::create([
            'username' => 'marryjane',
            'password' => Hash::make('password'),
            'token' => 'marryjane_token'
        ]);

    }
}
