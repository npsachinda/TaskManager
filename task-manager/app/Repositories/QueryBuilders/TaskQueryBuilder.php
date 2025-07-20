<?php

namespace App\Repositories\QueryBuilders;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TaskQueryBuilder
{
    protected $query;

    public function __construct(Task $task)
    {
        $this->query = $task->newQuery();
    }

    public function withRelations(): self
    {
        $this->query->with(['list.user']);
        return $this;
    }

    public function forAuthUser(): self
    {
        $this->query->whereHas('list', function ($query) {
            $query->where('user_id', Auth::id());
        });
        return $this;
    }

    public function withSearch(?string $search): self
    {
        if ($search) {
            $this->query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        return $this;
    }

    public function withStatusFilter(string $filter): self
    {
        if ($filter !== 'all') {
            $this->query->where('status', $filter);
        }
        return $this;
    }

    public function withUserFilter(string $userFilter): self
    {
        if ($userFilter !== 'all') {
            $this->query->whereHas('list', function ($query) use ($userFilter) {
                $query->where('user_id', $userFilter);
            });
        }
        return $this;
    }

    public function orderByLatest(): self
    {
        $this->query->orderBy('created_at', 'desc');
        return $this;
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }
} 