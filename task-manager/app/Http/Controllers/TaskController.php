<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\TaskService;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index()
    {
        $data = $this->taskService->getTasksData(
            request('search'),
            request('filter', 'all'),
            request('user_filter', 'all')
        );

        return Inertia::render('Tasks/index', array_merge($data, [
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ]
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'list_id' => 'required|integer|exists:lists,id',
            'status' => 'required|string|in:pending,completed'
        ]);

        $this->taskService->createTask($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'list_id' => 'required|integer|exists:lists,id',
            'status' => 'required|string|in:pending,completed'
        ]);

        $this->taskService->updateTask($id, $validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }

    public function destroy($id)
    {
        $this->taskService->deleteTask($id);
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
    }
}
