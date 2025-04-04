<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToSeller;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "you_did_not_sell_item",
 *  "data": {
 *      "auction_id": "uuid",
 *      "item" : "Vintage Watch",
 *      "final_price": 250,
 *      "desired_price": 260
 *  }
 * }
 */
class YouDidNotSellItem extends AbstractMessage
{
    private const array KEYS = [
        'auction_id',
        'item',
        'final_price',
        'desired_price',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::YOU_DID_NOT_SELL_ITEM, $data);
    }
}
