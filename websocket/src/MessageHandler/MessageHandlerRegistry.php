<?php

declare(strict_types=1);

namespace OnceTwiceSold\MessageHandler;

use OnceTwiceSold\Message\AbstractMessage;
use RuntimeException;

class MessageHandlerRegistry
{
    private array $handlers;

    public function __construct(iterable $handlers)
    {
        /** @var MessageHandlerInterface $handler */
        foreach ($handlers as $handler) {
            $this->handlers[$handler->getHandledMessageType()->value] = $handler;
        }
    }

    public function getHandler(AbstractMessage $message): MessageHandlerInterface
    {
        if (!isset($this->handlers[$message->getType()->value])) {
            throw new RuntimeException('Unknown message type');
        }

        return $this->handlers[$message->getType()->value];
    }
}
