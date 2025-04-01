<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToBidder;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "you_won_item",
 *  "data": {
 *      "item_id": "abc123",
 *      "final_price": 260,
 *      "seller_contact": {
 *          "name": "Bob",
 *          "email": "bob@example.com"
 *      }
 *  }
 * }
 */
class YouWonItem extends AbstractMessage
{
    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::YOU_WON_ITEM, $data);
    }
}