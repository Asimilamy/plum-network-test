<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case StandardUser = 'standard_user';

    /**
     * Get the human readable name for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super admin',
            self::StandardUser => 'Standard user',
        };
    }
}
