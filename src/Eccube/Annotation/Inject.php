<?php

namespace Eccube\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\ORM\Mapping\Annotation;


/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class Inject implements Annotation
{
    /**
     * @var string
     */
    public $value;
}
