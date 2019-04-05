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

use Eccube\Twig\Extension\IntlExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class IntlExtensionTest extends TestCase
{
    /**
     * @var Environment
     */
    protected $twig;

    public function setUp()
    {
        $loader = new ArrayLoader();
        $loader->setTemplate('date_day_template', '{{ date|date_day }}');
        $loader->setTemplate('date_min_template', '{{ date|date_min }}');

        $this->twig = new Environment($loader);
        $this->twig->getExtension('Twig_Extension_Core')->setTimezone('Asia/Tokyo');
        $this->twig->addExtension(new IntlExtension());

        // twig_localized_date_filter関数を使うため, Twig_Extensions_Extension_Intlをautoloadする.
        class_exists('Twig_Extensions_Extension_Intl');
    }

    public function testDateDay()
    {
        $date = new \DateTime('2018-01-01', new \DateTimeZone('Asia/Tokyo'));
        $this->assertSame('2018/01/01', $this->twig->render('date_day_template', ['date' => $date]));
    }

    public function testDateMin()
    {
        $date = new \DateTime('2018-01-01 12:34:56', new \DateTimeZone('Asia/Tokyo'));
        $this->assertSame('2018/01/01 12:34', $this->twig->render('date_min_template', ['date' => $date]));
    }
}
