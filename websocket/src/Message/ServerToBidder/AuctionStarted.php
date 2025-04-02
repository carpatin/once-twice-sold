<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToBidder;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "auction_started",
 *  "data": {
 *      "auction_id": "uuid",
 *      "title": "Vintage Watch",
 *      "starting_price": 100,
 *      "started_at": "2025-04-01T9:30:00Z"
 *      "ends_at": "2025-04-01T10:30:00Z"
 *  }
 * }
 */
class AuctionStarted extends AbstractMessage
{
    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::AUCTION_STARTED, $data);
    }
}
