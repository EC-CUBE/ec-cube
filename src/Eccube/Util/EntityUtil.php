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

namespace Eccube\Util;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Proxy\Proxy;

class EntityUtil
{
    /**
     * LAZY loading したエンティティの有無をチェックする.
     *
     * 削除済みのエンティティを LAZY loading した場合、 soft_delete filter で
     * フィルタリングされてしまい、正常に取得することができない.
     * しかし、 Proxy オブジェクトとして取得されるため、この関数を使用して
     * 有無をチェックする.
     * この関数を使用せず、該当のオブジェクトのプロパティを取得しようとすると、
     * EntityNotFoundException がスローされてしまう.
     *
     * @param $entity LAZY loading したエンティティ
     *
     * @return bool エンティティが削除済みの場合 true
     *
     * @see https://github.com/EC-CUBE/ec-cube/pull/602#issuecomment-125431246
     * @deprecated
     */
    public static function isEmpty($entity)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);
        if ($entity instanceof Proxy) {
            try {
                $entity->__load();
            } catch (EntityNotFoundException $e) {
                return true;
            }

            return false;
        } else {
            return empty($entity);
        }
    }

    /**
     * LAZY loading したエンティティの有無をチェックする.
     *
     * EntityUtil::isEmpty() の逆の結果を返します.
     *
     * @param $entity
     *
     * @return bool
     *
     * @see EntityUtil::isEmpty()
     * @deprecated
     */
    public static function isNotEmpty($entity)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);

        return !self::isEmpty($entity);
    }

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
