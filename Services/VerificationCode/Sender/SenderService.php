<?php

namespace App\VerificationCode\Services\VerificationCode\Sender;

class SenderService
{
    private const IP_LIMIT_PER_DAY = 10;
    private const SAME_LIMIT_PER_MINUTES = 5;
    private SenderCodeContext $context;

    public function __construct(SenderCodeContext $context)
    {
        $this->context = $context;
    }

    public function sendVerificationCode(VerificationCodeDTO $verificationCodeDTO): string
    {
        $this->handlePreSend($verificationCodeDTO);

        $verificationCodeDTO->code = $this->generateVerificationCode();
        $identification = $this->generateUniqueIdentification();
        $this->storeVerificationDetails($identification, $verificationCodeDTO);

        $strategy = $this->getStrategy($verificationCodeDTO);
        $this->context->setStrategy($strategy);
        $this->context->executeStrategy($verificationCodeDTO->code);

        $this->handlePostSend($verificationCodeDTO);

        return $identification;
    }

    private function getStrategy(VerificationCodeDTO $verificationCodeDTO): SenderStrategyInterface
    {
        switch ($verificationCodeDTO->type) {
            case VerificationCodeConstants::TYPE_SIGN_UP:
                $strategy = new SignUpStrategy();
                $strategy->setRecipient($verificationCodeDTO->mobile);
                break;
            case VerificationCodeConstants::TYPE_FORGET_PASSWORD:
                $strategy = new ForgetPasswordStrategy();
                $strategy->setRecipient($verificationCodeDTO->mobile);
                break;
            case VerificationCodeConstants::TYPE_SOCIAL_SIGN_UP:
                $strategy = new SocialSignUpStrategy();
                $strategy->setRecipient($verificationCodeDTO->mobile);
                break;
            case VerificationCodeConstants::TYPE_CHANGE_PASSWORD:
                $strategy = new ChangePasswordStrategy();
                $strategy->setRecipient($verificationCodeDTO->mobile);
                break;
            case VerificationCodeConstants::TYPE_CHANGE_EMAIL:
                $strategy = new ChangeEmailStrategy();
                $strategy->setRecipient($verificationCodeDTO->email);
                break;
            case VerificationCodeConstants::TYPE_CHANGE_MOBILE:
                $strategy = new ChangeMobileStrategy();
                $strategy->setRecipient($verificationCodeDTO->mobile);
                break;
            default:
                throw new CustomException('Error Code');
        }

        return $strategy;
    }

    private function handlePostSend(VerificationCodeDTO $verificationCodeDTO): void
    {
        $this->incrementIpSentCount($verificationCodeDTO->ip);
        $this->updateLastSentTime($verificationCodeDTO->mobile, $verificationCodeDTO->email);
    }

    private function updateLastSentTime(?string $mobile, ?string $email): void
    {
        if ($mobile) {
            $this->updateRecipientLastSentTime($mobile);
        }
        if ($email) {
            $this->updateRecipientLastSentTime($email);
        }
    }

    private function updateRecipientLastSentTime(string $recipient): void
    {
        $key = $this->getKeyOfLastSent($recipient);
        Redis::set($key, time());
    }

    private function incrementIpSentCount(string $ip): void
    {
        $key = $this->getKeyOfRequestIp($ip);
        if (is_null(Redis::get($key))) {
            Redis::setex($key, 86400, 1);
        } else {
            Redis::incr($key);
        }
    }

    private function storeVerificationDetails(string $identification, VerificationCodeDTO $verificationCodeDTO): void
    {
        Redis::setex(
            VerificationCodeConstants::REDIS_KEY_IDENTIFICATION . $identification,
            self::SAME_LIMIT_PER_MINUTES * 60,
            json_encode($verificationCodeDTO->getDetail())
        );
    }

    private function generateUniqueIdentification(): string
    {
        return 'Unique identification';
    }

    private function generateVerificationCode(): string
    {
        return 'VerificationController code';
    }

    private function handlePreSend(VerificationCodeDTO $verificationCodeDTO): void
    {
        $this->checkIpLimit($verificationCodeDTO->ip);
        $this->checkRecipientLimit($verificationCodeDTO->mobile, $verificationCodeDTO->email);
    }

    private function checkIpLimit(string $ip): void
    {
        $key = $this->getKeyOfRequestIp($ip);
        $count = Redis::get($key);

        if ($count >= self::IP_LIMIT_PER_DAY) {
            throw new CustomException('Error Code');
        }
    }

    private function getKeyOfRequestIp(string $ip): string
    {
        return 'Redis key of ip';
    }

    private function checkRecipientLimit(?string $mobile, ?string $email): void
    {
        if ($mobile) {
            $this->checkRecipient($mobile);
        }
        if ($email) {
            $this->checkRecipient($email);
        }
    }

    private function checkRecipient(string $recipient): void
    {
        $key = $this->getKeyOfLastSent($recipient);
        $lastSent = Redis::get($key);

        if ($lastSent && time() - $lastSent < self::SAME_LIMIT_PER_MINUTES * 60) {
            throw new CustomException('Error Code');
        }
    }

    private function getKeyOfLastSent(string $recipient): string
    {
        return 'Redis key of last sending time';
    }
}
