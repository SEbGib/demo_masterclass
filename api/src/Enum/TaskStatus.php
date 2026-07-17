<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Task status enumeration.
 *
 * Represents the lifecycle states of a task.
 */
enum TaskStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
}
