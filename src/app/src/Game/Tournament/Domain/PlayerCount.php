<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


use Webmozart\Assert\Assert;

class PlayerCount
{
    private int $min;
    private int $max;

    public function __construct(int $min = 2, int $max = 12)
    {
        Assert::greaterThanEq($min, 2, 'Players count must be at least 2');
        Assert::lessThanEq($max, 12, 'Players count can not be greater than 12');
        $this->min = $min;
        $this->max = $max;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): int
    {
        return $this->max;
    }
}
