<?php declare(strict_types=1);


namespace App\Game\Tournament\Infrastructure;


use App\Game\Tournament\Application\ClientComponentInterface;
use Exception;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

class ClientSocketHandler implements ClientComponentInterface
{
    private SplObjectStorage $connections;

    public function __construct()
    {
        $this->connections = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->connections->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        foreach ($this->connections as $connection) {
            if ($connection === $from) {
                continue;
            }
            $connection->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $this->connections->detach($conn);
    }

    public function onError(ConnectionInterface $conn, Exception $e): void
    {
        $this->connections->detach($conn);
        $conn->close();
    }
}
