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


namespace Eccube\Entity;

use Doctrine\Common\Util\Inflector;

abstract class AbstractEntity implements \ArrayAccess
{

    public function offsetExists($offset)
    {
        $method = Inflector::classify($offset);

        return method_exists($this, "get$method") || method_exists($this, "is$method");
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetGet($offset)
    {
        $method = Inflector::classify($offset);

        if (method_exists($this, "get$method")) {
            return $this->{"get$method"}();
        } elseif (method_exists($this, "is$method")) {
            return $this->{"is$method"}();
        }
    }

    public function offsetUnset($offset)
    {
    }

    /**
     * 引数の連想配列を元にプロパティを設定します.
     * DBから取り出した連想配列を, プロパティへ設定する際に使用します.
     *
     * @param array $arrProps プロパティの情報を格納した連想配列
     * @param ReflectionClass $parentClass 親のクラス. 本メソッドの内部的に使用します.
     * @param array $excludeAttribute 除外したいフィールド名の配列
     */
    public function setPropertiesFromArray(array $arrProps, array $excludeAttribute = array(), \ReflectionClass $parentClass = null)
    {
        $objReflect = null;
        if (is_object($parentClass)) {
            $objReflect = $parentClass;
        } else {
            $objReflect = new \ReflectionClass($this);
        }
        $arrProperties = $objReflect->getProperties();
        foreach ($arrProperties as $objProperty) {
            $objProperty->setAccessible(true);
            $name = $objProperty->getName();
            if (in_array($name, $excludeAttribute) || !array_key_exists($name, $arrProps)) {
                continue;
            }
            $objProperty->setValue($this, $arrProps[$name]);
        }

        // 親クラスがある場合は再帰的にプロパティを取得
        $parentClass = $objReflect->getParentClass();
        if (is_object($parentClass)) {
            self::setPropertiesFromArray($arrProps, $excludeAttribute, $parentClass);
        }
    }

    /**
     * プロパティの値を連想配列で返します.
     * DBを更新する場合などで, 連想配列の値を取得したい場合に使用します.
     *
     * @param ReflectionClass $parentClass 親のクラス. 本メソッドの内部的に使用します.
     * @param array $excludeAttribute 除外したいフィールド名の配列
     * @return array 連想配列のプロパティの値
     */
    public function toArray(array $excludeAttribute = array(), \ReflectionClass $parentClass = null)
    {
        $objReflect = null;
        if (is_object($parentClass)) {
            $objReflect = $parentClass;
        } else {
            $objReflect = new \ReflectionClass($this);
        }
        $arrProperties = $objReflect->getProperties();
        $arrResults = array();
        foreach ($arrProperties as $objProperty) {
            $objProperty->setAccessible(true);
            $name = $objProperty->getName();
            if (in_array($name, $excludeAttribute)) {
                continue;
            }
            $arrResults[$name] = $objProperty->getValue($this);
        }

        $parentClass = $objReflect->getParentClass();
        if (is_object($parentClass)) {
            $arrParents = self::toArray($excludeAttribute, $parentClass);
            if (!is_array($arrParents)) {
                $arrParents = array();
            }
            if (!is_array($arrResults)) {
                $arrResults = array();
            }
            $arrResults = array_merge($arrParents, $arrResults);
        }
        return $arrResults;
    }

    /**
     * コピー元のオブジェクトのフィールド名を指定して、同名のフィールドに値をコピー
     *
     * @param object $srcObject コピー元のオブジェクト
     * @param array $excludeAttribute 除外したいフィールド名の配列
     * @return object
     */
    public function copyProperties($srcObject, array $excludeAttribute = array())
    {
        $this->setPropertiesFromArray($srcObject->toArray($excludeAttribute), $excludeAttribute);
        return $this;
    }
}
