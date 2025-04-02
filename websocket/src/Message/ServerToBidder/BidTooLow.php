<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToBidder;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "bid_too_low",
 *  "data": {
 *   "auction_id": "uuid",
 *   "your_bid": 140
 *   "starting_price": 150
 *  }
 * }
 */
class BidTooLow extends AbstractMessage
{
    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::BID_TOO_LOW, $data);
    }
}
