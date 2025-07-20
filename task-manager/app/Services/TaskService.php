<?php

namespace App\Services;

use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    protected $taskRepository;
    protected $responseFormatter;

    public function __construct(
        TaskRepositoryInterface $taskRepository,
        ResponseFormatter $responseFormatter
    ) {
        $this->taskRepository = $taskRepository;
        $this->responseFormatter = $responseFormatter;
    }

    public function getTasksData($search = null, $filter = 'all', $user_filter = 'all')
    {
        $tasks = $this->taskRepository->getAllWithPagination($search, $filter, $user_filter);
        $lists = $this->taskRepository->getListsByUser(Auth::id());
        $users = $this->taskRepository->getAllUsers();

        return $this->responseFormatter->formatTaskResponse($tasks, $lists, $users, [
            'search' => $search,
            'filter' => $filter,
            'user_filter' => $user_filter
        ]);
    }

    public function createTask(array $data)
    {
        return $this->taskRepository->create($data);
    }

    public function updateTask($id, array $data)
    {
        return $this->taskRepository->update($id, $data);
    }

    public function deleteTask($id)
    {
        return $this->taskRepository->delete($id);
    }
} 