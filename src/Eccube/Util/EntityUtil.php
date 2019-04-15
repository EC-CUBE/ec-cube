<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
     * @return bool エンティティが削除済みの場合 true
     * @see https://github.com/EC-CUBE/ec-cube/pull/602#issuecomment-125431246
     */
    public static function isEmpty($entity)
    {
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
     * @return bool
     * @see EntityUtil::isEmpty()
     */
    public static function isNotEmpty($entity)
    {
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
     * @return array エンティティのプロパティの配列
     */
    public static function dumpToArray($entity)
    {
        $objReflect = new \ReflectionClass($entity);
        $arrProperties = $objReflect->getProperties();
        $arrResults = array();
        foreach ($arrProperties as $objProperty) {
            $objProperty->setAccessible(true);
            $name = $objProperty->getName();
            $value = $objProperty->getValue($entity);
            $arrResults[$name] = is_object($value) ? get_class($value) : $value;
        }
        return $arrResults;
    }
}
