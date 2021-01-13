<?php declare(strict_types=1);


namespace App\Game\Tournament\Event;


use App\Shared\Domain\AbstractDomainEvent;

class TournamentStarted extends AbstractDomainEvent
{
    public static function eventName(): string
    {
        return 'tournament.started';
    }

    public static function create(string $aggregateId, string $table): self
    {
        return new self(
            $aggregateId,
            [
                'table_id' => $table,
            ]
        );
    }
}
