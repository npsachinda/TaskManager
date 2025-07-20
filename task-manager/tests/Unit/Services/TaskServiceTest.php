<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\TaskService;
use App\Services\ResponseFormatter;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;

class TaskServiceTest extends TestCase
{
    protected $taskRepository;
    protected $responseFormatter;
    protected $taskService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskRepository = Mockery::mock(TaskRepositoryInterface::class);
        $this->responseFormatter = new ResponseFormatter();
        $this->taskService = new TaskService($this->taskRepository, $this->responseFormatter);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_tasks_data_returns_formatted_response()
    {
        // Arrange
        $paginatedTasks = new LengthAwarePaginator(
            Collection::make([['id' => 1, 'title' => 'Test Task']]),
            1,
            10,
            1
        );
        $lists = Collection::make([['id' => 1, 'title' => 'Test List']]);
        $users = Collection::make([['id' => 1, 'name' => 'Test User']]);

        $this->taskRepository->shouldReceive('getAllWithPagination')
            ->once()
            ->with('search term', 'pending', 'user1')
            ->andReturn($paginatedTasks);

        $this->taskRepository->shouldReceive('getListsByUser')
            ->once()
            ->andReturn($lists);

        $this->taskRepository->shouldReceive('getAllUsers')
            ->once()
            ->andReturn($users);

        // Act
        $result = $this->taskService->getTasksData('search term', 'pending', 'user1');

        // Assert
        $this->assertArrayHasKey('tasks', $result);
        $this->assertArrayHasKey('lists', $result);
        $this->assertArrayHasKey('users', $result);
        $this->assertArrayHasKey('filters', $result);
        $this->assertEquals($paginatedTasks, $result['tasks']);
        $this->assertEquals($lists, $result['lists']);
        $this->assertEquals($users, $result['users']);
    }

    public function test_create_task_delegates_to_repository()
    {
        // Arrange
        $taskData = [
            'title' => 'New Task',
            'description' => 'Test Description'
        ];

        $this->taskRepository->shouldReceive('create')
            ->once()
            ->with($taskData)
            ->andReturn((object)$taskData);

        // Act
        $result = $this->taskService->createTask($taskData);

        // Assert
        $this->assertEquals('New Task', $result->title);
    }

    public function test_update_task_delegates_to_repository()
    {
        // Arrange
        $taskId = 1;
        $taskData = [
            'title' => 'Updated Task'
        ];

        $this->taskRepository->shouldReceive('update')
            ->once()
            ->with($taskId, $taskData)
            ->andReturn((object)$taskData);

        // Act
        $result = $this->taskService->updateTask($taskId, $taskData);

        // Assert
        $this->assertEquals('Updated Task', $result->title);
    }

    public function test_delete_task_delegates_to_repository()
    {
        // Arrange
        $taskId = 1;

        $this->taskRepository->shouldReceive('delete')
            ->once()
            ->with($taskId)
            ->andReturn(true);

        // Act
        $result = $this->taskService->deleteTask($taskId);

        // Assert
        $this->assertTrue($result);
    }
} 