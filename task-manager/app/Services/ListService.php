<?php

namespace App\Services;

use App\Repositories\Interfaces\ListRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ListService
{
    protected $listRepository;
    protected $responseFormatter;

    public function __construct(
        ListRepositoryInterface $listRepository,
        ResponseFormatter $responseFormatter
    ) {
        $this->listRepository = $listRepository;
        $this->responseFormatter = $responseFormatter;
    }

    public function getListsData()
    {
        $lists = $this->listRepository->getAllByUser(Auth::id());
        $users = $this->listRepository->getAllUsers();

        return $this->responseFormatter->formatListResponse($lists, $users);
    }

    public function createList(array $data)
    {
        return $this->listRepository->create($data);
    }

    public function updateList($id, array $data)
    {
        return $this->listRepository->update($id, $data);
    }

    public function deleteList($id)
    {
        return $this->listRepository->delete($id);
    }
} 