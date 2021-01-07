<?php declare(strict_types=1);


namespace App\Game\Shared\Infrastructure;


use App\Game\Shared\Domain\Cards\Card;
use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Shared\Domain\Cards\CardFactoryInterface;
use App\Game\Shared\Domain\Cards\Color;
use App\Game\Shared\Domain\Cards\Value;

class CardDeckFactory implements CardFactoryInterface
{
    public function create(): CardCollection
    {
        $c = new CardCollection();
        $c->addCard(
            new Card(Color::CLUB(), Value::FIVE())
        );

        return $c;
    }
}
