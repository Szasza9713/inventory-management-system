<?php

declare(strict_types=1);

namespace App\Util;

use App\Exception\Assert\EmptyValueException;
use App\Exception\Assert\InvalidRangeException;
use App\Exception\Assert\ValueTooHighException;
use App\Exception\Assert\ValueTooLowException;

final class Assert
{
    public static function notEmpty(string $value, string $message): void
    {
        if (trim($value) === '') {
            throw new EmptyValueException($message);
        }
    }

    public static function nonNegativeInt(int $value, string $message): void
    {
        if ($value < 0) {
            throw new ValueTooLowException($message);
        }
    }

    public static function positiveInt(int $value, string $message): void
    {
        if ($value <= 0) {
            throw new ValueTooLowException($message);
        }
    }

    public static function nonNegativeFloat(float $value, string $message): void
    {
        if ($value < 0) {
            throw new ValueTooLowException($message);
        }
    }

    public static function inRange(int $value, int $min, int $max, string $message): void
    {
        if ($value < $min || $value > $max) {
            throw new InvalidRangeException($message);
        }
    }

    public static function maxInt(int $value, int $max, string $message): void
    {
        if ($value > $max) {
            throw new ValueTooHighException($message);
        }
    }
}
