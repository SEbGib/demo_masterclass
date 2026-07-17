<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validator for the FutureDate constraint.
 *
 * Validates that a DateTime value is today or in the future.
 * Null values are considered valid (use NotNull constraint if required).
 * Comparison is done at date level (ignoring time).
 */
class FutureDateValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof FutureDate) {
            throw new UnexpectedTypeException($constraint, FutureDate::class);
        }

        // Null values are valid - use NotNull constraint if the field is required
        if (null === $value) {
            return;
        }

        if (!$value instanceof \DateTimeInterface) {
            throw new UnexpectedValueException($value, \DateTimeInterface::class);
        }

        // Compare dates only (ignore time component)
        // This allows "today at 23:59" to be valid even if current time is later
        $today = new \DateTimeImmutable('today', new \DateTimeZone('UTC'));
        $valueDate = \DateTimeImmutable::createFromInterface($value)->setTime(0, 0, 0);

        if ($valueDate < $today) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value->format('Y-m-d'))
                ->addViolation();
        }
    }
}
