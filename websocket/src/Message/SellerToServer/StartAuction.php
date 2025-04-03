<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\SellerToServer;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "start_auction",
 *  "data": {
 *      "seller_name": "Mr. Seller",
 *      "seller_email": "seller@example.com",
 *      "item": "Vintage Watch",
 *      "description": "A rare 1950s Omega",
 *      "starting_price": 100,
 *      "desired_price": 250,
 *      "duration_seconds": 60
 *  }
 * }
 */
class StartAuction extends AbstractMessage
{
    private const array KEYS = [
        'seller_name',
        'seller_email',
        'item',
        'description',
        'starting_price',
        'desired_price',
        'duration_seconds',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::START_AUCTION, $data);
    }

    public function getSellerName(): string
    {
        return $this->data['seller_name'];
    }

    public function getSellerEmail(): string
    {
        return $this->data['seller_email'];
    }

    public function getItem(): string
    {
        return $this->data['item'];
    }

    public function getDescription(): string
    {
        return $this->data['description'];
    }

    public function getStartingPrice(): float
    {
        return $this->data['starting_price'];
    }

    public function getDesiredPrice(): float
    {
        return $this->data['desired_price'];
    }

    public function getDurationSeconds(): int
    {
        return $this->data['duration_seconds'];
    }
}
