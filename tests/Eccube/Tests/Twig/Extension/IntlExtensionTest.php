<?php

declare(strict_types=1);

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
use Twig\Extension\AttributeExtension;
use Twig\Extension\CoreExtension;
use Twig\Loader\ArrayLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

final class IntlExtensionTest extends TestCase
{
    protected ?Environment $twig = null;

    protected function setUp(): void
    {
        $loader = new ArrayLoader();
        $loader->setTemplate('date_day_template', '{{ date|date_day }}');
        $loader->setTemplate('date_min_template', '{{ date|date_min }}');

        $this->twig = new Environment($loader);
        $this->twig->getExtension(CoreExtension::class)->setTimezone('Asia/Tokyo');
        // IntlExtension は #[AsTwigFilter] で定義するため, 素の Environment では
        // AttributeExtension とランタイムローダの組で登録する（コンテナ経由の登録は
        // TwigBundle が twig.attribute_extension として自動で行う）。
        $this->twig->addExtension(new AttributeExtension(IntlExtension::class));
        $this->twig->addRuntimeLoader(new FactoryRuntimeLoader([
            IntlExtension::class => static fn (): IntlExtension => new IntlExtension(),
        ]));
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
