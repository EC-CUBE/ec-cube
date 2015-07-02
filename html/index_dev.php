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

use Symfony\Component\Debug\Debug;
use Symfony\Component\Yaml\Yaml;


// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.

$allow = array(
    '127.0.0.1',
    'fe80::1',
    '::1',
);

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], $allow)
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}


require_once __DIR__.'/../vendor/autoload.php';

Debug::enable();

// load configs.
$app = new Eccube\Application();

// debug enable.
$app['debug'] = true;

// initialize servicies.
$app->initialize();
$app->initializePlugin();

// load config dev
$conf = $app['config'];
$app['config'] = $app->share(function () use($conf) {
    $confarray = array();
    $config_dev_file = __DIR__ . '/../app/config/eccube/config_dev.yml';
    if (file_exists($config_dev_file)) {
        $config_dev = Yaml::parse(file_get_contents($config_dev_file));
        if (isset($config_dev)) {
            $confarray = array_replace_recursive($confarray, $config_dev);
        }
    }
    return array_replace_recursive($conf, $confarray);
});


// Mail
if (isset($app['config']['delivery_address'])) {
    $app['mailer']->registerPlugin(new \Swift_Plugins_RedirectingPlugin($app['config']['delivery_address']));
}


// Silex Web Profiler
$app->register(new \Silex\Provider\WebProfilerServiceProvider(), array(
    'profiler.cache_dir' => __DIR__ . '/../app/cache/profiler',
    'profiler.mount_prefix' => '/_profiler',
));
$app->register(new \Saxulum\SaxulumWebProfiler\Provider\SaxulumWebProfilerProvider());



$app->run();
