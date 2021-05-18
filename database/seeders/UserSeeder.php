<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate([
            'name' => 'Admin',
            'user_name' => 'admin',
            'email' => 'admin@sample.com',
            'user_role' => 'admin',
            'email_verified_at' => Carbon::now(),
            'registered_at' => Carbon::now(),
            'password' => Hash::make('password'),
        ]);
    }
}
