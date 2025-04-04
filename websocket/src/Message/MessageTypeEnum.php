<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message;

enum MessageTypeEnum: string
{
    /**
     * General purpose messages
     */
    case ERROR = 'error';

    /**
     * Seller -> Server
     */
    case START_AUCTION = 'start_auction';

    /**
     * Server -> Seller
     */
    case YOU_STARTED_AUCTION = 'you_started_auction';
    case YOU_SOLD_ITEM = 'you_sold_item';
    case YOU_DID_NOT_SELL_ITEM = 'you_did_not_sell_item';

    /**
     * Bidder -> Server
     */
    case LIST_ONGOING_AUCTIONS = 'list_ongoing_auctions';
    case PLACE_BID = 'place_bid';

    /**
     * Server -> Bidder
     */
    case ONGOING_AUCTIONS = 'ongoing_auctions';
    case YOU_BID_TOO_LOW = 'you_bid_too_low';
    case YOU_LOST_BID = 'you_lost_bid';
    case YOU_WON_ITEM = 'you_won_item';

    /**
     * Server -> * (broadcasts)
     */
    case AUCTION_STARTED = 'auction_started';
    case NEW_HIGH_BID = 'new_high_bid';
    case AUCTION_ENDED = 'auction_ended';
}
