<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eccube\Tests\Application;

use Eccube\Tests\EccubeTestCase;

/**
 * FormTrait test cases.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @requires PHP 5.4
 */
class FormTraitTest extends EccubeTestCase
{
    public function testForm()
    {
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilder', $this->app->form());
    }
}
