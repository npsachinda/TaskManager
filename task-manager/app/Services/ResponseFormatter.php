<?php

namespace App\Services;

class ResponseFormatter
{
    public function formatTaskResponse($tasks, $lists, $users, $filters)
    {
        return [
            'tasks' => $tasks,
            'lists' => $lists,
            'users' => $users,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'filter' => $filters['filter'] ?? 'all',
                'user_filter' => $filters['user_filter'] ?? 'all'
            ]
        ];
    }

    public function formatListResponse($lists, $users)
    {
        return [
            'lists' => $lists,
            'users' => $users
        ];
    }
} 