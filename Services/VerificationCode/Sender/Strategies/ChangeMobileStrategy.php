<?php
/**
 * 發送驗證碼 — 變更手機
 */

namespace App\VerificationCode\Services\VerificationCode\Sender\Strategies;

class ChangeMobileStrategy implements SenderStrategyInterface
{
    private string $recipient;

    /**
     * @inheritDoc
     */
    public function sendVerificationCode(string $code): void
    {
        $message = $this->getMessage($code);
        $service = new SMSService();
        $service->sendSMS($this->recipient, $message);
    }

    private function getMessage(string $code): string
    {
        return 'Message context for changing mobile number';
    }

    /**
     * @inheritDoc
     */
    public function doBeforeSend(): void
    {
        // Check if mobile exist or not

        // Check member is active or not
    }

    /**
     * @inheritDoc
     */
    public function setRecipient(string $recipient): void
    {
        $this->recipient = $recipient;
    }
}
