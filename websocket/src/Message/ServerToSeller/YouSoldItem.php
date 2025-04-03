<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToSeller;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "you_sold_item",
 *  "data": {
 *      "auction_id": "uuid",
 *      "item" : "Vintage Watch",
 *      "final_price": 260,
 *      "buyer_name": "Mr. Bidder",
 *      "buyer_email": "bidder@example.com"
 *  }
 * }
 */
class YouSoldItem extends AbstractMessage
{
    private const array KEYS = [
        'auction_id',
        'item',
        'final_price',
        'buyer_name',
        'buyer_email',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::YOU_SOLD_ITEM, $data);
    }
}
