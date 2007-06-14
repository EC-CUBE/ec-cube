<?php

$_ps = PATH_SEPARATOR;
$_include_path = ini_get('include_path') . $_ps . './' . $_ps . './Lib';

ini_set('include_path', $_include_path);

require_once('LLReader.php');

$config = array(
    'plugins' => array(
        'Subscription_Simple' => array(
            'urls' => array('http://feeds.feedburner.jp/cnet/rss')
        ),
        'Filter_SearchEntry2Feed' => array(
            'regex' => '/ライブドア/i'
        ),
    )
);
 
$LLR = new LLReader($config);
$LLR->run();

?>