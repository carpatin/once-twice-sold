<?php

declare(strict_types=1);

namespace OnceTwiceSold\MessageHandler;

use Closure;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

interface MessageHandlerInterface
{
    public function handle(int $connection, AbstractMessage $message, Closure $pushCallback): void;

    public function getHandledMessageType(): MessageTypeEnum;
}
