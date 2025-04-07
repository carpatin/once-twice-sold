<?php

declare(strict_types=1);

namespace OnceTwiceSold\Persistence;

use OnceTwiceSold\Model\Auction;
use OpenSwoole\Table;

class AuctionRepository
{
    private Table $auctionsTable;

    public function initializeTable(): void
    {
        $this->auctionsTable = new Table(100);
        $this->auctionsTable->column('state', Table::TYPE_STRING, 6);
        $this->auctionsTable->column('verdict', Table::TYPE_STRING, 6);
        $this->auctionsTable->column('seller_id', Table::TYPE_INT);
        $this->auctionsTable->column('all_bidders_ids', Table::TYPE_STRING, 300);
        $this->auctionsTable->column('highest_bidder_price', Table::TYPE_FLOAT);
        $this->auctionsTable->column('highest_bidder_id', Table::TYPE_INT);
        $this->auctionsTable->column('item', Table::TYPE_STRING, 30);
        $this->auctionsTable->column('description', Table::TYPE_STRING, 100);
        $this->auctionsTable->column('starting_price', Table::TYPE_FLOAT);
        $this->auctionsTable->column('desired_price', Table::TYPE_FLOAT);
        $this->auctionsTable->column('duration_seconds', Table::TYPE_INT);
        $this->auctionsTable->column('started_at', Table::TYPE_INT);
        $this->auctionsTable->column('ends_at', Table::TYPE_INT);

        $this->auctionsTable->create();
    }

    public function persist(Auction $auction): void
    {
        $this->auctionsTable->set($auction->getUuid(), $auction->toTableRow());
    }

    public function loadById(string $id): ?Auction
    {
        if (!$this->auctionsTable->exists($id)) {
            return null;
        }

        $auctionRow = $this->auctionsTable->get($id);

        return Auction::createFromTableRow($id, $auctionRow);
    }

    public function loadAll(): array
    {
        $auctions = [];
        foreach ($this->auctionsTable as $auctionId => $auctionRow) {
            $auctions[$auctionRow['started_at']] = Auction::createFromTableRow($auctionId, $auctionRow);
        }

        krsort($auctions);

        return array_values($auctions);
    }
}
