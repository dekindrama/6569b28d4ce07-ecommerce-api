<?php

namespace App\Enums;

class UserRoleEnum {
    const SUPER_ADMIN = 'SUPER_ADMIN';
    const ADMIN = 'ADMIN';

    const ROLES = [
        self::SUPER_ADMIN,
        self::ADMIN,
    ];
}
