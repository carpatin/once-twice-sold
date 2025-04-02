<?php

declare(strict_types=1);

namespace OnceTwiceSold\MessageHandler\Handler;

use Closure;
use DateTime;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;
use OnceTwiceSold\Message\SellerToServer\StartAuction;
use OnceTwiceSold\Message\ServerToBidder\AuctionStarted;
use OnceTwiceSold\Message\ServerToSeller\YouStartedAuction;
use OnceTwiceSold\MessageHandler\MessageHandlerInterface;
use OnceTwiceSold\Model\Auction;
use OnceTwiceSold\Persistence\AuctionsRepository;
use OnceTwiceSold\WebSocketServer\Participants;

readonly class StartAuctionHandler implements MessageHandlerInterface
{
    public function __construct(
        private AuctionsRepository $auctionsRepository,
    ) {
        //
    }

    /**
     * @param StartAuction $message
     */
    public function handle(Participants $participants, AbstractMessage $message, Closure $pushCallback): void
    {
        $seller = $participants->getCurrentParticipant();
        $auction = Auction::createNewFromMessage($seller, $message);
        $this->auctionsRepository->persistAuction($auction);

        $pushMessages = [
            $seller => new YouStartedAuction([
                'auction_id'     => $auction->getUuid(),
                'title'          => $auction->getTitle(),
                'starting_price' => $auction->getStartingPrice(),
                'desired_price'  => $auction->getDesiredPrice(),
                'started_at'     => (new DateTime())
                    ->setTimestamp($auction->getStartedAt())->format(DateTime::ATOM),
                'ends_at'        => (new DateTime())
                    ->setTimestamp($auction->getEndsAt())->format(DateTime::ATOM),
            ]),
        ];

        foreach ($participants->getOtherParticipants() as $connection) {
            $pushMessages[$connection] = new AuctionStarted([
                'auction_id'     => $auction->getUuid(),
                'title'          => $auction->getTitle(),
                'starting_price' => $auction->getStartingPrice(),
                'started_at'     => (new DateTime())
                    ->setTimestamp($auction->getStartedAt())->format(DateTime::ATOM),
                'ends_at'        => (new DateTime())
                    ->setTimestamp($auction->getEndsAt())->format(DateTime::ATOM),
            ]);
        }
        $pushCallback($pushMessages);
    }

    public function getHandledMessageType(): MessageTypeEnum
    {
        return MessageTypeEnum::START_AUCTION;
    }
}
