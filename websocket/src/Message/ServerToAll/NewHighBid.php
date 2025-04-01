<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToAll;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

class NewHighBid extends AbstractMessage
{
    /**
     * {
     * "type": "new_high_bid",
     * "data": {
     * "item_id": "abc123",
     * "amount": 140,
     * "bidder_id": "user456"
     * }
     * }
     */

    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::NEW_HIGH_BID, $data);
    }
}