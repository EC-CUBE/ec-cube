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
                'http://blog-search.yahoo.co.jp/rss?p=%E3%83%AD%E3%83%83%E3%82%AF%E3%82%AA%E3%83%B3&ei=utf-8',
                'http://blogsearch.google.co.jp/blogsearch_feeds?hl=ja&q=%E3%83%AD%E3%83%83%E3%82%AF%E3%82%AA%E3%83%B3&lr=lang_ja&ie=utf-8&num=10&output=atom',
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