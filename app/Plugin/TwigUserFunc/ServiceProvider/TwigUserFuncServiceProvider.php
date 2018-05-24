<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\TwigUserFunc\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TwigUserFuncServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app->extend('eccube_twig_block_templates', function ($templates) {
            $templates[] = 'TwigUserFunc/Resource/template/hello_block.twig';

            return $templates;
        });
    }
}
