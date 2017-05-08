<?php


namespace Eccube\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class QueryExtension implements Annotation
{
    /**
     * @var array
     */
    public $value;
}