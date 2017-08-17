<?php

namespace Eccube\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\ORM\Mapping\Annotation;


/**
 * @Annotation
 * @Target("CLASS")
 */
final class Service implements Annotation
{
    /**
     * @var string
     */
    public $value;
}
