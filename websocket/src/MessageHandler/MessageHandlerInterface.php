<?php

declare(strict_types=1);

namespace OnceTwiceSold\MessageHandler;

use Closure;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;
use OnceTwiceSold\WebSocketServer\Participants;

interface MessageHandlerInterface
{
    public function handle(Participants $participants, AbstractMessage $message, Closure $pushCallback): void;

    public function getHandledMessageType(): MessageTypeEnum;
}
