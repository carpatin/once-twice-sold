<?php

declare(strict_types=1);

namespace OnceTwiceSold\MessageHandler\Handler;

use Closure;
use OnceTwiceSold\Message\AbstractMessage;
use OnceTwiceSold\Message\MessageTypeEnum;
use OnceTwiceSold\Message\SellerToServer\AddItemPhoto;
use OnceTwiceSold\Message\ServerToAll\ItemPhotoAdded;
use OnceTwiceSold\MessageHandler\MessageHandlerInterface;
use OnceTwiceSold\Model\Photos;
use OnceTwiceSold\Persistence\AuctionRepository;
use OnceTwiceSold\Persistence\PhotoRepository;
use OnceTwiceSold\WebSocketServer\Clients;
use RuntimeException;

readonly class AddItemPhotoHandler implements MessageHandlerInterface
{
    public function __construct(
        private AuctionRepository $auctionRepository,
        private PhotoRepository $photosRepository,
    ) {
        //
    }

    public function handle(Clients $clients, AbstractMessage $message, Closure $pushCallback): void
    {
        /** @var AddItemPhoto $message */
        $auction = $this->auctionRepository->loadById($message->getAuctionId());
        if (null === $auction) {
            throw new RuntimeException('Auction not found');
        }

        $photos = $this->photosRepository->loadByAuctionId($auction->getUuid());
        if (null === $photos) {
            $photos = new Photos($auction->getUuid());
        }
        $base64Photo = $message->getPhoto();
        $isAdded = $photos->addPhoto($base64Photo);

        if (!$isAdded) {
            return;
        }

        // persist photos data for the current auction
        $this->photosRepository->persist($photos);

        // send item_photo_added message to everyone
        $pushMessages = [];
        foreach ($clients->getOtherClients() as $client) {
            $pushMessages[$client][] = new ItemPhotoAdded([
                'auction_id' => $auction->getUuid(),
                'item'       => $auction->getItem(),
                'photo'      => $base64Photo,
            ]);
        }
        $pushCallback($pushMessages);
    }

    public function getHandledMessageType(): MessageTypeEnum
    {
        return MessageTypeEnum::ADD_ITEM_PHOTO;
    }
}
