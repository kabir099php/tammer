<?php

return [
    'merchant_id' => env('EDFAPAY_MERCHANT_ID'),
    'merchant_password' => env('EDFAPAY_MERCHANT_PASSWORD'),
    'api_base_url' => env('EDFAPAY_API_BASE_URL', 'https://sandbox.edfapay.com/payment'),
    'callback_url' => env('EDFAPAY_CALLBACK_URL', env('APP_URL') . '/edfapay/callback'),
];