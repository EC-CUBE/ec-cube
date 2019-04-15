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

if (function_exists('apc_clear_cache')) {
    apc_clear_cache('user');
    apc_clear_cache();
}

//[INFO]index.php,install.phpをEC-CUBEルート直下に移動させる場合は、コメントアウトしている行に置き換える
require __DIR__ . '/../autoload.php';
//require __DIR__ . '/autoload.php';

$app = new Eccube\InstallApplication();
$app['debug'] = true;
$app->before(function (\Symfony\Component\HttpFoundation\Request $request, \Silex\Application $app) {
    if (!$request->getSession()->isStarted()) {
        $request->getSession()->start();
    }
});
$app->run();
