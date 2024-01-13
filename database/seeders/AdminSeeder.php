<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'email' => 'superadmin@admin.com',
            'password' => bcrypt('123456789s@'),
            'role' => 'super_admin',
        ]);

        DB::table('users')->insert([
            'email' => 'admin@admin.com',
            'password' => bcrypt('123456789s@'),
            'role' => 'admin',
        ]);

        DB::table('users')->insert([
            'email' => 'provider@admin.com',
            'password' => bcrypt('123456789s@'),
            'role' => 'provider',
        ]);

        DB::table('users')->insert([
            'email' => 'patient@patient.com',
            'password' => bcrypt('123456789s@'),
            'role' => 'patient',
        ]);
    }
}
