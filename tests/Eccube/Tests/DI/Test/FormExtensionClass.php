<?php

namespace Eccube\Tests\DI\Test;

use Eccube\Annotation\FormExtension;
use Symfony\Component\Form\AbstractTypeExtension;

/**
 * @FormExtension
 */
class FormExtensionClass extends AbstractTypeExtension
{
    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
    }
}
