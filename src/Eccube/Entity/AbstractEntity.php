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

namespace Eccube\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Proxy\Proxy;
use Eccube\DependencyInjection\Facade\AnnotationReaderFacade;
use Eccube\Util\StringUtil;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

/** @MappedSuperclass */
abstract class AbstractEntity implements \ArrayAccess
{
    public function offsetExists($offset)
    {
        $method = Inflector::classify($offset);

        return method_exists($this, $method)
            || method_exists($this, "get$method")
            || method_exists($this, "is$method")
            || method_exists($this, "has$method");
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetGet($offset)
    {
        $method = Inflector::classify($offset);

        if (method_exists($this, $method)) {
            return $this->$method();
        } elseif (method_exists($this, "get$method")) {
            return $this->{"get$method"}();
        } elseif (method_exists($this, "is$method")) {
            return $this->{"is$method"}();
        } elseif (method_exists($this, "has$method")) {
            return $this->{"has$method"}();
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
     * @param \ReflectionClass $parentClass 親のクラス. 本メソッドの内部的に使用します.
     * @param string[] $excludeAttribute 除外したいフィールド名の配列
     */
    public function setPropertiesFromArray(array $arrProps, array $excludeAttribute = [], \ReflectionClass $parentClass = null)
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
     * Convert to associative array.
     *
     * Symfony Serializer Component is expensive, and hard to implementation.
     * Use for encoder only.
     *
     * @param \ReflectionClass $parentClass parent class. Use internally of this method..
     * @param array $excludeAttribute Array of field names to exclusion.
     *
     * @return array
     */
    public function toArray(array $excludeAttribute = ['__initializer__', '__cloner__', '__isInitialized__'], \ReflectionClass $parentClass = null)
    {
        $objReflect = null;
        if (is_object($parentClass)) {
            $objReflect = $parentClass;
        } else {
            $objReflect = new \ReflectionClass($this);
        }
        $arrProperties = $objReflect->getProperties();
        $arrResults = [];
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
                $arrParents = [];
            }
            if (!is_array($arrResults)) {
                $arrResults = [];
            }
            $arrResults = array_merge($arrParents, $arrResults);
        }

        return $arrResults;
    }

    /**
     * Convert to associative array, and normalize to association properties.
     *
     * The type conversion such as:
     * - Datetime ::  W3C datetime format string
     * - AbstractEntity :: associative array such as [id => value]
     * - PersistentCollection :: associative array of [[id => value], [id => value], ...]
     *
     * @param array $excludeAttribute Array of field names to exclusion.
     *
     * @return array
     */
    public function toNormalizedArray(array $excludeAttribute = ['__initializer__', '__cloner__', '__isInitialized__'])
    {
        $arrResult = $this->toArray($excludeAttribute);
        foreach ($arrResult as &$value) {
            if ($value instanceof \DateTime) {
                // see also https://stackoverflow.com/a/17390817/4956633
                $value->setTimezone(new \DateTimeZone('UTC'));
                $value = $value->format('Y-m-d\TH:i:s\Z');
            } elseif ($value instanceof AbstractEntity) {
                // Entity の場合は [id => value] の配列を返す
                $value = $this->getEntityIdentifierAsArray($value);
            } elseif ($value instanceof Collection) {
                // Collection の場合は ID を持つオブジェクトの配列を返す
                $Collections = $value;
                $value = [];
                foreach ($Collections as $Child) {
                    $value[] = $this->getEntityIdentifierAsArray($Child);
                }
            }
        }

        return $arrResult;
    }

    /**
     * Convert to JSON.
     *
     * @param array $excludeAttribute Array of field names to exclusion.
     *
     * @return string
     */
    public function toJSON(array $excludeAttribute = ['__initializer__', '__cloner__', '__isInitialized__'])
    {
        return json_encode($this->toNormalizedArray($excludeAttribute));
    }

    /**
     * Convert to XML.
     *
     * @param array $excludeAttribute Array of field names to exclusion.
     *
     * @return string
     */
    public function toXML(array $excludeAttribute = ['__initializer__', '__cloner__', '__isInitialized__'])
    {
        $ReflectionClass = new \ReflectionClass($this);
        $serializer = new Serializer([new PropertyNormalizer()], [new XmlEncoder($ReflectionClass->getShortName())]);

        $xml = $serializer->serialize($this->toNormalizedArray($excludeAttribute), 'xml');
        if ('\\' === DIRECTORY_SEPARATOR) {
            // The m modifier of the preg functions converts the end-of-line to '\n'
            $xml = StringUtil::convertLineFeed($xml, "\r\n");
        }

        return $xml;
    }

    /**
     * コピー元のオブジェクトのフィールド名を指定して、同名のフィールドに値をコピー
     *
     * @param object $srcObject コピー元のオブジェクト
     * @param string[] $excludeAttribute 除外したいフィールド名の配列
     *
     * @return AbstractEntity
     */
    public function copyProperties($srcObject, array $excludeAttribute = [])
    {
        $this->setPropertiesFromArray($srcObject->toArray($excludeAttribute), $excludeAttribute);

        return $this;
    }

    /**
     * Convert to Entity of Identity value to associative array.
     *
     * @param AbstractEntity $Entity
     *
     * @return array associative array of [[id => value], [id => value], ...]
     */
    public function getEntityIdentifierAsArray(AbstractEntity $Entity)
    {
        $Result = [];
        $PropReflect = new \ReflectionClass($Entity);
        if ($Entity instanceof Proxy) {
            // Doctrine Proxy の場合は親クラスを取得
            $PropReflect = $PropReflect->getParentClass();
        }
        $Properties = $PropReflect->getProperties();

        foreach ($Properties as $Property) {
            $AnnotationReader = AnnotationReaderFacade::create();
            $anno = $AnnotationReader->getPropertyAnnotation($Property, Id::class);
            if ($anno) {
                $Property->setAccessible(true);
                $Result[$Property->getName()] = $Property->getValue($Entity);
            }
        }

        return $Result;
    }
}
