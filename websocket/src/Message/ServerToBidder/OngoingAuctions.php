<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToBidder;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "ongoing_auctions",
 *  "data": {
 *  "auctions" : [
 *    {
 *      "auction_id": "uuid",
 *      "item": "Vintage Watch",
 *      "starting_price": 100,
 *      "started_at": "2025-04-01T09:30:00Z"
 *      "ends_at": "2025-04-01T10:30:00Z"
 *    }
 *   ]
 *  }
 * }
 */
class OngoingAuctions extends AbstractMessage
{
    private const array KEYS = [
        'auctions',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::ONGOING_AUCTIONS, $data);
    }
}
