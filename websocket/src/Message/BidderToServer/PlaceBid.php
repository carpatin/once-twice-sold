<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\BidderToServer;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "place_bid",
 *  "data": {
 *      "auction_id": "uuid",
 *      "amount": 120.00
 *  }
 * }
 */
class PlaceBid extends AbstractMessage
{
    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::PLACE_BID, $data);
    }

    public function getAuctionId(): string
    {
        return $this->data['auction_id'];
    }

    public function getAmount(): float
    {
        return $this->data['amount'];
    }
}
