<?php return [
    'mail' => [
        'transport' => env('MAIL_TRANSPORT', 'smtp'),
        'host' => env('MAIL_HOST', '127.0.0.1'),
        'port' => env('MAIL_PORT', '1025'),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'encryption' => env('MAIL_ENCRYPTION'),
        'auth_mode' => env('MAIL_AUTH_MODE'),
        'charset_iso_2022_jp' => env('MAIL_CHARSET_ISO_2022_JP', false),
        'spool' => env('MAIL_SPOOL', false),
    ],
];
