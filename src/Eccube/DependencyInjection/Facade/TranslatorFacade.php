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

namespace Eccube\DependencyInjection\Facade;

use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatorFacade
{
    /** @var self|null */
    private static ?self $instance = null;

    private static TranslatorInterface $Translator;

    private function __construct(TranslatorInterface $Translator)
    {
        self::$Translator = $Translator;
    }

    public static function init(TranslatorInterface $Translator): ?TranslatorFacade
    {
        if (null === self::$instance) {
            self::$instance = new self($Translator);
        }

        return self::$instance;
    }

    /**
     * @throws \Exception
     */
    public static function create(): TranslatorInterface
    {
        if (null === self::$instance) {
            throw new \Exception('Facade is not instantiated');
        }

        return self::$Translator;
    }
}
