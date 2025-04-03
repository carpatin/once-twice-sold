<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToSeller;

use DateMalformedStringException;
use DateTime;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "you_started_auction",
 *  "data": {
 *      "auction_id": "uuid",
 *      "item": "Vintage Watch",
 *      "starting_price": 100,
 *      "desired_price": 250,
 *      "started_at": "2025-04-01T9:30:00Z"
 *      "ends_at": "2025-04-01T10:30:00Z"
 *  }
 * }
 */
class YouStartedAuction extends AbstractMessage
{
    private const array KEYS = [
        'auction_id',
        'item',
        'starting_price',
        'desired_price',
        'started_at',
        'ends_at',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::YOU_STARTED_AUCTION, $data);
    }

    public function getAuctionId(): string
    {
        return $this->data['auction_id'];
    }

    private function getEndsAt(): string
    {
        return $this->data['ends_at'];
    }

    /**
     * @throws DateMalformedStringException
     */
    public function getSecondsToEnd(): int
    {
        $endDateTime = new DateTime($this->getEndsAt());
        $endTimestamp = $endDateTime->getTimestamp();

        return $endTimestamp - time();
    }
}
