<?php

declare(strict_types=1);

namespace OnceTwiceSold\MessageHandler\Handler;

use Closure;
use DateTime;
use OnceTwiceSold\Auction\Auction;
use OnceTwiceSold\Auction\AuctionsManager;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;
use OnceTwiceSold\Message\SellerToServer\StartAuction;
use OnceTwiceSold\Message\ServerToSeller\YouStartedAuction;
use OnceTwiceSold\MessageHandler\MessageHandlerInterface;

class StartAuctionHandler implements MessageHandlerInterface
{
    public function __construct(
        private AuctionsManager $auctionsManager,
    ) {
        //
    }

    /**
     * @param StartAuction $message
     */
    public function handle(int $connection, AbstractMessage $message, Closure $pushCallback): void
    {
        $seller = $connection;
        $auction = Auction::createFromMessage($message);
        $this->auctionsManager->registerAuction($seller, $auction);

        $pushMessages = [
            $seller => new YouStartedAuction([
                'auction_id'     => $auction->getUuid(),
                'title'          => $auction->getTitle(),
                'starting_price' => $auction->getStartingPrice(),
                'desired_price'  => $auction->getDesiredPrice(),
                'started_at'     => (new DateTime())
                    ->setTimestamp($auction->getStartedAtTimestamp())->format(DateTime::ATOM),
                'ends_at'        => (new DateTime())
                    ->setTimestamp($auction->getEndsAtTimestamp())->format(DateTime::ATOM),
            ]),
        ];

        // TODO: foreach of the other connections send a AuctionStarted message

        $pushCallback($pushMessages);
    }

    public function getHandledMessageType(): MessageTypeEnum
    {
        return MessageTypeEnum::START_AUCTION;
    }
}