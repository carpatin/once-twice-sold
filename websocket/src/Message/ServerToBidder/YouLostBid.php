<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToBidder;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "you_lost_bid",
 *  "data": {
 *      "auction_id": "uuid",
 *      "item": "Vintage Watch",
 *      "final_price": 260
 *  }
 * }
 */
class YouLostBid extends AbstractMessage
{
    private const array KEYS = [
        'auction_id',
        'item',
        'final_price',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::YOU_LOST_BID, $data);
    }
}