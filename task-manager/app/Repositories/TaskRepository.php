<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\QueryBuilders\TaskQueryBuilder;
use Illuminate\Support\Facades\Auth;

class TaskRepository implements TaskRepositoryInterface
{
    protected $task;
    protected $taskList;
    protected $user;
    protected $queryBuilder;

    public function __construct(Task $task, TaskList $taskList, User $user, TaskQueryBuilder $queryBuilder)
    {
        $this->task = $task;
        $this->taskList = $taskList;
        $this->user = $user;
        $this->queryBuilder = $queryBuilder;
    }

    public function getAllWithPagination($search = null, $filter = 'all', $user_filter = 'all')
    {
        $query = $this->queryBuilder
            ->withRelations()
            ->forAuthUser()
            ->withSearch($search)
            ->withStatusFilter($filter)
            ->withUserFilter($user_filter)
            ->orderByLatest()
            ->getQuery();

        return $query->paginate(10);
    }

    public function getById($id)
    {
        return $this->task->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->task->create($data);
    }

    public function update($id, array $data)
    {
        $task = $this->getById($id);
        $task->update($data);
        return $task;
    }

    public function delete($id)
    {
        return $this->getById($id)->delete();
    }

    public function getListsByUser($userId)
    {
        return $this->taskList->where('user_id', $userId)->get();
    }

    public function getAllUsers()
    {
        return $this->user->select('id', 'name')->get();
    }
} 