<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\TaskList;
use App\Repositories\TaskRepository;
use App\Repositories\QueryBuilders\TaskQueryBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $taskRepository;
    protected $user;
    protected $list;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user and list
        $this->user = User::factory()->create();
        $this->list = TaskList::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Login the user
        Auth::login($this->user);
        
        // Create the repository instance
        $this->taskRepository = new TaskRepository(
            new Task,
            new TaskList,
            new User
        );
    }

    public function test_can_get_tasks_with_pagination()
    {
        // Arrange
        Task::factory()->count(5)->create([
            'list_id' => $this->list->id
        ]);
        
        // Act
        $tasks = $this->taskRepository->getAllWithPagination();
        
        // Assert
        $this->assertEquals(5, $tasks->total());
        $this->assertNotNull($tasks->first()->list);
    }

    public function test_can_filter_tasks_by_status()
    {
        // Enable query logging
        DB::enableQueryLog();
        
        // Arrange
        Task::factory()->count(3)->pending()->create([
            'list_id' => $this->list->id
        ]);
        
        Task::factory()->count(2)->completed()->create([
            'list_id' => $this->list->id
        ]);
        
        // Verify tasks were created correctly
        $this->assertEquals(3, Task::pending()->count(), 'Should have 3 pending tasks');
        $this->assertEquals(2, Task::completed()->count(), 'Should have 2 completed tasks');
        
        // Log all tasks in database
        Log::info('All tasks in database:', [
            'tasks' => Task::with('list')->get()->toArray(),
            'total' => Task::count()
        ]);
        
        // Act
        $pendingResults = $this->taskRepository->getAllWithPagination(null, 'pending');
        $completedResults = $this->taskRepository->getAllWithPagination(null, 'completed');
        
        // Log queries that were executed
        Log::info('Database queries:', DB::getQueryLog());
        
        // Log the results
        Log::info('Pending tasks query result:', [
            'total' => $pendingResults->total(),
            'items' => $pendingResults->items()
        ]);
        Log::info('Completed tasks query result:', [
            'total' => $completedResults->total(),
            'items' => $completedResults->items()
        ]);
        
        // Assert
        $this->assertEquals(3, $pendingResults->total(), 'Expected 3 pending tasks');
        $this->assertEquals(2, $completedResults->total(), 'Expected 2 completed tasks');
        
        // Additional assertions to verify task status
        foreach ($pendingResults as $task) {
            $this->assertEquals('pending', $task->status, 'Task should be pending');
        }
        
        foreach ($completedResults as $task) {
            $this->assertEquals('completed', $task->status, 'Task should be completed');
        }
    }

    public function test_can_search_tasks()
    {
        // Arrange
        Task::factory()->create([
            'list_id' => $this->list->id,
            'title' => 'Search Test Task',
            'description' => 'This is a test task'
        ]);
        
        // Act
        $searchResults = $this->taskRepository->getAllWithPagination('Search Test');
        
        // Assert
        $this->assertEquals(1, $searchResults->total());
        $this->assertEquals('Search Test Task', $searchResults->first()->title);
    }

    public function test_can_create_task()
    {
        // Arrange
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'list_id' => $this->list->id,
            'due_date' => now()->addDays(1)
        ];
        
        // Act
        $task = $this->taskRepository->create($taskData);
        
        // Assert
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals($this->list->id, $task->list_id);
    }

    public function test_can_update_task()
    {
        // Arrange
        $task = Task::factory()->create([
            'list_id' => $this->list->id
        ]);
        
        $updateData = [
            'title' => 'Updated Task',
            'status' => 'completed'
        ];
        
        // Act
        $updatedTask = $this->taskRepository->update($task->id, $updateData);
        
        // Assert
        $this->assertEquals('Updated Task', $updatedTask->title);
        $this->assertEquals('completed', $updatedTask->status);
    }

    public function test_can_delete_task()
    {
        // Arrange
        $task = Task::factory()->create([
            'list_id' => $this->list->id
        ]);
        
        // Act
        $result = $this->taskRepository->delete($task->id);
        
        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_can_get_lists_by_user()
    {
        // Arrange
        TaskList::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);
        
        // Act
        $lists = $this->taskRepository->getListsByUser($this->user->id);
        
        // Assert
        $this->assertCount(4, $lists); // 3 + 1 from setUp
    }
} 