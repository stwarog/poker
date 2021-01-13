<?php


namespace App\Game\Tournament\Application;


use App\Game\Shared\Domain\TableId;

interface TableViewRepositoryInterface
{
    public function getById(TableId $table): TableView;
}
