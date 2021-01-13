<?php


namespace App\Game\Table\Domain;


interface PlayerRepositoryInterface
{
    public function save(Player $player): void;
}
