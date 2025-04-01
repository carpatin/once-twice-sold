<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\BidderToServer;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

class SubscribeToAuctions extends AbstractMessage
{
    /**
     * {
     * "type": "subscribe_to_auctions",
     * "data": {}
     * }
     */

    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::SUBSCRIBE_TO_AUCTIONS, $data);
    }
}