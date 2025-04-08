<?php

declare(strict_types=1);

namespace OnceTwiceSold\Model;

use OnceTwiceSold\Message\SellerToServer\StartAuction;

class Auction
{
    public const string STATE_OPEN   = 'open';
    public const string STATE_CLOSED = 'closed';

    public const string VERDICT_SOLD   = 'sold';
    public const string VERDICT_UNSOLD = 'unsold';

    public function __construct(
        private string $uuid,
        private string $state, // open, closed
        private string $verdict, // sold, unsold
        private int $sellerId,
        private string $item,
        private string $description,
        private float $startingPrice,
        private float $desiredPrice,
        private int $startedAt,
        private int $endsAt,
        private float $highestBidderPrice = 0,
        private int $highestBidderId = 0,
        private string $allBiddersIds = '',
    ) {
        //
    }

    public static function createNewFromMessage(int $seller, StartAuction $message): self
    {
        $startedAtTimestamp = time();
        $endsAtTimestamp = $startedAtTimestamp + $message->getDurationSeconds();

        return new Auction(
            uuid_create(),
            self::STATE_OPEN,
            self::VERDICT_UNSOLD,
            $seller,
            $message->getItem(),
            $message->getDescription(),
            $message->getStartingPrice(),
            $message->getDesiredPrice(),
            $startedAtTimestamp,
            $endsAtTimestamp,
        );
    }

    public static function createFromTableRow(string $auctionId, array $auctionRow): self
    {
        return new Auction(
            $auctionId,
            $auctionRow['state'],
            $auctionRow['verdict'],
            $auctionRow['seller_id'],
            $auctionRow['item'],
            $auctionRow['description'],
            $auctionRow['starting_price'],
            $auctionRow['desired_price'],
            $auctionRow['started_at'],
            $auctionRow['ends_at'],
            $auctionRow['highest_bidder_price'],
            $auctionRow['highest_bidder_id'],
            $auctionRow['all_bidders_ids'],
        );
    }

    public function toTableRow(): array
    {
        return [
            'state'                => $this->state,
            'verdict'              => $this->verdict,
            'seller_id'            => $this->sellerId,
            'item'                 => $this->item,
            'description'          => $this->description,
            'starting_price'       => $this->startingPrice,
            'desired_price'        => $this->desiredPrice,
            'started_at'           => $this->startedAt,
            'ends_at'              => $this->endsAt,
            'highest_bidder_price' => $this->highestBidderPrice,
            'highest_bidder_id'    => $this->highestBidderId,
            'all_bidders_ids'      => $this->allBiddersIds,
        ];
    }

    public function placeBid(int $bidderId, float $amount): bool
    {
        // register bidder
        $biddersIdsArray = $this->getAllBiddersIds();
        $biddersIdsArray = array_unique([...$biddersIdsArray, $bidderId]);
        $this->allBiddersIds = implode(',', $biddersIdsArray);

        // check if current bid becomes highest
        if ($amount > $this->highestBidderPrice) {
            $this->highestBidderPrice = $amount;
            $this->highestBidderId = $bidderId;

            return true;
        }

        return false;
    }

    public function getLoosingBiddersIds(): array
    {
        $biddersIdsArray = $this->getAllBiddersIds();
        unset($biddersIdsArray[array_search($this->highestBidderId, $biddersIdsArray, true)]);

        return $biddersIdsArray;
    }


    public function getAllBiddersIds(): array
    {
        $biddersIds = $this->allBiddersIds ?? '';
        $biddersIdsArray = array_filter(explode(',', $biddersIds));

        return array_map(static fn ($id) => (int)$id, $biddersIdsArray);
    }

    public function closeAuction(): void
    {
        $this->state = self::STATE_CLOSED;
    }

    public function setVerdictToUnsold(): void
    {
        $this->verdict = self::VERDICT_UNSOLD;
    }

    public function obtainVerdict(): string
    {
        $this->verdict = ($this->highestBidderPrice >= $this->desiredPrice) ? self::VERDICT_SOLD : self::VERDICT_UNSOLD;

        return $this->verdict;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getVerdict(): string
    {
        return $this->verdict;
    }

    public function getSellerId(): int
    {
        return $this->sellerId;
    }

    public function getItem(): string
    {
        return $this->item;
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

    public function getStartedAt(): int
    {
        return $this->startedAt;
    }

    public function getEndsAt(): int
    {
        return $this->endsAt;
    }

    public function getHighestBidderPrice(): float
    {
        return $this->highestBidderPrice;
    }

    public function getHighestBidderId(): int
    {
        return $this->highestBidderId;
    }
}
