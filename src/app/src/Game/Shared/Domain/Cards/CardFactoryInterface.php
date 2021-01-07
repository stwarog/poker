<?php


namespace App\Game\Shared\Domain\Cards;


interface CardFactoryInterface
{
    public function create(): CardCollection;
}
