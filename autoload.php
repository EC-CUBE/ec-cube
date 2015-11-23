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

// magic_quotes_gpc = On の場合の対策
// see http://php.net/manual/ja/security.magicquotes.disabling.php
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

$autoload = __DIR__.'/vendor/autoload.php';

if (file_exists($autoload) && is_readable($autoload)) {
    $loader = require $autoload;
} else {
    die('Composer is not installed.');
}

// autoloader cache
if (extension_loaded('apc') && ini_get('apc.enabled')) {
    $apcLoader = new Symfony\Component\ClassLoader\ApcClassLoader('autoloader.', $loader);
    $apcLoader->register();
    $loader->unregister();
} elseif (extension_loaded('wincache') && ini_get('wincache.fcenabled')) {
    $winCacheLoader = new Symfony\Component\ClassLoader\WinCacheClassLoader('autoloader.', $loader);
    $winCacheLoader->register();
    $loader->unregister();
}

return $loader;
