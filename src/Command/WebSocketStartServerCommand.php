<?php


namespace App\Command;


use App\Service\WebSocketHandler;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebSocketStartServerCommand extends Command
{

    protected static $defaultName = "run:websocket-server";

    public function __construct(private WebSocketHandler $webSocketHandler)
    {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = 3200;
        $output->writeln("WebSocket server on port " . $port);
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    $this->webSocketHandler
                )
            ),
            $port
        );
        $server->run();
        return 0;
    }
}