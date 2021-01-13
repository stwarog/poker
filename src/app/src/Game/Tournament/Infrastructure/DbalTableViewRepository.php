<?php declare(strict_types=1);


namespace App\Game\Tournament\Infrastructure;


use App\Game\Shared\Domain\TableId;
use App\Game\Tournament\Application\TableView;
use App\Game\Tournament\Application\TableViewRepositoryInterface;
use Doctrine\DBAL\Connection;

class DbalTableViewRepository implements TableViewRepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getById(TableId $table): TableView
    {
        $q = $this->connection->createQueryBuilder();
        $q->select('id', 'round', 'cards', 'chips', 'small_blind', 'big_blind', 'player_id')
            ->from('game_table')
            ->where('id = ?')
            ->setParameter(0, $table->toString());

        $r = $this->connection->fetchAssociative($q->getSQL(), $q->getParameters());

        return TableView::fromArray($r);
    }
}
