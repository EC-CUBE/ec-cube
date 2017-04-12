<?php


namespace Eccube\Annotation;

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