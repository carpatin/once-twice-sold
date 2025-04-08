<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToBidder;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "you_bid_too_low",
 *  "data": {
 *   "auction_id": "uuid",
 *   "item" : "Vintage watch",
 *   "your_bid": 140
 *   "starting_price": 150
 *  }
 * }
 */
class YouBidTooLow extends AbstractMessage
{
    private const array KEYS = [
        'auction_id',
        'item',
        'your_bid',
        'starting_price',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::YOU_BID_TOO_LOW, $data);
    }
}
