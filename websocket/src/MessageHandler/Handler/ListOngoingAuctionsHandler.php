<?php

declare(strict_types=1);

namespace OnceTwiceSold\MessageHandler\Handler;

use Closure;
use DateTime;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\BidderToServer\ListOngoingAuctions;
use OnceTwiceSold\Message\MessageTypeEnum;
use OnceTwiceSold\Message\ServerToBidder\OngoingAuctions;
use OnceTwiceSold\MessageHandler\MessageHandlerInterface;
use OnceTwiceSold\Model\Auction;
use OnceTwiceSold\Persistence\AuctionRepository;
use OnceTwiceSold\WebSocketServer\Clients;

readonly class ListOngoingAuctionsHandler implements MessageHandlerInterface
{
    public function __construct(
        private AuctionRepository $auctionsRepository,
    ) {
        //
    }

    public function handle(Clients $clients, AbstractMessage $message, Closure $pushCallback): void
    {
        /** @var $message ListOngoingAuctions */
        $auctions = $this->auctionsRepository->loadAll();

        $auctionsArray = [];
        /** @var Auction $auction */
        foreach ($auctions as $auction) {
            // skip closed suctions
            if ($auction->getState() === Auction::STATE_CLOSED) {
                continue;
            }

            $auctionsArray[] = [
                'auction_id'     => $auction->getUuid(),
                'item'           => $auction->getItem(),
                'starting_price' => $auction->getStartingPrice(),
                'started_at'     => (new DateTime())
                    ->setTimestamp($auction->getStartedAt())->format(DateTime::ATOM),
                'ends_at'        => (new DateTime())
                    ->setTimestamp($auction->getEndsAt())->format(DateTime::ATOM),
            ];
        }

        // preparing the response message
        $ongoingAuctionsMessage = new OngoingAuctions([
            'auctions' => $auctionsArray,
        ]);

        // calling the push callback passing the message for the current participant only
        $pushCallback([
            $clients->getCurrentClient() => $ongoingAuctionsMessage,
        ]);
    }

    public function getHandledMessageType(): MessageTypeEnum
    {
        return MessageTypeEnum::LIST_ONGOING_AUCTIONS;
    }
}
