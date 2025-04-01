<?php

declare(strict_types=1);

namespace OnceTwiceSold\Auction;

use OnceTwiceSold\Message\SellerToServer\StartAuction;

class Auction
{
    private string $uuid;
    private int $startedAtTimestamp;
    private int $endsAtTimestamp;
    private ?float $highestBidAmount;
    private ?int $highestBidIdentifier;

    public function __construct(
        private string $title,
        private string $description,
        private float $startingPrice,
        private float $desiredPrice,
        private int $durationSeconds,

    ) {
        $this->uuid = uuid_create();
        $this->startedAtTimestamp = time();
        $this->endsAtTimestamp = $this->startedAtTimestamp + $this->durationSeconds;
        $this->highestBidAmount = null;
        $this->highestBidIdentifier = null;
    }

    public static function createFromMessage(StartAuction $message): Auction
    {
        return new Auction(
            $message->getTitle(),
            $message->getDescription(),
            $message->getStartingPrice(),
            $message->getDesiredPrice(),
            $message->getDurationSeconds(),
        );
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getStartedAtTimestamp(): int
    {
        return $this->startedAtTimestamp;
    }

    public function getEndsAtTimestamp(): int
    {
        return $this->endsAtTimestamp;
    }

    public function getHighestBidAmount(): ?float
    {
        return $this->highestBidAmount;
    }

    public function getHighestBidIdentifier(): ?int
    {
        return $this->highestBidIdentifier;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStartingPrice(): float
    {
        return $this->startingPrice;
    }

    public function getDesiredPrice(): float
    {
        return $this->desiredPrice;
    }

    public function getDurationSeconds(): int
    {
        return $this->durationSeconds;
    }
}