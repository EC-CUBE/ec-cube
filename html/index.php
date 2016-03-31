<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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

require __DIR__.'/../autoload.php';

ini_set('display_errors', 'Off');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// see http://silex.sensiolabs.org/doc/web_servers.html#php-5-4
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app = \Eccube\Application::getInstance();

// インストールされてなければインストーラにリダイレクト
if ($app['config']['eccube_install']) {
    $app->initialize();
    $app->initializePlugin();
    $app->run();
} else {
    $location = str_replace('index.php', 'install.php', $_SERVER['SCRIPT_NAME']);
    header('Location:'.$location);
    exit;
}
