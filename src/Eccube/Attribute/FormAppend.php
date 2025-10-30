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
    public bool $auto_render;

    /**
     * @var string
     */
    public string $form_theme;

    /**
     * @var string
     */
    public string $type;

    /**
     * @var array<string, mixed>
     */
    public array $options;

    /**
     * @var string
     */
    public string $style_class;
}
