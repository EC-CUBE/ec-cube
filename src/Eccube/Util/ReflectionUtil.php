<?php

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
