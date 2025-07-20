<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\ListService;

class ListController extends Controller
{
    protected $listService;

    public function __construct(ListService $listService)
    {
        $this->listService = $listService;
    }

    public function index()
    {
        $data = $this->listService->getListsData();

        return Inertia::render('Lists/index', array_merge($data, [
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id'
        ]);

        $this->listService->createList($validated);
        return redirect()->route('lists.index')->with('success', 'List created successfully');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id'
        ]);

        $this->listService->updateList($id, $validated);
        return redirect()->route('lists.index')->with('success', 'List updated successfully');
    }

    public function destroy($id)
    {
        $this->listService->deleteList($id);
        return redirect()->route('lists.index')->with('success', 'List deleted successfully');
    }
}
