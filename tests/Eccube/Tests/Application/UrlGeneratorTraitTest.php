<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eccube\Tests\Application;

use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * UrlGeneratorTrait test cases.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @requires PHP 5.4
 */
class UrlGeneratorTraitTest extends EccubeTestCase
{
    public function testUrl()
    {
        $generator = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')->disableOriginalConstructor()->getMock();

        $app = $this->app;
        $app->overwrite('url_generator', $generator);

        $generator->expects($this->once())->method('generate')->with('foo', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        $app->url('foo');
    }

    public function testPath()
    {
        $generator = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')->disableOriginalConstructor()->getMock();

        $app = $this->app;
        $app->overwrite('url_generator', $generator);

        $generator->expects($this->once())->method('generate')->with('foo', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
        $app->path('foo');
    }
}
