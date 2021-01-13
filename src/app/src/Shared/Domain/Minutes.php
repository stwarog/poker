<?php declare(strict_types=1);


namespace App\Shared\Domain;


use Webmozart\Assert\Assert;

class Minutes
{
    private int $value;

    public function __construct(int $value)
    {
        Assert::greaterThanEq($value, 1, 'Minutes must be in range 1-60');
        Assert::lessThanEq($value, 60, 'Minutes must be in range 1-60');
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return (string) $this->value;
    }
}
