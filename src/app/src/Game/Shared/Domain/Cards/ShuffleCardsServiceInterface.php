<?php


namespace App\Game\Shared\Domain\Cards;


interface ShuffleCardsServiceInterface
{
    public function shuffle(CardCollection $deck): void;
}
