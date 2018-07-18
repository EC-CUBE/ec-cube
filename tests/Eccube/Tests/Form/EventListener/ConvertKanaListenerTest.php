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

namespace Eccube\Tests\Form\EventListener;

use Eccube\Form\EventListener\ConvertKanaListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormEvent;

class ConvertKanaListenerTest extends TestCase
{
    public function testConvertKana_string()
    {
        $data = '１２３４５';
        $form = $this->getMockBuilder('Symfony\Component\Form\Test\FormInterface')->getMock();
        $event = new FormEvent($form, $data);

        $filter = new ConvertKanaListener();
        $filter->onPreSubmit($event);

        $this->assertEquals('12345', $event->getData());
    }

    public function testConvertKana_array()
    {
        $data = ['１２３４５'];
        $form = $this->getMockBuilder('Symfony\Component\Form\Test\FormInterface')->getMock();
        $event = new FormEvent($form, $data);

        $filter = new ConvertKanaListener();
        $filter->onPreSubmit($event);

        $this->assertEquals(['12345'], $event->getData());
    }

    public function testConvertKana_HiraganaToKana()
    {
        $data = 'あいうえお';
        $form = $this->getMockBuilder('Symfony\Component\Form\Test\FormInterface')->getMock();
        $event = new FormEvent($form, $data);

        $filter = new ConvertKanaListener('CV');
        $filter->onPreSubmit($event);

        $this->assertEquals('アイウエオ', $event->getData());
    }
}
