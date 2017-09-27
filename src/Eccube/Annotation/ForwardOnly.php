<?php

namespace Eccube\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\ORM\Mapping\Annotation;


/**
 * @Annotation
 * @Target("METHOD")
 */
final class ForwardOnly implements Annotation
{
    /**
     * @var string
     */
    public $value;
}
