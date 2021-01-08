<?php declare(strict_types=1);


namespace App\Game\Shared\Domain\Cards;


class CardDeckFactory implements CardFactoryInterface
{
    public function create(): CardCollection
    {
        $c = new CardCollection();
        foreach (Value::values() as $value) {
            foreach (Color::values() as $color) {
                $c->addCard(
                    new Card(new Color($color), new Value($value))
                );
            }
        }

        return $c;
    }
}
