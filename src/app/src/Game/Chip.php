<?php declare(strict_types=1);


namespace App\Game;


use Webmozart\Assert\Assert;

class Chip
{
    public const RED_25 = 25;
    public const WHITE_50 = 50;
    public const GREEN_100 = 100;
    public const BLUE_500 = 500;
    public const BLACK_1000 = 1000;

    private int $value;

    public function __construct(int $value)
    {
        Assert::greaterThanEq($value, self::RED_25, 'Amount must be greater than zero');
        foreach ($this->toArray() as $allowedValue) {
            if ($allowedValue > $value) {
                break;
            }
            Assert::eq($value % $allowedValue, 0, sprintf('Amount must be dividable by: %s', implode(', ', $this->toArray())));
        }
        $this->value = $value;
    }

    private function toArray(): array
    {
        return [
            self::RED_25,
            self::WHITE_50,
            self::GREEN_100,
            self::BLUE_500,
            self::BLACK_1000,
        ];
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
}
