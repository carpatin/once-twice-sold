<?php

declare(strict_types=1);

namespace OnceTwiceSold\MessageHandler;

use Closure;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;
use OnceTwiceSold\WebSocketServer\Clients;

interface MessageHandlerInterface
{
    public function handle(Clients $clients, AbstractMessage $message, Closure $pushCallback): void;

    public function getHandledMessageType(): MessageTypeEnum;
}
