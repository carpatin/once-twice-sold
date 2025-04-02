<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToAll;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "new_high_bid",
 *  "data": {
 *   "auction_id": "uuid",
 *   "amount": 140,
 *   "bidder_id": "user456"
 *  }
 * }
 */
class NewHighBid extends AbstractMessage
{
    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::NEW_HIGH_BID, $data);
    }
}
