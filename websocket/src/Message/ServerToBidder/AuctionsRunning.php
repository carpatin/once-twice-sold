<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToBidder;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

class AuctionsRunning extends AbstractMessage
{
    /**
     * {
     * "type": "auctions_running",
     * "data": {
     * "auctions" : [
     * {
     * "item_id": "abc123",
     * "title": "Vintage Watch",
     * "starting_price": 100,
     * "desired_price": 250,
     * "duration": 60,
     * "ends_at": "2025-04-01T10:30:00Z"
     * }
     * ]
     * }
     * }
     */

    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::AUCTIONS_RUNNING, $data);
    }
}
