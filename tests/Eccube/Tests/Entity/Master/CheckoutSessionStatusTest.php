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

namespace Eccube\Tests\Entity\Master;

use Eccube\Entity\Master\CheckoutSessionStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * CheckoutSessionStatus の終端判定を検証する.
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/7229
 */
final class CheckoutSessionStatusTest extends TestCase
{
    #[DataProvider(methodName: 'provideStatuses')]
    public function testIsTerminal(int $id, bool $expected): void
    {
        $status = (new CheckoutSessionStatus())->setId($id);

        $this->assertSame($expected, $status->isTerminal());
    }

    /**
     * 定義済みの全ステータスを網羅する。ステータスを足したら、ここにも終端かどうかを足す.
     *
     * @return array<string, array{int, bool}>
     */
    public static function provideStatuses(): array
    {
        return [
            'incomplete' => [CheckoutSessionStatus::INCOMPLETE, false],
            'ready' => [CheckoutSessionStatus::READY, false],
            'completed' => [CheckoutSessionStatus::COMPLETED, true],
            'canceled' => [CheckoutSessionStatus::CANCELED, true],
            'expired' => [CheckoutSessionStatus::EXPIRED, true],
            // 在庫を引き当てたまま complete の再開を待つ中間状態であり、終端ではない。
            'requires_action' => [CheckoutSessionStatus::REQUIRES_ACTION, false],
            'in_progress' => [CheckoutSessionStatus::IN_PROGRESS, false],
        ];
    }

    public function testProviderCoversAllStatusConstants(): void
    {
        $constants = (new \ReflectionClass(CheckoutSessionStatus::class))->getConstants();
        $ids = array_filter($constants, is_int(...));

        $this->assertEqualsCanonicalizing(
            array_values($ids),
            array_column(self::provideStatuses(), 0),
            'ステータスを足したら provideStatuses() に終端かどうかを足す',
        );
    }

    public function testUnsavedStatusIsNotTerminal(): void
    {
        $this->assertFalse((new CheckoutSessionStatus())->isTerminal(), 'id 未設定は終端扱いしない');
    }
}
