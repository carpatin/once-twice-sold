<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message\ServerToAll;

use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;

/**
 * {
 *  "type": "item_photo_added",
 *  "data": {
 *      "auction_id": "uuid",
 *      "item": "Vintage watch",
 *      "photo": "<<base64 encoded content>>"
 *   }
 * }
 */
class ItemPhotoAdded extends AbstractMessage
{
    private const array KEYS = [
        'auction_id',
        'photo',
    ];

    public function __construct(array $data)
    {
        assert(empty(array_diff(self::KEYS, array_keys($data))), 'Missing message fields');
        parent::__construct(MessageTypeEnum::ITEM_PHOTO_ADDED, $data);
    }
}
