<?php

namespace App\Repositories;

use App\Models\TaskList;
use App\Models\User;
use App\Repositories\Interfaces\ListRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ListRepository implements ListRepositoryInterface
{
    protected $taskList;
    protected $user;

    public function __construct(TaskList $taskList, User $user)
    {
        $this->taskList = $taskList;
        $this->user = $user;
    }

    public function getAllByUser($userId)
    {
        return $this->taskList->where('user_id', $userId)->get();
    }

    public function getById($id)
    {
        return $this->taskList->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->taskList->create($data);
    }

    public function update($id, array $data)
    {
        $list = $this->getById($id);
        $list->update($data);
        return $list;
    }

    public function delete($id)
    {
        return $this->getById($id)->delete();
    }

    public function getAllUsers()
    {
        return $this->user->select('id', 'name')->get();
    }
} 