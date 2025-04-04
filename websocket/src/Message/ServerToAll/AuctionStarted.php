<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToAll;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "auction_started",
 *  "data": {
 *      "auction_id": "uuid",
 *      "item": "Vintage Watch",
 *      "description": "A rare 1950s Omega",
 *      "starting_price": 100,
 *      "started_at": "2025-04-01T9:30:00Z"
 *      "ends_at": "2025-04-01T10:30:00Z"
 *  }
 * }
 */
class AuctionStarted extends AbstractMessage
{
    private const array KEYS = [
        'auction_id',
        'item',
        'description',
        'starting_price',
        'started_at',
        'ends_at',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::AUCTION_STARTED, $data);
    }
}
