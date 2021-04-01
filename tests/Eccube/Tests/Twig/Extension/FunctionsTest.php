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

namespace Eccube\Tests\Twig\Extension;

use Eccube\Tests\EccubeTestCase;

class FunctionsTest extends EccubeTestCase
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function setUp()
    {
        parent::setUp();

        $this->twig = self::$container->get('twig');
    }

    /**
     * PHP関数を使用するテスト
     */
    public function testPhpFunctions()
    {
        $template = $this->twig->createTemplate("<div id='test'>{{ php_print_r('aaa', true) }}</div>");

        $this->expected = "<div id='test'>aaa</div>";
        $this->actual = $template->render([]);

        $this->verify();
    }
}
