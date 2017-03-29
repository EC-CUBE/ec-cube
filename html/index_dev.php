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

//[INFO]index.php,install.phpをEC-CUBEルート直下に移動させる場合は、コメントアウトしている行に置き換える
require_once __DIR__.'/../autoload.php';
//require_once __DIR__.'/autoload.php';

Debug::enable();

// see http://silex.sensiolabs.org/doc/web_servers.html#php-5-4
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

// output_config_php = true に設定することで、Config Yaml ファイルを元に Config PHP ファイルが出力されます。
// app/config/eccube, src/Eccube/Resource/config 以下に書き込み権限が必要です。
// Config PHP ファイルが存在する場合は、 Config Yaml より優先されます。
// Yaml ファイルをパースする必要が無いため、高速化が期待できます。
$app = \Eccube\Application::getInstance(array('output_config_php' => false));

// debug enable.
$app['debug'] = true;

// initialize servicies.
$app->initialize();
$app->initializePlugin();

// load config dev
$conf = $app['config'];
$app['config'] = $app->share(function () use ($conf) {
    $confarray = array();
    $config_dev_file = __DIR__.'/../app/config/eccube/config_dev.yml';
    if (file_exists($config_dev_file)) {
        $config_dev = Yaml::parse(file_get_contents($config_dev_file));
        if (isset($config_dev)) {
            $confarray = array_replace_recursive($confarray, $config_dev);
        }
    }

    return array_replace_recursive($conf, $confarray);
});
// config_dev.ymlにmailが設定されていた場合、config_dev.ymlの設定内容を反映
$app['swiftmailer.options'] = $app['config']['mail'];

if (isset($app['config']['mail']['use_spool']) && is_bool($app['config']['mail']['use_spool'])) {
    $app['swiftmailer.use_spool'] = $app['config']['mail']['use_spool'];
}

// Mail
if (isset($app['config']['delivery_address'])) {
    $app['mailer']->registerPlugin(new \Swift_Plugins_RedirectingPlugin($app['config']['delivery_address']));
}

// Silex Web Profiler
$app->register(new \Silex\Provider\WebProfilerServiceProvider(), array(
    'profiler.cache_dir' => __DIR__.'/../app/cache/profiler',
    'profiler.mount_prefix' => '/_profiler',
));

// Debug出力
$app->register(new \Eccube\ServiceProvider\DebugServiceProvider());

$app->register(new \Saxulum\SaxulumWebProfiler\Provider\SaxulumWebProfilerProvider());

$app->run();
