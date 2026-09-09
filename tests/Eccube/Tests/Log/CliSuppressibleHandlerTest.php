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

namespace Eccube\Tests\Log;

use Eccube\Log\CliSuppressibleHandler;
use Monolog\Handler\TestHandler;
use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;

/**
 * テストは CLI で実行されるため, PHP_SAPI === 'cli' の経路を検証する.
 */
final class CliSuppressibleHandlerTest extends TestCase
{
    public function testSuppressesRecordInCli(): void
    {
        $inner = new TestHandler();
        $handler = new CliSuppressibleHandler($inner, false);

        $this->assertFalse($handler->isHandling($this->record()));
        $this->assertFalse($handler->handle($this->record()));
        $this->assertSame([], $inner->getRecords(), '委譲先を呼ばないためファイルは開かれない');
    }

    public function testDelegatesWhenEnabled(): void
    {
        // bubble = false のハンドラは handle() で true を返す. 委譲先の戻り値を
        // そのまま返していることを確認する (バブリングの挙動を変えない).
        $inner = new TestHandler(Level::Debug, false);
        $handler = new CliSuppressibleHandler($inner, true);

        $this->assertTrue($handler->isHandling($this->record()));
        $this->assertTrue($handler->handle($this->record()));
        $this->assertCount(1, $inner->getRecords());
    }

    public function testSuppressesBatchInCli(): void
    {
        $inner = new TestHandler();
        $handler = new CliSuppressibleHandler($inner, false);

        $handler->handleBatch([$this->record(), $this->record()]);

        $this->assertSame([], $inner->getRecords());
    }

    public function testDelegatesBatchWhenEnabled(): void
    {
        $inner = new TestHandler();
        $handler = new CliSuppressibleHandler($inner, true);

        $handler->handleBatch([$this->record(), $this->record()]);

        $this->assertCount(2, $inner->getRecords());
    }

    private function record(): LogRecord
    {
        return new LogRecord(new \DateTimeImmutable(), 'app', Level::Warning, 'message');
    }
}
