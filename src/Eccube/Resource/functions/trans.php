<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function trans($id, array $parameters = [], $domain = null, $locale = null)
{
    $app = \Eccube\Application::getInstance();
    if (isset($app['translator'])) {
        return $app['translator']->trans($id, $parameters, $domain, $locale);
    }
}

function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
{
    $app = \Eccube\Application::getInstance();
    if (isset($app['translator'])) {
        return $app['translator']->transChoice($id, $number, $parameters, $domain, $locale);
    }
}
