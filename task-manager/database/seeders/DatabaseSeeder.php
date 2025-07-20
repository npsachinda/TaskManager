<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users first
        \App\Models\User::factory(5)->create();

        // Then create lists for each user
        $this->call(ListSeeder::class);

        // Finally create tasks for each list
        $this->call(TaskSeeder::class);
    }
}
