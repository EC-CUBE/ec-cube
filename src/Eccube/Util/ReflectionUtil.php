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

namespace Eccube\Util;

class ReflectionUtil
{
    /**
     * @throws \ReflectionException
     */
    public static function setValue(object $instance, string $property, mixed $value): void
    {
        $refObj = new \ReflectionObject($instance);
        $refProp = $refObj->getProperty($property);
        $refProp->setValue($instance, $value);
    }

    /**
     * @param array<mixed> $values
     *
     * @throws \ReflectionException
     */
    public static function setValues(object $instance, array $values): void
    {
        foreach ($values as $property => $value) {
            self::setValue($instance, $property, $value);
        }
    }
}
