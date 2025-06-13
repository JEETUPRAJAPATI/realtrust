<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::table('users')->insert([
            [
                'name'      => 'Admin',
                'role_id'       => 1,
                'email'         => 'admin@gmail.com',
                'image'         => 'default.png',
                'password'      => bcrypt('123456'),
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s")
            ],
            [
                'name'          => 'staff',
                'role_id'       => 2,
                'email'         => 'staff@admin.com',
                'image'         => 'default.png',
                'password'      => bcrypt('123456'),
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s")
            ],
            [
                'name'          => 'User',
                'role_id'       => 3,
                'email'         => 'user@gmail.com',
                'image'         => 'default.png',
                'password'      => bcrypt('123456'),
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s")
            ]
        ]);
        $this->call(RoleSeeder::class);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
