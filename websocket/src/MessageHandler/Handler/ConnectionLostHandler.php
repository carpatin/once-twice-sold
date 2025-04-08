<?php

declare(strict_types=1);

namespace OnceTwiceSold\MessageHandler\Handler;

use Closure;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;
use OnceTwiceSold\Message\ServerToAll\AuctionEnded;
use OnceTwiceSold\Message\ServerToAll\NewHighBid;
use OnceTwiceSold\MessageHandler\MessageHandlerInterface;
use OnceTwiceSold\Model\Auction;
use OnceTwiceSold\Persistence\AuctionRepository;
use OnceTwiceSold\Persistence\ParticipantRepository;
use OnceTwiceSold\Persistence\PhotoRepository;
use OnceTwiceSold\WebSocketServer\Clients;

readonly class ConnectionLostHandler implements MessageHandlerInterface
{
    public function __construct(
        private AuctionRepository $auctionsRepository,
        private PhotoRepository $photoRepository,
        private ParticipantRepository $participantRepository,
    ) {
        //
    }

    public function handle(Clients $clients, AbstractMessage $message, Closure $pushCallback): void
    {
        // when the disconnected client is a bidder we need to clear all their high bids
        $this->handleCaseHighestBidderLeaves($clients, $pushCallback);
        // when the disconnected client is a seller we need to close their auctions
        $this->handleCaseSellerLeaves($clients, $pushCallback);
        // finally we need to remove the disconnected client from the participants
        $this->participantRepository->removeById($clients->getCurrentClient());
    }

    public function getHandledMessageType(): MessageTypeEnum
    {
        return MessageTypeEnum::CONNECTION_LOST;
    }

    private function handleCaseHighestBidderLeaves(
        Clients $clients,
        Closure $pushCallback
    ): void {
        // handle the case of highest bidder leaving the auction
        $auctions = $this->auctionsRepository->loadByHighestBidder($clients->getCurrentClient());
        $pushMessages = [];
        foreach ($auctions as $auction) {
            // let other clients involved in the auction that the highest bid is no longer there
            $message = new NewHighBid([
                'auction_id' => $auction->getUuid(),
                'item'       => $auction->getItem(),
                'bid'        => 0, // could be improved by storing all bets and falling back to second best
            ]);
            foreach ($clients->getOtherClients() as $clientId) {
                $pushMessages [$clientId][] = $message;
            }
        }
        // send prepared messages to clients
        $pushCallback($pushMessages);
    }

    private function handleCaseSellerLeaves(Clients $clients, Closure $pushCallback): void
    {
        // handle case of seller leaving the auction
        $auctions = $this->auctionsRepository->loadBySeller($clients->getCurrentClient());
        $pushMessages = [];
        /** @var Auction $auction */
        foreach ($auctions as $auction) {
            // set auction state as closed and verdict as unsold
            $auction->closeAuction();
            $auction->setVerdictToUnsold();
            $this->auctionsRepository->persist($auction);
            // remove photos for the closed auction
            $this->photoRepository->removeByAuctionId($auction->getUuid());

            // prepare message about the auction being closed for others involved
            $message = new AuctionEnded([
                'auction_id'  => $auction->getUuid(),
                'item'        => $auction->getItem(),
                'verdict'     => $auction->getVerdict(),
                'final_price' => 0,
            ]);
            foreach ($clients->getOtherClients() as $clientId) {
                $pushMessages[$clientId][] = $message;
            }
        }
        // send prepared messages to clients
        $pushCallback($pushMessages);
    }
}
