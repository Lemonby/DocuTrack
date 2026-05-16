<?php

namespace App\Services;

use App\Models\LoginModel;
use App\Models\SuperAdminModel;
use Exception;

class AuthService
{
    private $loginModel;
    private $userModel;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->loginModel = new LoginModel($this->db);
        $this->userModel = new SuperAdminModel($this->db);
    }

    public function login($email, $password)
    {
        $user = $this->loginModel->getUserByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Email tidak terdaftar.'];
        }

        if (password_verify($password, $user['password'])) {
            unset($user['password']);
            return ['success' => true, 'user' => $user];
        } else {
            return ['success' => false, 'message' => 'Password salah.'];
        }
    }

    public function getUserProfile($userId)
    {
        try {
            return $this->userModel->getUserById($userId);
        } catch (Exception $e) {
            error_log("AuthService Error fetching user profile for ID {$userId}: " . $e->getMessage());
            return null;
        }
    }

    public function updateUserProfile(int $userId, array $data): bool
    {
        $updateData = [];
        if (isset($data['nama'])) {
            $updateData['nama'] = $data['nama'];
        }
        if (isset($data['email'])) {
            $updateData['email'] = $data['email'];
        }

        if (empty($updateData)) {
            return true;
        }

        return $this->userModel->updateUser($userId, $updateData);
    }

    public function changePassword(int $userId, string $newPassword): bool
    {
        if (strlen($newPassword) < 8) {
            throw new Exception("Password terlalu pendek, minimal 8 karakter.");
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        return $this->userModel->updateUser($userId, ['password' => $hashedPassword]);
    }
}
