<?php

// システム要件チェック
if (version_compare(PHP_VERSION, '5.3.9') < 0) {
    die('Your PHP installation is too old. EC-CUBE requires at least PHP 5.3.9. See the <a href="http://www.ec-cube.net/product/system.php">system requirements</a> page for more information.');
}

if (extension_loaded('wincache')) {
    if (!ini_get('opcache.enable')) {
        ini_set('wincache.ocenabled', 1);
    }
    ini_set('wincache.fcenabled', 1);
}

if (file_exists(__DIR__.'/vendor.phar')) {
    $loader = require  __DIR__.'/vendor.phar';
} elseif (file_exists(__DIR__.'/vendor/autoload.php')) {
    $loader = require __DIR__.'/vendor/autoload.php';
} else {
    die('Composer is not installed.');
}
$loader->addPsr4('Plugin\\', __DIR__ . '/app/Plugin');

// autoloader cache
if (extension_loaded('apc') && ini_get('apc.enabled')) {
    $apcLoader = new Symfony\Component\ClassLoader\ApcClassLoader(sha1(__FILE__), $loader);
    $apcLoader->register();
    $loader->unregister();
} elseif (extension_loaded('wincache') && ini_get('wincache.fcenabled')) {
    $winCacheLoader = new Symfony\Component\ClassLoader\WinCacheClassLoader(sha1(__FILE__), $loader);
    $winCacheLoader->register();
    $loader->unregister();
}

return $loader;
