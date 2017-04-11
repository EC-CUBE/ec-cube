<?php

namespace Eccube\Annotation;

use Doctrine\ORM\Mapping\Annotation;


/**
 * @Annotation
 * @Target("CLASS")
 */
final class EntityExt implements Annotation
{
    /**
     * @var string
     */
    public $target;

}
