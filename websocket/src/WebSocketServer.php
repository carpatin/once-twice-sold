<?php

declare(strict_types=1);

namespace OnceTwiceSold;

use Exception;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\ErrorMessage;
use OnceTwiceSold\Message\MessageFactory;
use OnceTwiceSold\MessageHandler\MessageHandlerRegistry;
use OpenSwoole\Http\Request;
use OpenSwoole\WebSocket\Frame;
use OpenSwoole\WebSocket\Server;

class WebSocketServer
{
    private Server $server;


    public function __construct(
        private readonly string $listenHost,
        private readonly int $listenPort,
        private MessageFactory $messageFactory,
        private MessageHandlerRegistry $messageHandlerRegistry,
    ) {
        $this->server = new Server($this->listenHost, $this->listenPort);
        $this->server->on('Start', [$this, 'onStart']);
        $this->server->on('Open', [$this, 'onOpen']);
        $this->server->on('Message', [$this, 'onMessage']);
        $this->server->on('Close', [$this, 'onClose']);
        $this->server->on('Disconnect', [$this, 'onDisconnect']);
    }

    public function onStart(Server $server): void
    {
        echo "Once,Twice...Sold server started at {$this->listenHost}:{$this->listenPort}".PHP_EOL;
    }

    public function onOpen(Server $server, Request $request): void
    {
        echo "Connection open: $request->fd".PHP_EOL;
    }

    public function onMessage(Server $server, Frame $frame): void
    {
        echo "Received message: $frame->data".PHP_EOL;

        $connection = $frame->fd;
        try {
            $message = $this->messageFactory->createFromData($frame->data);
            $handler = $this->messageHandlerRegistry->getHandler($message);
            // respond to client callback, usable from within the client message's handler
            $pushCallback = function (array $messages) use ($server): void {
                /**
                 * @var int $connection
                 * @var AbstractMessage $message
                 */
                foreach ($messages as $connection => $message) {
                    $server->push($connection, $message->toJson());
                }
            };

            $handler->handle($connection, $message, $pushCallback);
        } catch (Exception $exception) {
            $message = ErrorMessage::createForException($exception);
            $server->push($connection, $message->toJson());
        }
    }

    public function onClose(Server $server, int $connectionId): void
    {
        echo "Connection close: $connectionId".PHP_EOL;
    }

    public function onDisconnect(Server $server, int $fd): void
    {
        echo "Connection disconnect: $fd".PHP_EOL;
    }

    public function start(): void
    {
        $this->server->start();
    }
}