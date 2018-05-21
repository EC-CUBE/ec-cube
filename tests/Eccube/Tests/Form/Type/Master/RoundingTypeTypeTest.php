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

namespace Eccube\Tests\Form\Type\Master;

use Eccube\Form\Type\Master\RoundingTypeType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Symfony\Component\Form\FormInterface;

class RoundingTypeTypeTest extends AbstractTypeTestCase
{
    /** @var FormInterface */
    protected $form;

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(RoundingTypeType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function test_getName_is_rounding_type()
    {
        $this->assertSame('rounding_type', $this->form->getName());
    }
}
