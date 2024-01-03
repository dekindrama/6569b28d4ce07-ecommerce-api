<?php

namespace App\Services\Auth\Entities;

class LoginEntity
{
    public string $email;
    public string $password;
    public string $device_name;
    public function __construct(
        string $email,
        string $password,
        string $device_name,
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->device_name = $device_name;

        return $this;
    }
}
