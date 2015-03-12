<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/eccube/config.php';

$loader->add('Eccube\Tests', __DIR__);