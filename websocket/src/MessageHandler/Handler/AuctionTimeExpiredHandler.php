<?php

declare(strict_types=1);

namespace OnceTwiceSold\MessageHandler\Handler;

use Closure;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;
use OnceTwiceSold\Message\ServerToAll\AuctionEnded;
use OnceTwiceSold\Message\ServerToBidder\YouLostBid;
use OnceTwiceSold\Message\ServerToBidder\YouWonItem;
use OnceTwiceSold\Message\ServerToSeller\YouDidNotSellItem;
use OnceTwiceSold\Message\ServerToSeller\YouSoldItem;
use OnceTwiceSold\Message\ServerToSeller\YouStartedAuction;
use OnceTwiceSold\MessageHandler\MessageHandlerInterface;
use OnceTwiceSold\Model\Auction;
use OnceTwiceSold\Persistence\AuctionRepository;
use OnceTwiceSold\Persistence\ParticipantRepository;
use OnceTwiceSold\WebSocketServer\Clients;
use RuntimeException;

/**
 * This handler is invoked when the time for started auction expires.
 * The current participant is the client that started the auction.
 */
readonly class AuctionTimeExpiredHandler implements MessageHandlerInterface
{
    public function __construct(
        private AuctionRepository $auctionsRepository,
        private ParticipantRepository $participantRepository,
    ) {
        //
    }

    public function handle(Clients $clients, AbstractMessage $message, Closure $pushCallback): void
    {
        $pushMessages = [];
        /** @var YouStartedAuction $message */
        // check that the auction referenced is valid
        $auction = $this->auctionsRepository->loadById($message->getAuctionId());
        if (null === $auction) {
            throw new RuntimeException('Auction not found');
        }

        $auction->closeAuction();
        $verdict = $auction->obtainVerdict();
        $this->auctionsRepository->persist($auction);

        $participants = [$auction->getSellerId(), ...$auction->getAllBiddersIds()];
        // send auction_ended message to everyone
        foreach ($clients->getAllClients() as $client) {
            // skip the auction participants from the general announcement
            if (in_array($client, $participants, true)) {
                continue;
            }

            $pushMessages[$client][] = new AuctionEnded([
                'auction_id'  => $auction->getUuid(),
                'item'        => $auction->getItem(),
                'verdict'     => $verdict,
                'final_price' => $auction->getHighestBidderPrice(),
            ]);
        }

        if ($verdict === Auction::VERDICT_UNSOLD) {
            // send you_did_not_sell_item message to the seller if item
            $this->addYouDidNotSellItemMessage($auction, $pushMessages);

            // send all the participating bidders message about auction ended
            foreach ($auction->getAllBiddersIds() as $bidderClientId) {
                $pushMessages[$bidderClientId][] = new AuctionEnded([
                    'auction_id'  => $auction->getUuid(),
                    'item'        => $auction->getItem(),
                    'verdict'     => $verdict,
                    'final_price' => $auction->getHighestBidderPrice(),
                ]);
            }
        }

        if ($verdict === Auction::VERDICT_SOLD) {
            // send you_lost_bid message to bidders that didn't win if item was sold
            $this->addYouLostBidMessages($auction, $pushMessages);

            // send you_won_item message to the winning bidder if item was sold
            $this->addYouWonItemMessage($auction, $pushMessages);

            // send you_sold_item message to the seller if item was sold
            $this->addYouSoldItemMessage($auction, $pushMessages);
        }

        $pushCallback($pushMessages);
    }

    private function addYouLostBidMessages(Auction $auction, array &$pushMessages): void
    {
        $youLostBid = new YouLostBid([
            'auction_id'  => $auction->getUuid(),
            'item'        => $auction->getItem(),
            'final_price' => $auction->getHighestBidderPrice(),
        ]);
        foreach ($auction->getLoosingBiddersIds() as $loosingBidder) {
            $pushMessages[$loosingBidder][] = $youLostBid;
        }
    }

    private function addYouWonItemMessage(Auction $auction, array &$pushMessages): void
    {
        $seller = $this->participantRepository->loadById($auction->getSellerId());
        if ($seller === null) {
            throw new RuntimeException('Seller details not found');
        }

        $youWonItem = new YouWonItem([
            'auction_id'   => $auction->getUuid(),
            'item'         => $auction->getItem(),
            'final_price'  => $auction->getHighestBidderPrice(),
            'seller_name'  => $seller->getName(),
            'seller_email' => $seller->getEmail(),
        ]);
        $pushMessages[$auction->getHighestBidderId()][] = $youWonItem;
    }

    private function addYouSoldItemMessage(Auction $auction, array &$pushMessages): void
    {
        $buyer = $this->participantRepository->loadById($auction->getHighestBidderId());
        if ($buyer === null) {
            throw new RuntimeException('Winning bidder details not found');
        }

        $youSoldItem = new YouSoldItem([
            'auction_id'  => $auction->getUuid(),
            'item'        => $auction->getItem(),
            'final_price' => $auction->getHighestBidderPrice(),
            'buyer_name'  => $buyer->getName(),
            'buyer_email' => $buyer->getEmail(),
        ]);
        $pushMessages[$auction->getSellerId()][] = $youSoldItem;
    }

    private function addYouDidNotSellItemMessage(Auction $auction, array &$pushMessages): void
    {
        $youDidNotSellItem = new YouDidNotSellItem([
            'auction_id'    => $auction->getUuid(),
            'item'          => $auction->getItem(),
            'final_price'   => $auction->getHighestBidderPrice(),
            'desired_price' => $auction->getDesiredPrice(),
        ]);
        $pushMessages[$auction->getSellerId()][] = $youDidNotSellItem;
    }

    public function getHandledMessageType(): MessageTypeEnum
    {
        return MessageTypeEnum::YOU_STARTED_AUCTION;
    }
}
