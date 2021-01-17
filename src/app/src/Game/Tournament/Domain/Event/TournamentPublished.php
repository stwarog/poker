<?php declare(strict_types=1);


namespace App\Game\Tournament\Event;


use App\Shared\Domain\AbstractDomainEvent;

class TournamentPublished extends AbstractDomainEvent
{
    public static function eventName(): string
    {
        return 'tournament.published';
    }
}
