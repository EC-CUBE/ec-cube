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
 * AbstractMasterEntity
 */
#[ORM\MappedSuperclass]
abstract class AbstractMasterEntity extends \Eccube\Entity\AbstractEntity implements \Stringable
{
    /**
     * @return string
     */
    #[\Override]
    public function __toString(): string
    {
        return (string) $this->getName();
    }

    /**
     * @var int
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
        // VarExporter(LazyGhost) 経由かつ、プロパティが実在する場合のみ許可
        if (self::isLazyGhostHydration() && self::assignToDeclaredProperty($this, $name, $value)) {
            // スコープを $this に束縛して、子クラスの protected/private も安全に代入
            (\Closure::bind(function ($n, $v) { $this->$n = $v; }, $this, $this))($name, $value);

            return;
        }
        throw new \InvalidArgumentException(\sprintf('%s: unknown property "%s"', static::class, $name));
    }

    private static function isLazyGhostHydration(): bool
    {
        // コストを抑えるためスタック深さは小さめ＆引数は無視
        $trace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 12);
        foreach ($trace as $f) {
            if (isset($f['class']) && \str_starts_with((string) $f['class'], 'Symfony\\Component\\VarExporter')) {
                return true; // Internal\Hydrator や LazyGhostTrait など
            }
            if (isset($f['file']) && false !== \strpos((string) $f['file'], 'var-exporter')) {
                return true;
            }
        }

        return false;
    }

    /**
     * +     * 継承チェーンを遡って「宣言クラス」を特定し、そのスコープで代入する。
     * +     * 見つかった場合 true / 見つからなければ false を返す。
     * +     */
    private static function assignToDeclaredProperty(object $obj, string $name, $value): bool
    {
        $rc = new \ReflectionClass($obj);
        while ($rc) {
            if ($rc->hasProperty($name)) {
                $declaring = $rc->getProperty($name)->getDeclaringClass()->getName();
                // 宣言クラスのスコープで代入（private/protected どちらでも可）
                (\Closure::bind(function ($n, $v) { $this->$n = $v; }, $obj, $declaring))($name, $value);

                return true;
            }
            $rc = $rc->getParentClass();
        }

        return false;
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
