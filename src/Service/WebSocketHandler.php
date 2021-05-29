<?php


namespace App\Service;


use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class WebSocketHandler implements MessageComponentInterface
{
    protected $clients;

    public function __construct(private AuctionService $auctionService)
    {
        $this->clients = new SplObjectStorage();
    }

    function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
    }

    function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->clients->detach($conn);
        $conn->close();
    }

    function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        $auction = $this->auctionService->biddAuction($data['id'], $data['biddOffer'], $data['username']);

            foreach ($this->clients as $client) {
                $client->send($msg);
            }
        }

}