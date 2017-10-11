<?php

namespace Eccube\Tests\DI\Test;

use Eccube\Annotation\FormExtension;
use Symfony\Component\Form\AbstractTypeExtension;

/**
 * @FormExtension
 */
class FormExtensionClass extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
    }
}
