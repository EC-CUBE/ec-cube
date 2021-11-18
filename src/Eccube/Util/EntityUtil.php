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

class EntityUtil
{
    /**
     * エンティティのプロパティを配列で返す.
     *
     * このメソッドはエンティティの内容をログ出力する際などに使用する.
     * AbstractEntity::toArray() と異なり再帰処理しない.
     * プロパティの値がオブジェクトの場合は、クラス名を出力する.
     *
     * @param object $entity 対象のエンティティ
     *
     * @return array エンティティのプロパティの配列
     */
    public static function dumpToArray($entity)
    {
        $objReflect = new \ReflectionClass($entity);
        $arrProperties = $objReflect->getProperties();
        $arrResults = [];
        foreach ($arrProperties as $objProperty) {
            $objProperty->setAccessible(true);
            $name = $objProperty->getName();
            $value = $objProperty->getValue($entity);
            $arrResults[$name] = is_object($value) ? get_class($value) : $value;
        }

        return $arrResults;
    }
}
