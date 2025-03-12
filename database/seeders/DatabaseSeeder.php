<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create(
            [
            'name' => 'Zidni',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
        ]
    );
        User::factory()->create(
            [
            'name' => 'Zidni2',
            'email' => 'admin2@gmail.com',
            'password' => bcrypt('admin123'),
        ]
    );
        User::factory()->create(
            [
            'name' => 'Zidni3',
            'email' => 'admin3@gmail.com',
            'password' => bcrypt('admin123'),
        ]
    );
    }
}
