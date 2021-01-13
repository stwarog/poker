<?php


namespace App\Game\Tournament\Application;


use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

interface ClientComponentInterface extends MessageComponentInterface
{
    public function onOpen(ConnectionInterface $conn): void;

    public function onMessage(ConnectionInterface $from, $msg): void;

    public function onClose(ConnectionInterface $conn): void;

    public function onError(ConnectionInterface $conn, Exception $e): void;
}
