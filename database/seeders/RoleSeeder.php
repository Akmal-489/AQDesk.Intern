<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['role_name' => 'student', 'permissions' => 'basic', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'instructor', 'permissions' => 'create_events,create_podcasts', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'researcher', 'permissions' => 'basic', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'professional', 'permissions' => 'post_jobs', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'policy_expert', 'permissions' => 'consulting', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'admin', 'permissions' => 'all', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
