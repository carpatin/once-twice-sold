<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message;

use JsonException;

abstract class AbstractMessage
{
    protected function __construct(
        private readonly MessageTypeEnum $type,
        protected array $data
    ) {
        //
    }

    public function getType(): MessageTypeEnum
    {
        return $this->type;
    }

    /**
     * @throws JsonException
     */
    public function toJson(): string
    {
        return json_encode([
            'type' => $this->type,
            'data' => $this->data,
        ], JSON_THROW_ON_ERROR);
    }
}
