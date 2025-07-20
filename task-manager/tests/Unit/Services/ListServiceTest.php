<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ListService;
use App\Services\ResponseFormatter;
use App\Repositories\Interfaces\ListRepositoryInterface;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;

class ListServiceTest extends TestCase
{
    protected $listRepository;
    protected $responseFormatter;
    protected $listService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->listRepository = Mockery::mock(ListRepositoryInterface::class);
        $this->responseFormatter = new ResponseFormatter();
        $this->listService = new ListService($this->listRepository, $this->responseFormatter);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_lists_data_returns_formatted_response()
    {
        // Arrange
        $lists = Collection::make([
            ['id' => 1, 'title' => 'Test List 1'],
            ['id' => 2, 'title' => 'Test List 2']
        ]);
        $users = Collection::make([
            ['id' => 1, 'name' => 'Test User 1'],
            ['id' => 2, 'name' => 'Test User 2']
        ]);

        $this->listRepository->shouldReceive('getAllByUser')
            ->once()
            ->andReturn($lists);

        $this->listRepository->shouldReceive('getAllUsers')
            ->once()
            ->andReturn($users);

        // Act
        $result = $this->listService->getListsData();

        // Assert
        $this->assertArrayHasKey('lists', $result);
        $this->assertArrayHasKey('users', $result);
        $this->assertEquals($lists, $result['lists']);
        $this->assertEquals($users, $result['users']);
    }

    public function test_create_list_delegates_to_repository()
    {
        // Arrange
        $listData = [
            'title' => 'New List',
            'description' => 'Test Description',
            'user_id' => 1
        ];

        $this->listRepository->shouldReceive('create')
            ->once()
            ->with($listData)
            ->andReturn((object)$listData);

        // Act
        $result = $this->listService->createList($listData);

        // Assert
        $this->assertEquals('New List', $result->title);
        $this->assertEquals(1, $result->user_id);
    }

    public function test_update_list_delegates_to_repository()
    {
        // Arrange
        $listId = 1;
        $listData = [
            'title' => 'Updated List',
            'description' => 'Updated Description'
        ];

        $this->listRepository->shouldReceive('update')
            ->once()
            ->with($listId, $listData)
            ->andReturn((object)$listData);

        // Act
        $result = $this->listService->updateList($listId, $listData);

        // Assert
        $this->assertEquals('Updated List', $result->title);
        $this->assertEquals('Updated Description', $result->description);
    }

    public function test_delete_list_delegates_to_repository()
    {
        // Arrange
        $listId = 1;

        $this->listRepository->shouldReceive('delete')
            ->once()
            ->with($listId)
            ->andReturn(true);

        // Act
        $result = $this->listService->deleteList($listId);

        // Assert
        $this->assertTrue($result);
    }
} 