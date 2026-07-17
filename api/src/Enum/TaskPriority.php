<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Task priority enumeration.
 *
 * Represents the urgency level of a task.
 * Values are ordered from lowest to highest priority.
 */
enum TaskPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';
}
