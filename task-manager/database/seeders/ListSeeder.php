<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaskList;
use App\Models\User;

class ListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $lists = [
            [
                'title' => 'Personal Tasks',
                'description' => 'Daily personal tasks and routines'
            ],
            [
                'title' => 'Work Projects',
                'description' => 'Professional tasks and project management'
            ],
            [
                'title' => 'Shopping List',
                'description' => 'Items to buy and shopping reminders'
            ],
            [
                'title' => 'Study Goals',
                'description' => 'Educational tasks and learning objectives'
            ],
            [
                'title' => 'Home Maintenance',
                'description' => 'House chores and maintenance tasks'
            ],
        ];

        foreach ($users as $user) {
            foreach ($lists as $list) {
                TaskList::create([
                    'title' => $list['title'],
                    'description' => $list['description'],
                    'user_id' => $user->id
                ]);
            }
        }

        $this->command->info('Lists seeded successfully!');
    }
}
