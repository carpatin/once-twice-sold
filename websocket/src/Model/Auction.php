<?php

declare(strict_types=1);

namespace OnceTwiceSold\Model;

use OnceTwiceSold\Message\SellerToServer\StartAuction;

class Auction
{
    public function __construct(
        private string $uuid,
        private int $sellerId,
        private string $title,
        private string $description,
        private float $startingPrice,
        private float $desiredPrice,
        private int $startedAt,
        private int $endsAt,
        private ?float $highestBidderPrice,
        private ?int $highestBidderId,

    ) {
        //
    }

    public static function createNewFromMessage(int $seller, StartAuction $message): self
    {
        $startedAtTimestamp = time();
        $endsAtTimestamp = $startedAtTimestamp + $message->getDurationSeconds();

        return new Auction(
            uuid_create(),
            $seller,
            $message->getTitle(),
            $message->getDescription(),
            $message->getStartingPrice(),
            $message->getDesiredPrice(),
            $startedAtTimestamp,
            $endsAtTimestamp,
            null,
            null
        );
    }

    public static function createFromTableRow(string $auctionId, array $auctionRow): self
    {
        return new Auction(
            $auctionId,
            $auctionRow['seller_id'],
            $auctionRow['title'],
            $auctionRow['description'],
            $auctionRow['starting_price'],
            $auctionRow['desired_price'],
            $auctionRow['started_at'],
            $auctionRow['ends_at'],
            $auctionRow['highest_bidder_price'],
            $auctionRow['highest_bidder_id'],
        );
    }

    public function toTableRow(): array
    {
        return [
            'uuid'                 => $this->uuid,
            'seller_id'            => $this->sellerId,
            'title'                => $this->title,
            'description'          => $this->description,
            'starting_price'       => $this->startingPrice,
            'desired_price'        => $this->desiredPrice,
            'started_at'           => $this->startedAt,
            'ends_at'              => $this->endsAt,
            'highest_bidder_price' => $this->highestBidderPrice,
            'highest_bidder_id'    => $this->highestBidderId,
        ];
    }

    public function placeBid(int $bidder, float $amount): bool
    {
        if ($amount > $this->highestBidderPrice) {
            $this->highestBidderPrice = $amount;
            $this->highestBidderId = $bidder;

            return true;
        }

        return false;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getSellerId(): int
    {
        return $this->sellerId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStartingPrice(): float
    {
        return $this->startingPrice;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDesiredPrice(): float
    {
        return $this->desiredPrice;
    }

    public function getStartedAt(): int
    {
        return $this->startedAt;
    }

    public function getEndsAt(): int
    {
        return $this->endsAt;
    }

    public function getHighestBidderPrice(): ?float
    {
        return $this->highestBidderPrice;
    }

    public function getHighestBidderId(): ?int
    {
        return $this->highestBidderId;
    }
}