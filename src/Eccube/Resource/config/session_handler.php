<?php return [
  'session_handler' =>
  [
    // PHP/PHP拡張のセッションハンドラを利用する場合, trueに設定します。
    'enabled' => false,

    // memcacheの設定例
    //'save_handler' => 'memcache',
    //'save_path' => '127.0.0.1:11211',

    // memcachedの設定例
    //'save_handler' => 'memcached',
    //'save_path' => '127.0.0.1:11211',

    // redisの設定例
    //'save_handler' => 'redis',
    //'save_path' => '127.0.0.1:6379',
  ],
];
