<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
        $this->formFactory = self::$container->get('form.factory');
    }
}
