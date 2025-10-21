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

namespace Eccube\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class FormAppend
{
    /**
     * @var bool
     */
    public $auto_render;

    /**
     * @var string
     */
    public $form_theme;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array<string, mixed>
     */
    public $options;

    /**
     * @var string
     */
    public $style_class;
}
