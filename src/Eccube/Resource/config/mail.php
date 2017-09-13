<?php return [
  'mail' => 
  [
    'transport' => '${MAIL_BACKEND}',
    'host' => '${MAIL_HOST}',
    'port' => '${MAIL_PORT}',
    'username' => '${MAIL_USER}',
    'password' => '${MAIL_PASS}',
    'encryption' => NULL,
    'auth_mode' => NULL,
    'charset_iso_2022_jp' => false,
    'spool' => false,
  ],
];
