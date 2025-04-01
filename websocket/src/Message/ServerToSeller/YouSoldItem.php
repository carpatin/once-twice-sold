<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToSeller;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "you_sold_item",
 *  "data": {
 *      "item_id": "abc123",
 *      "final_price": 260,
 *      "buyer_id": "user789",
 *      "buyer_contact": {
 *          "name": "Alice",
 *          "email": "alice@example.com"
 *      }
 *  }
 * }
 */
class YouSoldItem extends AbstractMessage
{
    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::YOU_SOLD_ITEM, $data);
    }
}