<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToSeller;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "you_started_auction",
 *  "data": {
 *      "auction_id": "uuid",
 *      "title": "Vintage Watch",
 *      "starting_price": 100,
 *      "desired_price": 250,
 *      "started_at": "2025-04-01T9:30:00Z"
 *      "ends_at": "2025-04-01T10:30:00Z"
 *  }
 * }
 */
class YouStartedAuction extends AbstractMessage
{
    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::YOU_STARTED_AUCTION, $data);
    }
}
