<?php

namespace Eccube\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\ORM\Mapping\Annotation;


/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class FormAppend implements Annotation
{
    /**
     * @var bool
     */
    public $auto_render;

    /**
     * @var string
     */
    public $form_theme;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $options;
}
