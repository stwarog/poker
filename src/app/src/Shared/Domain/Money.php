<?php declare(strict_types=1);


namespace App\Shared\Domain;


use Webmozart\Assert\Assert;

class Money
{
    private int $value;
    private string $currency;

    public function __construct(int $value, string $currency = 'PLN')
    {
        Assert::greaterThanEq($value, 1, 'Value must be greater than 0');
        Assert::upper($currency);
        Assert::length($currency, 3, 'Currency code must be exactly 3 characters length');
        $this->value = $value;
        $this->currency = $currency;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return sprintf('%d %s', $this->getValue(), $this->currency);
    }
}
