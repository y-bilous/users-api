<?php

namespace App\Enum;

enum UserRole: string
{
    case ROOT = 'ROLE_ROOT';
    case USER = 'ROLE_USER';
}
