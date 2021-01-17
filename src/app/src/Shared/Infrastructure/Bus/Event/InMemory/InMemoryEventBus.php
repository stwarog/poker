<?php declare(strict_types=1);


namespace App\Shared\Infrastructure\Bus\Event\InMemory;


use App\Shared\Domain\AbstractDomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;

class InMemoryEventBus implements EventBusInterface
{
    public function publish(AbstractDomainEvent ...$events): void
    {
        foreach ($events as $e) {
            dump($e::eventName(), $e->getBody());
        }
    }
}
