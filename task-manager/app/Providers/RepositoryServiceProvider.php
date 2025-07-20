<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\Interfaces\ListRepositoryInterface;
use App\Repositories\TaskRepository;
use App\Repositories\ListRepository;
use App\Services\TaskService;
use App\Services\ListService;
use App\Services\ResponseFormatter;
use App\Repositories\QueryBuilders\TaskQueryBuilder;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(ListRepositoryInterface::class, ListRepository::class);

        // Register QueryBuilder as singleton
        $this->app->singleton(TaskQueryBuilder::class, function ($app) {
            return new TaskQueryBuilder($app->make('App\Models\Task'));
        });

        // Register ResponseFormatter as singleton
        $this->app->singleton(ResponseFormatter::class);

        // Register services with their dependencies
        $this->app->when(TaskService::class)
            ->needs(TaskRepositoryInterface::class)
            ->give(function ($app) {
                return $app->make(TaskRepository::class);
            });

        $this->app->when(ListService::class)
            ->needs(ListRepositoryInterface::class)
            ->give(function ($app) {
                return $app->make(ListRepository::class);
            });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 