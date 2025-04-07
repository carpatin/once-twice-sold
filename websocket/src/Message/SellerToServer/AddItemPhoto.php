<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\SellerToServer;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "add_item_photo",
 *  "data": {
 *      "auction_id": "uuid",
 *      "photo": "<<base64 encoded content>>"
 *  }
 * }
 */
class AddItemPhoto extends AbstractMessage
{
    private const array KEYS = [
        'auction_id',
        'photo',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::ADD_ITEM_PHOTO, $data);
    }

    public function getAuctionId(): string
    {
        return $this->data['auction_id'];
    }

    public function getPhoto(): string
    {
        return $this->data['photo'];
    }
}
