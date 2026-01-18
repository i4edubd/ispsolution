<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your SMS gateway settings here. Multiple gateways are supported.
    |
    */

    'enabled' => env('SMS_ENABLED', false),

    'default_gateway' => env('SMS_DEFAULT_GATEWAY', 'twilio'),

    /*
    |--------------------------------------------------------------------------
    | Twilio Configuration
    |--------------------------------------------------------------------------
    */
    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'from_number' => env('TWILIO_FROM_NUMBER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Nexmo/Vonage Configuration
    |--------------------------------------------------------------------------
    */
    'nexmo' => [
        'api_key' => env('NEXMO_API_KEY'),
        'api_secret' => env('NEXMO_API_SECRET'),
        'from_number' => env('NEXMO_FROM_NUMBER', 'ISP'),
    ],

    /*
    |--------------------------------------------------------------------------
    | BulkSMS Configuration
    |--------------------------------------------------------------------------
    */
    'bulksms' => [
        'username' => env('BULKSMS_USERNAME'),
        'password' => env('BULKSMS_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Bangladeshi SMS Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Generic configuration for local Bangladeshi SMS gateways
    |
    */
    'bangladeshi' => [
        'api_key' => env('BD_SMS_API_KEY'),
        'sender_id' => env('BD_SMS_SENDER_ID'),
        'api_url' => env('BD_SMS_API_URL'),
    ],

];
