<?php

namespace App\Http\Controllers;

class VerificationController
{
    private SenderService $service;

    public function __construct(VerificationResuest $request, SenderService $service)
    {
        $needVerifyType = [
            VerificationCodeConstants::TYPE_CHANGE_PASSWORD,
            VerificationCodeConstants::TYPE_CHANGE_EMAIL,
            VerificationCodeConstants::TYPE_CHANGE_MOBILE,
        ];
        if (in_array($request->post('type'), $needVerifyType)) {
            $this->middleware('token.check');
        }

        $this->service = $service;
    }

    public function __invoke(VerificationResuest $request)
    {
        try {
            $verificationCodeDTO = new VerificationCodeDTO(
                type: $request->post('type'),
                mobile: $request->post('mobile'),
                socialData: $request->post('social_data'),
                email: $request->post('email'),
                ip: $request->getClientIp()
            );

            $this->service->sendVerificationCode($verificationCodeDTO);
            return response()->json(['status' => 0]);
        } catch (Exception $e) {
            return response()->json(['status' => $e->getMessage()]);
        }
    }
}