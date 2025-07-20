<?php

namespace App\Repositories\Interfaces;

interface ListRepositoryInterface
{
    public function getAllByUser($userId);
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getAllUsers();
}