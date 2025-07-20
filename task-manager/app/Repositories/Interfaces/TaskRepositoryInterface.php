<?php

namespace App\Repositories\Interfaces;

interface TaskRepositoryInterface
{
    public function getAllWithPagination($search = null, $filter = 'all', $user_filter = 'all');
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getListsByUser($userId);
    public function getAllUsers();
} 