<?php

namespace App\Message;

final class SendConfirmationAccountLinkMessage
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

    private $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
