<?php

namespace App\VerificationCode\Services\VerificationCode;

class VerificationCodeDTO
{
    public int $type;
    public ?string $mobile;
    public ?array $socialData;
    public ?string $email;
    public string $ip;
    public bool $verified;
    public int $verifiedAt;
    public string $code;

    public function __construct(
        int     $type,
        ?string $mobile,
        ?array  $socialData,
        ?string $email,
        string  $ip,
        bool    $verified = false,
        int     $verifiedAt = 0,
        string  $code = ''
    )
    {
        $this->type = $type;
        $this->mobile = $mobile;
        $this->socialData = $socialData;
        $this->email = $email;
        $this->ip = $ip;
        $this->verified = $verified;
        $this->verifiedAt = $verifiedAt;
        $this->code = $code;
    }

    public function getDetail(): array
    {
        return [
            'type' => $this->type,
            'mobile' => $this->mobile,
            'socialData' => $this->socialData,
            'email' => $this->email,
            'ip' => $this->ip,
            'verified' => $this->verified,
            'verified_at' => $this->verifiedAt,
            'code' => $this->code,
        ];
    }
}