<?php

declare(strict_types=1);

namespace OnceTwiceSold\MessageHandler\Handler;

use Closure;
use DateTime;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;
use OnceTwiceSold\Message\SellerToServer\StartAuction;
use OnceTwiceSold\Message\ServerToAll\AuctionStarted;
use OnceTwiceSold\Message\ServerToSeller\YouStartedAuction;
use OnceTwiceSold\MessageHandler\MessageHandlerInterface;
use OnceTwiceSold\Model\Auction;
use OnceTwiceSold\Model\Participant;
use OnceTwiceSold\Persistence\AuctionRepository;
use OnceTwiceSold\Persistence\ParticipantRepository;
use OnceTwiceSold\WebSocketServer\Clients;

readonly class StartAuctionHandler implements MessageHandlerInterface
{
    public function __construct(
        private AuctionRepository $auctionsRepository,
        private ParticipantRepository $participantRepository,
    ) {
        //
    }

    /**
     * @param StartAuction $message
     */
    public function handle(Clients $clients, AbstractMessage $message, Closure $pushCallback): void
    {
        $sellerClientId = $clients->getCurrentClient();

        // persist seller details
        $seller = new Participant($sellerClientId, $message->getSellerName(), $message->getSellerEmail());
        $this->participantRepository->persist($seller);

        // persist new auction
        $auction = Auction::createNewFromMessage($sellerClientId, $message);
        $this->auctionsRepository->persist($auction);

        $pushMessages = [
            $sellerClientId => new YouStartedAuction([
                'auction_id'     => $auction->getUuid(),
                'item'           => $auction->getItem(),
                'starting_price' => $auction->getStartingPrice(),
                'desired_price'  => $auction->getDesiredPrice(),
                'started_at'     => (new DateTime())
                    ->setTimestamp($auction->getStartedAt())->format(DateTime::ATOM),
                'ends_at'        => (new DateTime())
                    ->setTimestamp($auction->getEndsAt())->format(DateTime::ATOM),
            ]),
        ];

        foreach ($clients->getOtherClients() as $connection) {
            $pushMessages[$connection] = new AuctionStarted([
                'auction_id'     => $auction->getUuid(),
                'item'           => $auction->getItem(),
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
