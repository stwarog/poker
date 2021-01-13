<?php


namespace App\Game\Table\Domain;


interface TableRepositoryInterface
{
    public function save(Table $table): void;
}
