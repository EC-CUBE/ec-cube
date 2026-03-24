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

namespace Eccube\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class EntityExtension
{
    /**
     * @var string
     */
    public $value;

    public function __construct($value = null)
    {
        if (is_string($value)) {
            $this->value = $value;
        } elseif (is_array($value) && isset($value['value'])) {
            $this->value = $value['value'];
        }
    }
}
