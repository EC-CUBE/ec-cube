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

namespace Plugin\FormExtension\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Plugin\FormExtension\Form\Extension\EntryTypeExtension;

class FormExtensionServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app->extend(
            'form.type.extensions',
            function ($extensions) {
                $extensions[] = new EntryTypeExtension();

                return $extensions;
            }
        );
    }
}
