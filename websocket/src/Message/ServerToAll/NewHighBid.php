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
 *   "bid": 140,
 *  }
 * }
 */
class NewHighBid extends AbstractMessage
{
    private const array KEYS = [
        'auction_id',
        'bid',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::NEW_HIGH_BID, $data);
    }
}
