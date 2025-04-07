<?php

declare(strict_types=1);

namespace OnceTwiceSold\Persistence;

use OnceTwiceSold\Model\Photos;
use OpenSwoole\Table;

class PhotoRepository
{
    private Table $photosTable;

    public function initializeTable(): void
    {
        $this->photosTable = new Table(100);
        $this->photosTable->column('photo1', Table::TYPE_STRING, 10 * 1024 * 1024);
        $this->photosTable->column('photo2', Table::TYPE_STRING, 10 * 1024 * 1024);
        $this->photosTable->column('photo3', Table::TYPE_STRING, 10 * 1024 * 1024);

        $this->photosTable->create();
    }

    public function persist(Photos $photos): void
    {
        $this->photosTable->set($photos->getAuctionId(), $photos->toTableRow());
    }

    public function loadByAuctionId(string $id): ?Photos
    {
        if (!$this->photosTable->exists($id)) {
            return null;
        }

        $photosRow = $this->photosTable->get($id);

        return Photos::createFromTableRow($id, $photosRow);
    }
}
