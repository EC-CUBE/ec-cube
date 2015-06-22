<?php
// https://github.com/litek/silex-skeleton/blob/master/autoload.php
$loader = require __DIR__ . '/vendor/autoload.php';

// APC autoloader cache
if (extension_loaded('apc')) {
    require __DIR__ . '/vendor/symfony/class-loader/Symfony/Component/ClassLoader/ApcClassLoader.php';
    $apcLoader = new Symfony\Component\ClassLoader\ApcClassLoader('autoloader.', $loader);
    $apcLoader->register();
    $loader->unregister();
}

return $loader;
