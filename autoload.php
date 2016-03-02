<?php

// システム要件チェック
if (version_compare(PHP_VERSION, '5.3.3') < 0) {
    die('Your PHP installation is too old. EC-CUBE requires at least PHP 5.3.3. See the <a href="http://www.ec-cube.net/product/system.php">system requirements</a> page for more information.');
}

if (extension_loaded('wincache')) {
    if (!ini_get('opcache.enable')) {
        ini_set('wincache.ocenabled', 1);
    }
    ini_set('wincache.fcenabled', 1);
}

$autoload = __DIR__.'/vendor/autoload.php';

if (file_exists($autoload) && is_readable($autoload)) {
    $loader = require $autoload;
} else {
    die('Composer is not installed.');
}



return $loader;
