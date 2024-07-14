<?php

namespace App\VerificationCode\Services\VerificationCode\Sender;

interface SenderStrategyInterface
{
    public function sendVerificationCode(string $code): void;

    public function doBeforeSend(): void;

    public function setRecipient(string $recipient): void;
}
