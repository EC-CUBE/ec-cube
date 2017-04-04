<?php


namespace Eccube\Entity\Event\Annotations;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class TargetEvent
{
    /**
     * @var string
     */
    public $value;
}