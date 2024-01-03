<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Auth\Entities\LoginEntity;
use Illuminate\Database\Eloquent\Collection;

interface AuthServiceInterface {
    public function login(LoginEntity $params) : string;
    public function logout() : void;
    public function getLoggedUser() : User;
    public function getListUsers(User $loggedUser) : Collection;
    public function register(User $loggedUser, object $validatedRequest) : User;
}
