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

$baseDir = '../';
$checkLogFile = $baseDir.'app'.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.'install.log';
ini_set('display_errors', 'Off');
if (!is_writable($checkLogFile)) {
    die('app/log/install.log をウェブサーバーから書き込めるようにしてください');
}

if (function_exists('apc_clear_cache')) {
    apc_clear_cache('user');
    apc_clear_cache();
}

require __DIR__ . '/../autoload.php';

$app = new Eccube\InstallApplication();
$app['debug'] = true;
$app->before(function (\Symfony\Component\HttpFoundation\Request $request, \Silex\Application $app) {
    if (!$request->getSession()->isStarted()) {
        $request->getSession()->start();
    }
});
$app->run();
