<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

//[INFO]index.php,install.phpをEC-CUBEルート直下に移動させる場合は、コメントアウトしている行に置き換える
require __DIR__.'/../autoload.php';
//require __DIR__.'/autoload.php';

ini_set('display_errors', 'Off');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// see http://silex.sensiolabs.org/doc/web_servers.html#php-5-4
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$errorHandler = \Symfony\Component\Debug\ErrorHandler::register();
//@deprecated since 3.0.0, to be considered in 3.1 .
$errorLevel = E_ALL & ~E_NOTICE & ~E_USER_NOTICE & ~E_WARNING & E_USER_WARNING & ~E_STRICT & ~E_DEPRECATED & ~E_USER_DEPRECATED;
$errorHandler->throwAt($errorLevel, true);
\Eccube\Exception\EccubeExceptionHandler::register(false);

// output_config_php = true に設定することで、Config Yaml ファイルを元に Config PHP ファイルが出力されます。
// app/config/eccube, src/Eccube/Resource/config 以下に書き込み権限が必要です。
// Config PHP ファイルが存在する場合は、 Config Yaml より優先されます。
// Yaml ファイルをパースする必要が無いため、高速化が期待できます。
$app = \Eccube\Application::getInstance(array('output_config_php' => false));

// インストールされてなければインストーラにリダイレクト
if (isset($app['config']['eccube_install']) && $app['config']['eccube_install']) {
    $app->initialize();
    $app->initializePlugin();
    if ($app['config']['http_cache']['enabled']) {
        $app['http_cache']->run();
    } else {
        $app->run();
    }
} else {
    $location = str_replace('index.php', 'install.php', $_SERVER['SCRIPT_NAME']);
    header('Location:'.$location);
    exit;
}
