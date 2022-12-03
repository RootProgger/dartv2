<?php

declare(strict_types=1);

namespace App\Enum;

enum UserRole: string
{
    case SUPER_ADMIN = 'ROLE_SUPERADMIN';
    case ADMIN = 'ROLE_ADMIN';
    case USER = 'ROLE_USER';

    public function label(): string
    {
        return match ($this) {
            static::SUPER_ADMIN => 'ROLE_SUPERADMIN',
            static::ADMIN => 'ROLE_ADMIN',
            static::USER => 'ROLE_USER',
        };
    }
}
