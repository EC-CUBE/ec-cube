<?php


namespace Eccube\Annotation;

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