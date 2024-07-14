<?php

namespace App\VerificationCode\Services\VerificationCode\Sender;

class SenderCodeContext
{
    private SenderStrategyInterface $strategy;

    public function setStrategy(SenderStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function executeStrategy($code): void
    {
        $this->strategy->doBeforeSend();
        $this->strategy->sendVerificationCode($code);
    }
}
