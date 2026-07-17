<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Validator\FutureDate;
use App\Validator\FutureDateValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * Unit tests for FutureDateValidator.
 *
 * Tests cover all edge cases from Issue #2:
 * - null is valid (no due date set)
 * - Today at any time is valid
 * - Future dates are valid
 * - Yesterday at any time is invalid
 * - Past dates are invalid
 */
class FutureDateValidatorTest extends TestCase
{
    private FutureDateValidator $validator;
    private ExecutionContextInterface $context;
    private ConstraintViolationBuilderInterface $violationBuilder;

    protected function setUp(): void
    {
        $this->validator = new FutureDateValidator();

        $this->violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->violationBuilder->method('setParameter')->willReturnSelf();
        $this->violationBuilder->method('addViolation')->willReturnSelf();

        $this->context = $this->createMock(ExecutionContextInterface::class);
    }

    // =============================================
    // VALID CASES (no violation expected)
    // =============================================

    public function testNullIsValid(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->initialize($this->context);
        $this->validator->validate(null, new FutureDate());
    }

    public function testTodayIsValid(): void
    {
        $today = new \DateTimeImmutable('today');

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->initialize($this->context);
        $this->validator->validate($today, new FutureDate());
    }

    public function testTodayAt2359IsValid(): void
    {
        $todayLate = new \DateTimeImmutable('today 23:59:59');

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->initialize($this->context);
        $this->validator->validate($todayLate, new FutureDate());
    }

    public function testTomorrowIsValid(): void
    {
        $tomorrow = new \DateTimeImmutable('tomorrow');

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->initialize($this->context);
        $this->validator->validate($tomorrow, new FutureDate());
    }

    public function testNextWeekIsValid(): void
    {
        $nextWeek = new \DateTimeImmutable('+7 days');

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->initialize($this->context);
        $this->validator->validate($nextWeek, new FutureDate());
    }

    public function testNextYearIsValid(): void
    {
        $nextYear = new \DateTimeImmutable('+1 year');

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->initialize($this->context);
        $this->validator->validate($nextYear, new FutureDate());
    }

    // =============================================
    // INVALID CASES (violation expected)
    // =============================================

    public function testYesterdayIsInvalid(): void
    {
        $yesterday = new \DateTimeImmutable('yesterday');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->willReturn($this->violationBuilder);

        $this->validator->initialize($this->context);
        $this->validator->validate($yesterday, new FutureDate());
    }

    public function testYesterdayAt2359IsInvalid(): void
    {
        $yesterdayLate = new \DateTimeImmutable('yesterday 23:59:59');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->willReturn($this->violationBuilder);

        $this->validator->initialize($this->context);
        $this->validator->validate($yesterdayLate, new FutureDate());
    }

    public function testLastWeekIsInvalid(): void
    {
        $lastWeek = new \DateTimeImmutable('-7 days');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->willReturn($this->violationBuilder);

        $this->validator->initialize($this->context);
        $this->validator->validate($lastWeek, new FutureDate());
    }

    public function testLastYearIsInvalid(): void
    {
        $lastYear = new \DateTimeImmutable('-1 year');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->willReturn($this->violationBuilder);

        $this->validator->initialize($this->context);
        $this->validator->validate($lastYear, new FutureDate());
    }

    // =============================================
    // EDGE CASES
    // =============================================

    public function testDateTimeIsAlsoAccepted(): void
    {
        $tomorrow = new \DateTime('tomorrow');

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->initialize($this->context);
        $this->validator->validate($tomorrow, new FutureDate());
    }

    public function testConstraintHasDefaultMessage(): void
    {
        $constraint = new FutureDate();

        $this->assertStringContainsString('must be today or in the future', $constraint->message);
    }
}
