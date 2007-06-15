<?php

$_path1 = '/home/web/dev.ec-cube.net/html/test/adachi/PLLagger';
$_path2 = '/home/web/dev.ec-cube.net/html/test/adachi/PLLagger/Lib';

$_ps = PATH_SEPARATOR;
$_include_path = ini_get('include_path') . $_ps . './' . $_ps . './Lib';

ini_set('include_path', $_include_path);

require_once('PLLagger.php');

$config = array(
    'plugins' => array(
        'Subscription_Simple' => array(
            'urls' => array(
                'http://feeds.feedburner.jp/cnet/rss', //cnet
                'http://rss.rssad.jp/rss/itm/rss.xml', //@IT
                'http://rss.rssad.jp/rss/itm/1.0/topstory.xml' //ITmedia
            )
        ),
        'Filter_SearchEntry2Feed' => array(
            'regex' => '/ロックオン/i'
        ),
    )
);
 
$LLR = new PLLagger($config);
$LLR->run();

?>