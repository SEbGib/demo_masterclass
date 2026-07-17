<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint to validate that a date is today or in the future.
 *
 * Use this constraint on DateTime/DateTimeImmutable fields that should
 * not accept past dates (e.g., due dates, appointment dates).
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class FutureDate extends Constraint
{
    public string $message = 'The date "{{ value }}" must be today or in the future.';
}
