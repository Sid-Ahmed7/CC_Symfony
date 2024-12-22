<?php

declare(strict_types=1);

namespace App\Enum;

enum SessionStatusEnum: string
{
    case INPROGRESS = 'inProgress';
    case COMPLETED = 'completed';
    case SHORTLY = 'shortly';
    case CANCELLED = 'cancelled';
}