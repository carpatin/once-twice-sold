<?php

declare(strict_types=1);

namespace OnceTwiceSold\Auction;

class AuctionsManager
{
    private array $auctionsByUuid = [];
    private array $auctionIdsBySellers = [];

    public function registerAuction(int $seller, Auction $auction): void
    {
        $this->auctionsByUuid[$auction->getUuid()] = $auction;
        $this->auctionIdsBySellers[$seller][] = $auction->getUuid();
    }
}
