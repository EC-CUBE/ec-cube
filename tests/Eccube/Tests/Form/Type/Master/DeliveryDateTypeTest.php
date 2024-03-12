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

namespace Eccube\Tests\Form\Type\Master;

use Eccube\Form\Type\Master\DeliveryDurationType;
use Eccube\Repository\DeliveryDurationRepository;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class DeliveryDurationTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var DeliveryDurationRepository */
    protected $deliveryDurationRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->deliveryDurationRepo = $this->entityManager->getRepository(\Eccube\Entity\DeliveryDuration::class);

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(DeliveryDurationType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit(1);
        $this->assertTrue($this->form->isValid());
        $this->assertEquals($this->form->getData(), $this->deliveryDurationRepo->find(1));
    }

    public function testViewData()
    {
        $view = $this->form->createView();
        $choices = $view->vars['choices'];

        $data = [];
        foreach ($choices as $choice) {
            $data[] = $choice->data;
        }
        $query = $this->deliveryDurationRepo->createQueryBuilder('m')
            ->orderBy('m.sort_no', 'ASC')
            ->getQuery();
        $res = $query->getResult();
        // order by されているか
        $this->assertEquals($data, $res);
    }

    /**
     * 範囲外の値のテスト
     */
    public function testInvalidDataInt()
    {
        $this->form->submit(50);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * 範囲外の値のテスト
     */
    public function testInvalidDataString()
    {
        $this->form->submit('a');
        $this->assertFalse($this->form->isValid());
    }
}
