<?php


namespace App\Game\Tournament\Domain;


interface PlayerRepositoryInterface
{
    public function save(Player $player): void;
}
