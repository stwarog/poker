<?php declare(strict_types=1);


namespace App\Game;


use Webmozart\Assert\Assert;

class Chip
{
    private const DIVIDABLE = 5;
    public const RED25 = 25;
    public const WHITE50 = 50;
    public const GREEN100 = 100;
    public const BLUE500 = 500;
    public const BLACK1000 = 1000;

    private int $value;

    public function __construct(int $value)
    {
        Assert::greaterThanEq($value, 0, 'Amount must be greater or equals zero');

        if ($value !== 0) {
            Assert::eq($value % self::DIVIDABLE, 0, sprintf('Amount must be dividable by: %s', self::DIVIDABLE));
        }

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string) $this->value;
    }

    public static function create(int $value): self
    {
        return new self($value);
    }

    public function equals(Chip $chipsAmount): bool
    {
        return $this->value === $chipsAmount->getValue();
    }
}
