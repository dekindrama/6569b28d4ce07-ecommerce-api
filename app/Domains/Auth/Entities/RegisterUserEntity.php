<?php

namespace App\Domains\Auth\Entities;

use App\Enums\UserRoleEnum;

class RegisterUserEntity
{
    public string $name;
    public string $email;
    public string $password;
    public string $role;
    public function __construct(object $validatedRequest) {
        $this->name = $validatedRequest->name;
        $this->email = $validatedRequest->email;
        $this->password = $validatedRequest->password;
        $this->role = $validatedRequest->role;

        return $this;
    }
}
