# 發送驗證碼
透過簡訊或是Email服務發送驗證碼給使用者，藉以確認身份，並根據發送目的進行相對應的檢查，其中發送目的包含：註冊、變更手機號碼、變更Email、變更密碼等。

## 目錄
1. [工具](#工具)
2. [用法](#用法)

## 工具
1. **PHP**
    ```text
   8.1
    ```
   
2. **Laravel**
   ```text
   10
   ```

## 用法

```php
use App\Services\VerificationCode\SenderService;
use App\Services\VerificationCode\VerificationCodeDTO;

$senderService = new SenderService();

$verificationCodeDTO = new VerificationCodeDTO(
   type: VerificationCodeConstants::TYPE_SIGN_UP,
   mobile: 'your-mobile-number',
   socialData: [
      'token' => 'your-social-token'
   ],
   email: 'your-email',
   ip: 'your-ip'
);

$identification = $senderService->sendVerificationCode($verificationCodeDTO);
echo "Verification code sent. Identification: {$identification}";
