<?php

declare(strict_types=1);

namespace OnceTwiceSold\MessageHandler\Handler;

use Closure;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\BidderToServer\PlaceBid;
use OnceTwiceSold\Message\MessageTypeEnum;
use OnceTwiceSold\Message\ServerToAll\NewHighBid;
use OnceTwiceSold\Message\ServerToBidder\BidTooLow;
use OnceTwiceSold\MessageHandler\MessageHandlerInterface;
use OnceTwiceSold\Persistence\AuctionsRepository;
use OnceTwiceSold\WebSocketServer\Participants;
use RuntimeException;

readonly class PlaceBidHandler implements MessageHandlerInterface
{
    public function __construct(
        private AuctionsRepository $auctionsRepository,
    ) {
        //
    }

    public function handle(Participants $participants, AbstractMessage $message, Closure $pushCallback): void
    {
        /** @var $message PlaceBid */
        // check that the auction referenced is valid
        $auction = $this->auctionsRepository->loadAuction($message->getAuctionId());
        if (null === $auction) {
            throw new RuntimeException('Auction not found');
        }

        $bidder = $participants->getCurrentParticipant();

        // check that bid amount is above the starting price
        if ($auction->getStartingPrice() > $message->getAmount()) {
            $pushCallback([
                $bidder => new BidTooLow([
                    'auction_id'     => $auction->getUuid(),
                    'your_bid'       => $message->getAmount(),
                    'starting_price' => $auction->getStartingPrice(),
                ]),
            ]);
            return;
        }

        // place the bid
        $isHighest = $auction->placeBid($bidder, $message->getAmount());
        $this->auctionsRepository->persistAuction($auction);
        if ($isHighest) {
            // push message to everyone when the highest bid was placed
            $pushMessages = [];
            $highestBidMessage = new NewHighBid([
                'auction_id' => $auction->getUuid(),
                'amount'     => $auction->getHighestBidderPrice(),
                'bidder_id'  => $bidder,
            ]);

            foreach ($participants->getAllParticipants() as $participant) {
                $pushMessages [$participant] = $highestBidMessage;
            }
            $pushCallback($pushMessages);
        }
    }

    public function getHandledMessageType(): MessageTypeEnum
    {
        return MessageTypeEnum::PLACE_BID;
    }
}
