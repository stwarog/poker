<?php declare(strict_types=1);


namespace App\Shared\Common;


use DateTimeImmutable;

abstract class DomainEvent
{
    private string $eventId;
    private string $occurredOn;
    private string $aggregateId;

    public function __construct(string $aggregateId, string $eventId = null, string $occurredOn = null)
    {
        $this->eventId     = $eventId ?: (string) Uuid::random();
        $this->occurredOn  = $occurredOn ?: (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $this->aggregateId = $aggregateId;
    }

    abstract public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self;

    abstract public static function eventName(): string;

    abstract public function toPrimitives(): array;

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function occurredOn(): string
    {
        return $this->occurredOn;
    }
}
