<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToBidder;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 * "type": "you_lost_bid",
 * "data": {
 * "item_id": "abc123",
 * "final_price": 260,
 * "winner_id": "user789"
 * }
 * }
 */
class YouLostBid extends AbstractMessage
{
    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::YOU_LOST_BID, $data);
    }
}