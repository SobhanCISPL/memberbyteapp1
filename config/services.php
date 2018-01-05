<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'facebook' => [
        'client_id' => '159875864623260',
        'client_secret' => '98ac2a675db3e8d756063aa0162edc66',
        'redirect' => 'http://memberbyteapp.localhost.com/',
    ],
    'google' => [
        'client_id'     => '18158992706-lj2bpjmc1s6jj0v6r7fma4ka3b3t7adt.apps.googleusercontent.com',
        'client_secret' => 'ET_frq0q1sme5pelfSNk_3Xq',
        'redirect'      => 'http://memberbyteapp.localhost.com',
    ],

];
