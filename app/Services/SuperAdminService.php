<?php

namespace App\Services;

use App\Models\SuperAdminModel;
use Exception;

class SuperAdminService
{
    private $model;

    public function __construct($db)
    {
        $this->model = new SuperAdminModel($db);
    }

    public function getAllUsers()
    {
        return $this->model->getAllUsers();
    }

    public function getAllRoles()
    {
        return $this->model->getAllRoles();
    }

    public function getListJurusan()
    {
        return $this->model->getListJurusan();
    }

    public function getUserById(int $id)
    {
        return $this->model->getUserById($id);
    }

    public function deleteUser(int $id): bool
    {
        return $this->model->deleteUser($id);
    }

    /**
     * Create a new user. Handles password hashing.
     *
     * @param array $data Validated user data.
     * @return bool
     */
    public function createUser(array $data): bool
    {
        // Hash password before sending to model
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->model->createUser($data);
    }

    /**
     * Update an existing user. Handles password hashing.
     *
     * @param int $id User ID.
     * @param array $data Validated data to update.
     * @return bool
     */
    public function updateUser(int $id, array $data): bool
    {
        // Hash password only if it is being changed
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // Don't update password if it's empty
            unset($data['password']);
        }
        return $this->model->updateUser($id, $data);
    }
}
