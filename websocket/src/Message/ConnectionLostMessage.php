<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message;

/**
 * Used internally to encapsulate the event of a client connection close.
 */
class ConnectionLostMessage extends AbstractMessage
{
    public function __construct()
    {
        parent::__construct(MessageTypeEnum::CONNECTION_LOST, []);
    }
}
