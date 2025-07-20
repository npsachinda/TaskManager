<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\User;
use App\Models\TaskList;
use App\Repositories\ListRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $listRepository;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create();
        
        // Create the repository instance
        $this->listRepository = new ListRepository(new TaskList, new User);
    }

    public function test_can_get_all_lists_by_user()
    {
        // Arrange
        TaskList::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);
        
        // Act
        $lists = $this->listRepository->getAllByUser($this->user->id);
        
        // Assert
        $this->assertCount(3, $lists);
        $this->assertEquals($this->user->id, $lists->first()->user_id);
    }

    public function test_can_create_list()
    {
        // Arrange
        $listData = [
            'title' => 'Test List',
            'description' => 'Test Description',
            'user_id' => $this->user->id
        ];
        
        // Act
        $list = $this->listRepository->create($listData);
        
        // Assert
        $this->assertInstanceOf(TaskList::class, $list);
        $this->assertEquals('Test List', $list->title);
        $this->assertEquals($this->user->id, $list->user_id);
    }

    public function test_can_update_list()
    {
        // Arrange
        $list = TaskList::factory()->create([
            'user_id' => $this->user->id
        ]);
        
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description'
        ];
        
        // Act
        $updatedList = $this->listRepository->update($list->id, $updateData);
        
        // Assert
        $this->assertEquals('Updated Title', $updatedList->title);
        $this->assertEquals('Updated Description', $updatedList->description);
    }

    public function test_can_delete_list()
    {
        // Arrange
        $list = TaskList::factory()->create([
            'user_id' => $this->user->id
        ]);
        
        // Act
        $result = $this->listRepository->delete($list->id);
        
        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('lists', ['id' => $list->id]);
    }

    public function test_can_get_all_users()
    {
        // Arrange
        User::factory()->count(3)->create();
        
        // Act
        $users = $this->listRepository->getAllUsers();
        
        // Assert
        $this->assertCount(4, $users); // 3 + 1 from setUp
    }
} 