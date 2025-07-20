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

    public function __construct(Task $task, TaskList $taskList, User $user)
    {
        $this->task = $task;
        $this->taskList = $taskList;
        $this->user = $user;
        $this->queryBuilder = new TaskQueryBuilder($task);
    }

    public function getAllWithPagination($search = null, $filter = 'all', $user_filter = 'all')
    {
        $query = $this->task->newQuery();

        // Add relations
        $query->with(['list.user']);

        // Filter by authenticated user's lists
        $query->whereHas('list', function ($q) {
            $q->where('user_id', Auth::id());
        });

        // Add search condition
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Add status filter
        if ($filter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($filter === 'completed') {
            $query->where('status', 'completed');
        }

        // Add user filter
        if ($user_filter !== 'all') {
            $query->whereHas('list', function ($q) use ($user_filter) {
                $q->where('user_id', $user_filter);
            });
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

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