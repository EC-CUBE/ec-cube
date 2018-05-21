<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Form\Type;

use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Form\FormFactoryInterface;

abstract class AbstractTypeTestCase extends EccubeTestCase
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    public function setUp()
    {
        parent::setUp();
        $this->formFactory = $this->container->get('form.factory');
    }
}
