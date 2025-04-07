<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message;

use JsonException;
use OnceTwiceSold\Message\BidderToServer\ListOngoingAuctions;
use OnceTwiceSold\Message\BidderToServer\PlaceBid;
use OnceTwiceSold\Message\SellerToServer\AddItemPhoto;
use OnceTwiceSold\Message\SellerToServer\StartAuction;
use OnceTwiceSold\Message\ServerToAll\AuctionEnded;
use OnceTwiceSold\Message\ServerToAll\AuctionStarted;
use OnceTwiceSold\Message\ServerToAll\ItemPhotoAdded;
use OnceTwiceSold\Message\ServerToAll\NewHighBid;
use OnceTwiceSold\Message\ServerToBidder\OngoingAuctions;
use OnceTwiceSold\Message\ServerToBidder\YouBidTooLow;
use OnceTwiceSold\Message\ServerToBidder\YouLostBid;
use OnceTwiceSold\Message\ServerToBidder\YouWonItem;
use OnceTwiceSold\Message\ServerToSeller\YouDidNotSellItem;
use OnceTwiceSold\Message\ServerToSeller\YouSoldItem;
use OnceTwiceSold\Message\ServerToSeller\YouStartedAuction;

class MessageFactory
{
    /**
     * @throws JsonException
     */
    public function createFromData(string $string): AbstractMessage
    {
        $array = json_decode($string, true, 512, JSON_THROW_ON_ERROR);
        $type = $array['type'];
        $data = $array['data'];

        return match ($type) {
            MessageTypeEnum::PLACE_BID->value => new PlaceBid($data),
            MessageTypeEnum::LIST_ONGOING_AUCTIONS->value => new ListOngoingAuctions($data),
            MessageTypeEnum::START_AUCTION->value => new StartAuction($data),
            MessageTypeEnum::ADD_ITEM_PHOTO->value => new AddItemPhoto($data),
            MessageTypeEnum::AUCTION_ENDED->value => new AuctionEnded($data),
            MessageTypeEnum::ITEM_PHOTO_ADDED->value => new ItemPhotoAdded($data),
            MessageTypeEnum::AUCTION_STARTED->value => new AuctionStarted($data),
            MessageTypeEnum::NEW_HIGH_BID->value => new NewHighBid($data),
            MessageTypeEnum::YOU_BID_TOO_LOW->value => new YouBidTooLow($data),
            MessageTypeEnum::ONGOING_AUCTIONS->value => new OngoingAuctions($data),
            MessageTypeEnum::YOU_LOST_BID->value => new YouLostBid($data),
            MessageTypeEnum::YOU_WON_ITEM->value => new YouWonItem($data),
            MessageTypeEnum::YOU_STARTED_AUCTION->value => new YouStartedAuction($data),
            MessageTypeEnum::YOU_DID_NOT_SELL_ITEM->value => new YouDidNotSellItem($data),
            MessageTypeEnum::YOU_SOLD_ITEM->value => new YouSoldItem($data),
        };
    }
}
