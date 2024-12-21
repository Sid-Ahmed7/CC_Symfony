<?php

declare(strict_types=1);

namespace App\Enum;

enum DifficultyEnum: string
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';
    case EXPERT = 'expert';
}