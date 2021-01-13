<?php declare(strict_types=1);


namespace App\Game\Tournament\UI;


use App\Game\Tournament\Application\ClientComponentInterface;
use App\Game\Tournament\Application\TournamentFacade;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SocketServerCommand extends Command
{
    protected static $defaultName = 'app:ser';
    private TournamentFacade $facade;
    private ClientComponentInterface $clientComponent;

    public function __construct(string $name = null, TournamentFacade $facade, ClientComponentInterface $clientComponent)
    {
        $this->facade          = $facade;
        $this->clientComponent = $clientComponent;
        parent::__construct($name);
    }

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = 3001;
        $output->writeln("Starting server on port ".$port);
        $server = IoServer::factory(
            new HttpServer(
                new WsServer($this->clientComponent)
            ),
            $port
        );
        $server->run();

        return Command::SUCCESS;
    }
}
