<?php declare(strict_types=1);


namespace Integration;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class IntegrationTest extends KernelTestCase
{
    protected Connection $connection;
    protected ContainerInterface $c;

    protected function setUp(): void
    {
        parent::setUp();
        $kernel  = self::bootKernel();
        $this->c = $kernel->getContainer();

        /** @var Connection $connection */
        $connection       = self::$container->get(Connection::class);
        $this->connection = $connection;
    }

    protected function getDbCount(string $table): int
    {
        $q = $this->q()->select('COUNT(*)')->from($table);

        return (int) $q->execute()->fetchOne();
    }

    protected function q(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }
}
