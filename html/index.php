<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Eccube\Application(array(
    'env' => 'dev',
));
$app->run();
