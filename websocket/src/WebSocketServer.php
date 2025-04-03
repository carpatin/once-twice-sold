<?php

declare(strict_types=1);

namespace OnceTwiceSold;

use Closure;
use DateMalformedStringException;
use Exception;
use JsonException;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\ErrorMessage;
use OnceTwiceSold\Message\MessageFactory;
use OnceTwiceSold\Message\ServerToSeller\YouStartedAuction;
use OnceTwiceSold\MessageHandler\MessageHandlerRegistry;
use OnceTwiceSold\Persistence\AuctionRepository;
use OnceTwiceSold\Persistence\ParticipantRepository;
use OnceTwiceSold\WebSocketServer\Clients;
use OpenSwoole\Http\Request;
use OpenSwoole\Timer;
use OpenSwoole\WebSocket\Frame;
use OpenSwoole\WebSocket\Server;

class WebSocketServer
{
    private Server $server;

    public function __construct(
        private readonly string $listenHost,
        private readonly int $listenPort,
        private readonly AuctionRepository $auctionsRepository,
        private readonly ParticipantRepository $participantRepository,
        private readonly MessageFactory $messageFactory,
        private readonly MessageHandlerRegistry $messageHandlerRegistry,
    ) {
        $this->server = new Server($this->listenHost, $this->listenPort);
        $this->server->on('Start', [$this, 'onStart']);
        $this->server->on('Open', [$this, 'onOpen']);
        $this->server->on('Message', [$this, 'onMessage']);
        $this->server->on('Close', [$this, 'onClose']);
        $this->server->on('Disconnect', [$this, 'onDisconnect']);

        // initialize memory tables before starting the server
        $this->participantRepository->initializeTable();
        $this->auctionsRepository->initializeTable();
    }

    public function onStart(Server $server): void
    {
        echo "Once,Twice...Sold server started at {$this->listenHost}:{$this->listenPort}".PHP_EOL;
    }

    public function onOpen(Server $server, Request $request): void
    {
        echo "Connection open: $request->fd".PHP_EOL;
    }

    /**
     * @throws JsonException
     */
    public function onMessage(Server $server, Frame $frame): void
    {
        echo "Received message: $frame->data".PHP_EOL;

        $connection = $frame->fd;
        try {
            // respond to client callback, usable from within the client message's handler
            $pushCallback = $this->preparePushCallback($server);
            $message = $this->messageFactory->createFromData($frame->data);
            $handler = $this->messageHandlerRegistry->getHandler($message);
            $handler->handle($this->prepareClients($connection, $server), $message, $pushCallback);
        } catch (Exception $exception) {
            $message = ErrorMessage::createForException($exception);
            $server->push($connection, $message->toJson());
        }
    }

    public function onClose(Server $server, int $connection): void
    {
        echo "Connection close: $connection".PHP_EOL;
    }

    public function onDisconnect(Server $server, int $fd): void
    {
        echo "Connection disconnect: $fd".PHP_EOL;
    }

    public function start(): void
    {
        $this->server->start();
    }

    private function prepareClients(int $connection, Server $server): Clients
    {
        $otherConnections = iterator_to_array($server->connections);
        unset($otherConnections[array_search($connection, $otherConnections, true)]);

        return new Clients($connection, $otherConnections);
    }

    /**
     * The returned push callback receives an array of messages (concrete classes extending Message\AbstractMessage)
     * indexed by the integer connection (client) identifier.
     * The callback simply iterates these messages and pushes each message to the associated connection/client.
     */
    private function preparePushCallback(Server $server): Closure
    {
        return function (array $messages) use ($server): void {
            foreach ($messages as $connection => $oneOrMoreMessages) {
                // ensure array when only one message was sent to a connection
                if (!is_array($oneOrMoreMessages)) {
                    $oneOrMoreMessages = [$oneOrMoreMessages];
                }
                foreach ($oneOrMoreMessages as $message) {
                    // handle case when pushing a certain message requires starting related timer
                    $participants = $this->prepareClients($connection, $server);
                    $this->setupTimers($message, $server, $participants);

                    // push message to client if the connection is an established WebSocket connection
                    if ($server->isEstablished($connection)) {
                        $server->push($connection, $message->toJson());
                    }
                }
            }
        };
    }

    /**
     * Some messages that the server pushes to acknowledge change of state need to start timers
     * that would execute at a given point in the future. The logic executed when the timer expires is
     * also a message handler, this time specialized in handling Server pushed messages.
     *
     * @throws DateMalformedStringException
     */
    private function setupTimers(
        AbstractMessage $message,
        Server $server,
        Clients $participants,
    ): void {
        $pushCallback = $this->preparePushCallback($server);

        // in case of a message pushed back to the seller acknowledging the auction start we start a timer
        // that expires when the bidding is due to finish in order to handle the auction results
        if ($message instanceof YouStartedAuction) {
            $intervalMs = $message->getSecondsToEnd() * 1000;
            $handler = $this->messageHandlerRegistry->getHandler($message);

            Timer::after($intervalMs, static function () use ($handler, $participants, $message, $pushCallback) {
                $handler->handle($participants, $message, $pushCallback);
            });
        }
    }
}
