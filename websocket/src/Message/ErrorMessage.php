<?php

declare(strict_types=1);

namespace OnceTwiceSold\Message;

use Exception;

class ErrorMessage extends AbstractMessage
{
    protected function __construct(array $data)
    {
        parent::__construct(MessageTypeEnum::ERROR, $data);
    }

    public static function createForException(Exception $ex): ErrorMessage
    {
        return new self(['message' => $ex->getMessage(), 'code' => $ex->getCode()]);
    }
}
