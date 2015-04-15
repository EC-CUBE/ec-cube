<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Eccube\Application();
$app['debug'] = true;
$app->run();
