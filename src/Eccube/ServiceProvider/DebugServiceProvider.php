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


namespace Eccube\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Bridge\Twig\Extension\DumpExtension;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\DataCollector\DumpDataCollector;
use Symfony\Component\HttpKernel\EventListener\DumpListener;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Debug Dump
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Jérôme Macias
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @see https://github.com/jeromemacias/Silex-Debug/tree/1.0
 *
 */
class DebugServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['var_dumper.cloner'] = $app->share(function ($app) {
            $cloner = new VarCloner();

            if (isset($app['debug.max_items'])) {
                $cloner->setMaxItems($app['debug.max_items']);
            }

            if (isset($app['debug.max_string_length'])) {
                $cloner->setMaxString($app['debug.max_string_length']);
            }

            return $cloner;
        });

        $app['data_collector.templates'] = $app->share($app->extend('data_collector.templates', function ($templates) {
            return array_merge($templates, array(array('dump', '@Debug/Profiler/dump.html.twig')));
        }));

        $app['data_collector.dump'] = $app->share(function ($app) {
            return new DumpDataCollector($app['stopwatch'], $app['code.file_link_format'], null, null, new HtmlDumper());
        });

        $app['data_collectors'] = $app->share($app->extend('data_collectors', function ($collectors, $app) {
            $collectors['dump'] = $app->share(function ($app) {
                return $app['data_collector.dump'];
            });

            return $collectors;
        }));

        $app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
            if (class_exists('\Symfony\Bridge\Twig\Extension\DumpExtension')) {
                $twig->addExtension(new DumpExtension($app['var_dumper.cloner']));
            }

            return $twig;
        }));

        $app['twig.loader.filesystem'] = $app->share($app->extend('twig.loader.filesystem', function ($loader, $app) {
            $loader->addPath($app['debug.templates_path'], 'Debug');

            return $loader;
        }));

        $app['debug.templates_path'] = function () {
            $r = new \ReflectionClass('Symfony\Bundle\DebugBundle\DependencyInjection\Configuration');

            return dirname(dirname($r->getFileName())).'/Resources/views';
        };
    }

    public function boot(Application $app)
    {
        // This code is here to lazy load the dump stack. This default
        // configuration for CLI mode is overridden in HTTP mode on
        // 'kernel.request' event
        VarDumper::setHandler(function ($var) use ($app) {
            $dumper = $app['data_collector.dump'];
            $cloner = $app['var_dumper.cloner'];

            $handler = function ($var) use ($dumper, $cloner) {
                $dumper->dump($cloner->cloneVar($var));
            };

            VarDumper::setHandler($handler);
            $handler($var);
        });

        $app['dispatcher']->addSubscriber(new DumpListener($app['var_dumper.cloner'], $app['data_collector.dump']));
    }
}
