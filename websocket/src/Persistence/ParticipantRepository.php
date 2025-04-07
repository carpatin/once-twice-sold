<?php

declare(strict_types=1);

namespace OnceTwiceSold\Persistence;

use OnceTwiceSold\Model\Participant;
use OpenSwoole\Table;

class ParticipantRepository
{
    private Table $table;

    public function initializeTable(): void
    {
        $this->table = new Table(100);
        $this->table->column('id', Table::TYPE_INT);
        $this->table->column('name', Table::TYPE_STRING, 30);
        $this->table->column('email', Table::TYPE_STRING, 50);

        $this->table->create();
    }

    public function persist(Participant $participant): void
    {
        $this->table->set((string)$participant->getId(), $participant->toTableRow());
    }

    public function loadById(int $participantId): ?Participant
    {
        if (!$this->table->exists((string)$participantId)) {
            return null;
        }
        $row = $this->table->get((string)$participantId);

        return Participant::createFromTableRow($participantId, $row);
    }
}