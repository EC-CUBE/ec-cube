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
    public static function setValue($instance, $property, $value)
    {
        $refObj = new \ReflectionObject($instance);
        $refProp = $refObj->getProperty($property);
        $refProp->setAccessible(true);
        $refProp->setValue($instance, $value);
    }

    public static function setValues($instance, array $values)
    {
        foreach ($values as $property => $value) {
            self::setValue($instance, $property, $value);
        }
    }
}
