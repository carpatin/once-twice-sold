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
 *      "bidder_name": "Mr. Bidder",
 *      "bidder_email": "bidder@example.com",
 *      "bid": 120.00
 *  }
 * }
 */
class PlaceBid extends AbstractMessage
{
    private const array KEYS = [
        'bidder_name',
        'bidder_email',
        'auction_id',
        'bid',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::PLACE_BID, $data);
    }

    public function getAuctionId(): string
    {
        return $this->data['auction_id'];
    }

    public function getBidderName(): string
    {
        return $this->data['bidder_name'];
    }

    public function getBidderEmail(): string
    {
        return $this->data['bidder_email'];
    }

    public function getBid(): float
    {
        return $this->data['bid'];
    }
}
