<?php
// https://github.com/litek/silex-skeleton/blob/master/autoload.php
$loader = require __DIR__ . '/vendor/autoload.php';

// APC autoloader cache
if (extension_loaded('apc') && ini_get('apc.enabled')) {
    $apcLoader = new Symfony\Component\ClassLoader\ApcClassLoader('autoloader.', $loader);
    $apcLoader->register();
    $loader->unregister();
}

return $loader;
