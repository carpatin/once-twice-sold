<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\SellerToServer;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "start_auction",
 *  "data": {
 *      "title": "Vintage Watch",
 *      "description": "A rare 1950s Omega",
 *      "starting_price": 100,
 *      "desired_price": 250,
 *      "duration_seconds": 60
 *  }
 * }
 */
class StartAuction extends AbstractMessage
{
    public function __construct(array $data)
    {
        // TODO: validate $data for the right keys and values
        parent::__construct(MessageTypeEnum::START_AUCTION, $data);
    }

    public function getTitle(): string
    {
        return $this->data['title'];
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
