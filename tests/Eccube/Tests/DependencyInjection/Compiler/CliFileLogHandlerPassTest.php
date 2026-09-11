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

namespace Eccube\Tests\DependencyInjection\Compiler;

use Eccube\DependencyInjection\Compiler\CliFileLogHandlerPass;
use Eccube\Log\CliSuppressibleHandler;
use Monolog\Handler\RotatingFileHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class CliFileLogHandlerPassTest extends TestCase
{
    public function testWrapsFileHandler(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('monolog.handler.main', new Definition(RotatingFileHandler::class, [
            '/var/www/html/var/log/prod/site.log',
            10,
        ]));

        (new CliFileLogHandlerPass())->process($container);

        $wrapper = $container->getDefinition('monolog.handler.main');
        $this->assertSame(CliSuppressibleHandler::class, $wrapper->getClass());
        $this->assertSame('%env(bool:ECCUBE_CLI_LOG_TO_FILE)%', $wrapper->getArgument(1));

        $inner = $container->getDefinition('monolog.handler.main.cli_suppressible.inner');
        $this->assertSame(RotatingFileHandler::class, $inner->getClass());
        $this->assertSame('/var/www/html/var/log/prod/site.log', $inner->getArgument(0));
    }

    public function testDoesNotWrapNonFileHandler(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('monolog.handler.console', new Definition(ConsoleHandler::class));

        (new CliFileLogHandlerPass())->process($container);

        $this->assertSame(ConsoleHandler::class, $container->getDefinition('monolog.handler.console')->getClass());
        $this->assertFalse($container->hasDefinition('monolog.handler.console.cli_suppressible.inner'));
    }

    public function testIsIdempotent(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('monolog.handler.main', new Definition(RotatingFileHandler::class, ['/tmp/site.log']));

        $pass = new CliFileLogHandlerPass();
        $pass->process($container);
        $pass->process($container);

        $this->assertSame(CliSuppressibleHandler::class, $container->getDefinition('monolog.handler.main')->getClass());
        $this->assertSame(
            RotatingFileHandler::class,
            $container->getDefinition('monolog.handler.main.cli_suppressible.inner')->getClass()
        );
        $this->assertFalse(
            $container->hasDefinition('monolog.handler.main.cli_suppressible.inner.cli_suppressible.inner'),
            '二重にラップしない'
        );
    }
}
