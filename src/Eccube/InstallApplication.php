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


namespace Eccube;

use Eccube\Application\ApplicationTrait;
use Monolog\Logger;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class InstallApplication extends ApplicationTrait
{
    public function __construct(array $values = array())
    {
        $app = $this;

        parent::__construct($values);

        $app->register(new \Silex\Provider\MonologServiceProvider(), array(
            'monolog.logfile' => __DIR__ . '/../../app/log/install.log',
        ));

        $app->register(new \Silex\Provider\SessionServiceProvider());

        $app->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => array(__DIR__ . '/Resource/template/install'),
            'twig.form.templates' => array('bootstrap_3_horizontal_layout.html.twig'),
        ));

        $this->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $this->register(new \Silex\Provider\FormServiceProvider());
        $this->register(new \Silex\Provider\ValidatorServiceProvider());

        $this->register(new \Silex\Provider\TranslationServiceProvider(), array(
            'locale' => 'ja',
        ));
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
            $translator->addResource('yaml', __DIR__ . '/Resource/locale/ja.yml', 'ja');

            return $translator;
        }));

        $app->mount('', new ControllerProvider\InstallControllerProvider());
        $app->register(new ServiceProvider\InstallServiceProvider());

        $app->error(function (\Exception $e, $code) use ($app) {
            if ($code === 404) {
                return $app->redirect($app['url_generator']->generate('install'));
            } elseif ($app['debug']) {
                return;
            }

            return $app['twig']->render('error.twig', array(
                'error' => 'エラーが発生しました.',
            ));
        });
    }
}
