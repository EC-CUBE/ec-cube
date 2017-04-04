<?php


namespace Eccube\Entity\Event\Annotations;

/**
 * @Annotation
 * @Target("CLASS")
 */
class EntityEvent
{
    /**
     * 対象にするEntityクラス
     * @var array
     */
    public $value;
}