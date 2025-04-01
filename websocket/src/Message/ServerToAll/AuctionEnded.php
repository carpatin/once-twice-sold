<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToAll;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

class AuctionEnded extends AbstractMessage
{
    /**
     * {
     * "type": "auction_ended",
     * "data": {
     * "item_id": "abc123",
     * "status": "sold", // or "unsold"
     * "final_price": 260,
     * "winner_id": "user789"
     * }
     * }
     */

    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::AUCTION_ENDED, $data);
    }
}
