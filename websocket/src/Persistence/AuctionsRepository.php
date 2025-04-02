<?php

declare(strict_types=1);

namespace OnceTwiceSold\Persistence;

use OnceTwiceSold\Model\Auction;
use OpenSwoole\Table;

class AuctionsRepository
{
    private Table $auctionsTable;

    public function initializeTables(): void
    {
        $this->auctionsTable = new Table(100);
        $this->auctionsTable->column('seller_id', Table::TYPE_INT);
        $this->auctionsTable->column('highest_bidder_price', Table::TYPE_FLOAT);
        $this->auctionsTable->column('highest_bidder_id', Table::TYPE_INT);
        $this->auctionsTable->column('title', Table::TYPE_STRING, 20);
        $this->auctionsTable->column('description', Table::TYPE_STRING, 50);
        $this->auctionsTable->column('starting_price', Table::TYPE_FLOAT);
        $this->auctionsTable->column('desired_price', Table::TYPE_FLOAT);
        $this->auctionsTable->column('duration_seconds', Table::TYPE_INT);
        $this->auctionsTable->column('started_at', Table::TYPE_INT);
        $this->auctionsTable->column('ends_at', Table::TYPE_INT);

        $this->auctionsTable->create();
    }

    public function persistAuction(Auction $auction): void
    {
        $this->auctionsTable->set($auction->getUuid(), $auction->toTableRow());
    }

    public function loadAuction(string $auctionId): ?Auction
    {
        if (!$this->auctionsTable->exists($auctionId)) {
            return null;
        }

        $auctionRow = $this->auctionsTable->get($auctionId);

        return Auction::createFromTableRow($auctionId, $auctionRow);
    }

    public function loadAll(): array
    {
        $auctions = [];
        foreach ($this->auctionsTable as $auctionId => $auctionRow) {
            $auctions[] = Auction::createFromTableRow($auctionId, $auctionRow);
        }

        return $auctions;
    }
}
