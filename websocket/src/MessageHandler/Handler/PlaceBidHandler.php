<?php

declare(strict_types=1);

namespace OnceTwiceSold\MessageHandler\Handler;

use Closure;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\BidderToServer\PlaceBid;
use OnceTwiceSold\Message\MessageTypeEnum;
use OnceTwiceSold\Message\ServerToAll\NewHighBid;
use OnceTwiceSold\Message\ServerToBidder\YouBidTooLow;
use OnceTwiceSold\MessageHandler\MessageHandlerInterface;
use OnceTwiceSold\Model\Auction;
use OnceTwiceSold\Model\Participant;
use OnceTwiceSold\Persistence\AuctionRepository;
use OnceTwiceSold\Persistence\ParticipantRepository;
use OnceTwiceSold\WebSocketServer\Clients;
use RuntimeException;

readonly class PlaceBidHandler implements MessageHandlerInterface
{
    public function __construct(
        private AuctionRepository $auctionsRepository,
        private ParticipantRepository $participantRepository,
    ) {
        //
    }

    public function handle(Clients $clients, AbstractMessage $message, Closure $pushCallback): void
    {
        /** @var $message PlaceBid */
        // check that the auction referenced is valid
        $auction = $this->auctionsRepository->loadById($message->getAuctionId());

        if (null === $auction) {
            throw new RuntimeException('Auction not found');
        }

        if ($auction->getState() === Auction::STATE_CLOSED) {
            throw new RuntimeException('Auction is closed');
        }

        $bidderClientId = $clients->getCurrentClient();

        // check that bid amount is above the starting price
        if ($auction->getStartingPrice() > $message->getBid()) {
            $pushCallback([
                $bidderClientId => new YouBidTooLow([
                    'auction_id'     => $auction->getUuid(),
                    'your_bid'       => $message->getBid(),
                    'starting_price' => $auction->getStartingPrice(),
                ]),
            ]);

            return;
        }

        // persist bidder details
        $bidder = new Participant($bidderClientId, $message->getBidderName(), $message->getBidderEmail());
        $this->participantRepository->persist($bidder);

        // place the bid
        $isHighest = $auction->placeBid($bidderClientId, $message->getBid());
        $this->auctionsRepository->persist($auction);
        if ($isHighest) {
            // push message to everyone when the highest bid was placed
            $pushMessages = [];
            $highestBidMessage = new NewHighBid([
                'auction_id' => $auction->getUuid(),
                'bid'        => $auction->getHighestBidderPrice(),
            ]);

            foreach ($clients->getAllClients() as $clientId) {
                $pushMessages [$clientId] = $highestBidMessage;
            }
            $pushCallback($pushMessages);
        }
    }

    public function getHandledMessageType(): MessageTypeEnum
    {
        return MessageTypeEnum::PLACE_BID;
    }
}
