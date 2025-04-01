<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\BidderToServer;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

class PlaceBid extends AbstractMessage
{
    /**
     * {
     * "type": "place_bid",
     * "data": {
     * "item_id": "abc123",
     * "amount": 120
     * }
     * }
     */

    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::PLACE_BID, $data);
    }
}
