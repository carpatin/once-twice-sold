<?php

declare(strict_types=1);

namespace OnceTwiceSold\Model;

readonly class Participant
{
    public function __construct(
        private int $id,
        private string $name,
        private string $email,
    ) {
        //
    }

    public static function createFromTableRow(int $participantId, array $participantRow): self
    {
        return new self(
            $participantId,
            $participantRow['name'],
            $participantRow['email'],
        );
    }

    public function toTableRow(): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
