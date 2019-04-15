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

namespace Eccube\Tests\Form\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormEvent;

class TruncateHyphenListenerTest extends TestCase
{
    public function testTruncateHyphen()
    {
        $data = '0123-456-789';
        $form = $this->getMockBuilder('Symfony\Component\Form\Test\FormInterface')->getMock();
        $event = new FormEvent($form, $data);

        $filter = new \Eccube\Form\EventListener\TruncateHyphenListener();
        $filter->onPreSubmit($event);

        $this->assertEquals('0123456789', $event->getData());
    }
}
