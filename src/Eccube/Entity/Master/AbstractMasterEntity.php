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

namespace Eccube\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

/**
 * AbstractMasterentity
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractMasterEntity extends \Eccube\Entity\AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="smallint", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_no", type="smallint", options={"unsigned":true})
     */
    protected $sort_no;

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sortNo.
     *
     * @param int $sortNo
     *
     * @return $this
     */
    public function setSortNo($sortNo)
    {
        $this->sort_no = $sortNo;

        return $this;
    }

    /**
     * Get sortNo.
     *
     * @return int
     */
    public function getSortNo()
    {
        return $this->sort_no;
    }

    public function __get($name)
    {
        return self::getConstantValue($name);
    }

    public function __set($name, $value)
    {
        throw new \InvalidArgumentException();
    }

    public static function __callStatic($name, $arguments)
    {
        return self::getConstantValue($name);
    }

    protected static function getConstantValue($name)
    {
        if (in_array($name, ['id', 'name', 'sortNo'])) {
            throw new \InvalidArgumentException();
        }
        // see also. http://qiita.com/Hiraku/items/71e385b56dcaa37629fe
        $ref = new \ReflectionClass(static::class);
        // クラス定数が存在していれば, クラス定数から値を取得する
        $constants = $ref->getConstants();
        if (array_key_exists($name, $constants)) {
            return $constants[$name];
        }
        // XXX $obj = new static(); とすると segmentation fault が発生するため, リフレクションで値を取得する
        $refProperty = $ref->getProperty($name);
        $refProperty->setAccessible(true);

        return $refProperty->getValue($ref->newInstance());
    }
}
