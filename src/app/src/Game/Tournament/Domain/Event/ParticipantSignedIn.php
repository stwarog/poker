<?php declare(strict_types=1);


namespace App\Game\Tournament\Event;


use App\Shared\Domain\AbstractDomainEvent;

class ParticipantSignedIn extends AbstractDomainEvent
{
    public static function eventName(): string
    {
        return 'tournament.participant.signed';
    }

    public static function create(string $aggregateId, string $participant, string $account): self
    {
        return new self(
            $aggregateId,
            [
                'participant_id' => $participant,
                'account_id'     => $account,
            ]
        );
    }
}
