<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message;

enum MessageTypeEnum: string
{
    case ERROR = 'error';

    case LIST_ONGOING_AUCTIONS = 'list_ongoing_auctions';
    case ONGOING_AUCTIONS = 'ongoing_auctions';

    case START_AUCTION = 'start_auction';
    case YOU_STARTED_AUCTION = 'you_started_auction';
    case PLACE_BID = 'place_bid';
    case AUCTION_STARTED = 'auction_started';
    case NEW_HIGH_BID = 'new_high_bid';
    case AUCTION_ENDED = 'auction_ended';
    case YOU_SOLD_ITEM = 'you_sold_item';
    case YOU_WON_ITEM = 'you_won_item';
    case YOU_LOST_BID = 'you_lost_bid ';
    case BID_TOO_LOW = 'bid_too_low';
}
