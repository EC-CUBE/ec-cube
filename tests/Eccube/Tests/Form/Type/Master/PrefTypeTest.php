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

use Eccube\Form\Type\Master\PrefType;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class PrefTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var PrefRepository */
    protected $prefRepo;

    public function setUp()
    {
        parent::setUp();
        $this->prefRepo = $this->entityManager->getRepository(\Eccube\Entity\Master\Pref::class);

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(PrefType::class, null)
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit(47);
        $this->assertTrue($this->form->isValid());

        $this->assertEquals($this->form->getData(), $this->prefRepo->find(47));
    }

    public function testViewData()
    {
        $view = $this->form->createView();
        $choices = $view->vars['choices'];

        // placeholder
        $this->assertEquals('common.select__pref', $view->vars['placeholder']);

        $data = [];
        // attrなど含まれているので
        foreach ($choices as $choice) {
            $data[] = $choice->data;
        }

        $query = $this->prefRepo->createQueryBuilder('p')
            ->orderBy('p.sort_no', 'ASC')
            ->getQuery();

        $pref = $query->getResult();

        // order by されているか
        $this->assertEquals($data, $pref);
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
