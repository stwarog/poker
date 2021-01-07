<?php declare(strict_types=1);


namespace App\Game\Shared\Domain\Cards;


class Card
{
    private Color $color;
    private Value $value;

    public function __construct(Color $color, Value $value)
    {
        $this->color = $color;
        $this->value = $value;
    }

    public function __toString()
    {
        return sprintf('%s-%s', (string) $this->color, (string) $this->value);
    }
}
