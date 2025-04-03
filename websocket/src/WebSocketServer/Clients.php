<?php

declare(strict_types=1);

namespace OnceTwiceSold\WebSocketServer;

readonly class Clients
{
    public function __construct(
        public int $currentClient,
        public array $otherClients,
    ) {
        //
    }

    public function getCurrentClient(): int
    {
        return $this->currentClient;
    }

    public function getOtherClients(): array
    {
        return $this->otherClients;
    }

    public function getAllClients(): array
    {
        return [$this->currentClient, ... $this->otherClients];
    }
}
