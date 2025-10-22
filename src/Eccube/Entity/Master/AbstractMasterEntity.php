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
use Eccube\Entity\AbstractEntity;

/**
 * AbstractMasterEntity
 */
#[ORM\MappedSuperclass]
abstract class AbstractMasterEntity extends AbstractEntity implements \Stringable
{
    /**
     * @return string
     */
    #[\Override]
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'smallint', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    protected $name;

    /**
     * @var int
     */
    #[ORM\Column(name: 'sort_no', type: 'smallint', options: ['unsigned' => true])]
    protected $sort_no;

    /**
     * Set id.
     *
     * @return $this
     */
    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @return $this
     */
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set sortNo.
     *
     * @return $this
     */
    public function setSortNo(int $sortNo): static
    {
        $this->sort_no = $sortNo;

        return $this;
    }

    /**
     * Get sortNo.
     *
     * @return int
     */
    public function getSortNo(): int
    {
        return $this->sort_no;
    }

    /**
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return self::getConstantValue($name);
    }

    /**
     * @return mixed
     */
    public static function __callStatic(string $name, mixed $arguments): mixed
    {
        return self::getConstantValue($name);
    }

    /**
     * @return mixed
     *
     * @throws \ReflectionException
     */
    protected static function getConstantValue(string $name): mixed
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

        return $refProperty->getValue($ref->newInstance());
    }
}
