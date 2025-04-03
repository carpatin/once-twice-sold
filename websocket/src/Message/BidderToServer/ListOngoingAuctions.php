<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\BidderToServer;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "list_ongoing_auctions",
 *  "data": {}
 * }
 */
class ListOngoingAuctions extends AbstractMessage
{
    public function __construct(array $data)
    {
        parent::__construct(MessageTypeEnum::LIST_ONGOING_AUCTIONS, $data);
    }
}
