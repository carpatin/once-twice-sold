<?php

declare(strict_types=1);

namespace OnceTwiceSold\WebSocketServer;

readonly class Participants
{
    public function __construct(
        public int $currentParticipant,
        public array $otherParticipants,
    ) {
        //
    }

    public function getCurrentParticipant(): int
    {
        return $this->currentParticipant;
    }

    public function getOtherParticipants(): array
    {
        return $this->otherParticipants;
    }

    public function getAllParticipants(): array
    {
        return [$this->currentParticipant, ... $this->otherParticipants];
    }
}
