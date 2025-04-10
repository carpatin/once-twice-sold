<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToBidder;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "you_won_item",
 *  "data": {
 *      "auction_id": "uuid",
 *      "item": "Vintage Watch",
 *      "final_price": 260,
 *      "seller_name": "Mr. Seller",
 *      "seller_email": "seller@example.com"
 *  }
 * }
 */
class YouWonItem extends AbstractMessage
{
    private const array KEYS = [
        'auction_id',
        'item',
        'final_price',
        'seller_name',
        'seller_email',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::YOU_WON_ITEM, $data);
    }
}
