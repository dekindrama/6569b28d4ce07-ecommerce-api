<?php

namespace App\Domains\Auth;

use App\Domains\Auth\Entities\RegisterUserEntity;
use App\Exceptions\Commons\NotFoundException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class AuthRepository implements AuthRepositoryInterface
{
    private $_userModel;
    public function __construct(User $userModel) {
        $this->_userModel = $userModel;
    }

    public function getUser(string $email): User
    {
        $user = $this->_userModel->where('email', $email)->first();
        if (!$user) {
            throw new NotFoundException('user not found');
        }
        return $user;
    }

    public function getListUsers(): Collection
    {
        $users = $this->_userModel->select('id', 'name', 'email', 'created_at', 'updated_at')->get();
        return $users;
    }

    public function registerUser(RegisterUserEntity $request): User
    {
        $registeredUser = $this->_userModel->create([
            'id' => Str::orderedUuid(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'email_verified_at' => now(),
            'role' => $request->role,
        ]);

        return $registeredUser;
    }
}
