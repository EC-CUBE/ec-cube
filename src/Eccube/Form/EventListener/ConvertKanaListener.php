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

namespace Eccube\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ConvertKanaListener implements EventSubscriberInterface
{
    /**
     * @param string $option
     * @param string $encoding
     */
    public function __construct(protected $option = 'a', protected $encoding = 'utf-8')
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (is_array($data)) {
            foreach ($data as &$value) {
                if (is_string($value)) {
                    $value = mb_convert_kana($value, $this->option, $this->encoding);
                }
            }
        } else {
            if (is_string($data)) {
                $data = mb_convert_kana($data, $this->option, $this->encoding);
            }
        }

        $event->setData($data);
    }
}
