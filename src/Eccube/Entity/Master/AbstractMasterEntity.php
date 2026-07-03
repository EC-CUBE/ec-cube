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

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;

/**
 * AbstractMasterEntity
 */
#[ORM\MappedSuperclass]
abstract class AbstractMasterEntity extends AbstractEntity implements \Stringable
{
    #[\Override]
    public function __toString(): string
    {
        return $this->getName();
    }

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::SMALLINT, options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected ?int $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255)]
    protected ?string $name = null;

    #[ORM\Column(name: 'sort_no', type: Types::SMALLINT, options: ['unsigned' => true])]
    protected ?int $sort_no = null;

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
     */
    public function getSortNo(): int
    {
        return $this->sort_no;
    }

    public function __get(string $name): mixed
    {
        return self::getConstantValue($name);
    }

    public function __set(string $name, mixed $value): void
    {
        // Allow Doctrine ORM 3.x lazy ghost objects to hydrate properties
        // see also. https://github.com/EC-CUBE/ec-cube/issues/6469
    }

    public static function __callStatic(string $name, mixed $arguments): mixed
    {
        return self::getConstantValue($name);
    }

    /**
     * @throws \ReflectionException
     */
    protected static function getConstantValue(string $name): mixed
    {
        @trigger_error(
            sprintf(
                'The enum-like property access mechanism is deprecated. Use PHP 8.2+ trait constants instead: trait MyTrait { public const %s = value; }',
                $name
            ),
            E_USER_DEPRECATED
        );

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
