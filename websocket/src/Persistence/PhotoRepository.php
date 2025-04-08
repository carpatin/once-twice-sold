<?php

declare(strict_types=1);

namespace OnceTwiceSold\Persistence;

use OnceTwiceSold\Model\Photos;
use OpenSwoole\Table;

class PhotoRepository
{
    private Table $table;

    public function initializeTable(): void
    {
        $this->table = new Table(100);
        $this->table->column('photo1', Table::TYPE_STRING, 10 * 1024 * 1024);
        $this->table->column('photo2', Table::TYPE_STRING, 10 * 1024 * 1024);
        $this->table->column('photo3', Table::TYPE_STRING, 10 * 1024 * 1024);

        $this->table->create();
    }

    public function persist(Photos $photos): void
    {
        $this->table->set($photos->getAuctionId(), $photos->toTableRow());
    }

    public function loadByAuctionId(string $id): ?Photos
    {
        if (!$this->table->exists($id)) {
            return null;
        }

        $photosRow = $this->table->get($id);

        return Photos::createFromTableRow($id, $photosRow);
    }

    public function removeByAuctionId(string $id): void
    {
        if (!$this->table->exists($id)) {
            $this->table->del($id);
        }
    }
}
