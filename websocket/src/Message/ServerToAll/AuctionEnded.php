<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToAll;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "auction_ended",
 *  "data": {
 *      "auction_id": "abc123",
 *      "item" : "Vintage Watch",
 *      "verdict": "sold", // or "unsold"
 *      "final_price": 260
 *   }
 * }
 */
class AuctionEnded extends AbstractMessage
{
    private const array KEYS = [
        'auction_id',
        'item',
        'verdict',
        'final_price',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::AUCTION_ENDED, $data);
    }
}
