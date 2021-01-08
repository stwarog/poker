<?php declare(strict_types=1);


namespace App\Game\Shared\Domain\Cards;


interface CardCollectionInterface
{
    public function addCard(Card ...$cards): void;

    public function addCards(CardCollection $cards): void;

    public function hasCard(Card $card): bool;

    public function removeCard(Card $card): void;

    public function pickCard(int $amount): CardCollectionInterface;
}
