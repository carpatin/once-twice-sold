<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message;

use JsonException;
use OnceTwiceSold\Message\BidderToServer\PlaceBid;
use OnceTwiceSold\Message\BidderToServer\SubscribeToAuctions;
use OnceTwiceSold\Message\SellerToServer\StartAuction;
use OnceTwiceSold\Message\ServerToAll\AuctionEnded;
use OnceTwiceSold\Message\ServerToAll\AuctionStarted;
use OnceTwiceSold\Message\ServerToAll\NewHighBid;
use OnceTwiceSold\Message\ServerToBidder\AuctionsRunning;
use OnceTwiceSold\Message\ServerToBidder\YouLostBid;
use OnceTwiceSold\Message\ServerToBidder\YouWonItem;
use OnceTwiceSold\Message\ServerToSeller\YouSoldItem;

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
            MessageTypeEnum::SUBSCRIBE_TO_AUCTIONS->value => new SubscribeToAuctions($data),
            MessageTypeEnum::START_AUCTION->value => new StartAuction($data),
            MessageTypeEnum::AUCTION_ENDED->value => new AuctionEnded($data),
            MessageTypeEnum::AUCTION_STARTED->value => new AuctionStarted($data),
            MessageTypeEnum::NEW_HIGH_BID->value => new NewHighBid($data),
            MessageTypeEnum::AUCTIONS_RUNNING->value => new AuctionsRunning($data),
            MessageTypeEnum::YOU_LOST_BID->value => new YouLostBid($data),
            MessageTypeEnum::YOU_WON_ITEM->value => new YouWonItem($data),
            MessageTypeEnum::YOU_SOLD_ITEM->value => new YouSoldItem($data),
        };
    }
}
