<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskList;
use App\Models\Task;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lists = TaskList::all();

        if ($lists->isEmpty()) {
            $this->command->info('No lists found. Please run ListSeeder first.');
            return;
        }

        $taskTemplates = [
            'Personal Tasks' => [
                [
                    'title' => 'Morning Exercise',
                    'description' => '30 minutes of cardio workout',
                    'status' => 'pending'
                ],
                [
                    'title' => 'Read a Book',
                    'description' => 'Read for 1 hour',
                    'status' => 'completed'
                ],
                [
                    'title' => 'Meditation',
                    'description' => '15 minutes mindfulness practice',
                    'status' => 'pending'
                ]
            ],
            'Work Projects' => [
                [
                    'title' => 'Client Meeting',
                    'description' => 'Discuss project requirements',
                    'status' => 'pending'
                ],
                [
                    'title' => 'Project Documentation',
                    'description' => 'Update technical documentation',
                    'status' => 'completed'
                ],
                [
                    'title' => 'Code Review',
                    'description' => 'Review team pull requests',
                    'status' => 'pending'
                ]
            ],
            'Shopping List' => [
                [
                    'title' => 'Grocery Shopping',
                    'description' => 'Buy weekly groceries',
                    'status' => 'pending'
                ],
                [
                    'title' => 'Buy Office Supplies',
                    'description' => 'Restock office essentials',
                    'status' => 'completed'
                ],
                [
                    'title' => 'Gift Shopping',
                    'description' => 'Buy birthday gifts',
                    'status' => 'pending'
                ]
            ],
            'Study Goals' => [
                [
                    'title' => 'Complete Online Course',
                    'description' => 'Finish Laravel course modules',
                    'status' => 'pending'
                ],
                [
                    'title' => 'Practice Coding',
                    'description' => 'Solve programming challenges',
                    'status' => 'completed'
                ],
                [
                    'title' => 'Watch Tutorial',
                    'description' => 'Study new framework features',
                    'status' => 'pending'
                ]
            ],
            'Home Maintenance' => [
                [
                    'title' => 'Clean House',
                    'description' => 'Weekly house cleaning',
                    'status' => 'pending'
                ],
                [
                    'title' => 'Garden Work',
                    'description' => 'Water plants and trim bushes',
                    'status' => 'completed'
                ],
                [
                    'title' => 'Fix Leaky Faucet',
                    'description' => 'Repair bathroom faucet',
                    'status' => 'pending'
                ]
            ]
        ];

        foreach ($lists as $list) {
            $tasks = $taskTemplates[$list->title] ?? [];
            
            foreach ($tasks as $task) {
                $dueDate = Carbon::now()->addDays(rand(1, 30));
                
                Task::create([
                    'title' => $task['title'],
                    'description' => $task['description'],
                    'status' => $task['status'],
                    'due_date' => $dueDate,
                    'list_id' => $list->id
                ]);
            }
        }

        $this->command->info('Tasks seeded successfully!');
    }
}
