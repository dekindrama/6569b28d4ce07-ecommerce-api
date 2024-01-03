<?php

namespace App\Domains\Auth;

use App\Domains\Auth\Entities\RegisterUserEntity;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface AuthRepositoryInterface {
    public function getUser(string $email) : User;
    public function getListUsers() : Collection;
    public function registerUser(RegisterUserEntity $request): User;
}
