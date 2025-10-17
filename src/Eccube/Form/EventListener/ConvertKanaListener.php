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
     * @var string
     */
    protected $option;

    /**
     * @var string
     */
    protected $encoding;

    /**
     * @param string $option
     * @param string $encoding
     */
    public function __construct($option = 'a', $encoding = 'utf-8')
    {
        $this->option = $option;
        $this->encoding = $encoding;
    }

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     *
     * @return void
     */
    public function onPreSubmit(FormEvent $event): void
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
