<?php

declare(strict_types=1);

namespace App\Enum;

enum GenderEnum: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';
}