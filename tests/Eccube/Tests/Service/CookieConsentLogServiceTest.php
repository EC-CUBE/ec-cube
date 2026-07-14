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

namespace Eccube\Tests\Service;

use Eccube\Service\CookieConsentLogService;
use Eccube\Service\CookieConsentService;
use Eccube\Tests\EccubeTestCase;
use Monolog\Handler\TestHandler;

/**
 * CookieConsentLogServiceのテスト
 */
final class CookieConsentLogServiceTest extends EccubeTestCase
{
    private ?CookieConsentLogService $service = null;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CookieConsentLogService();
    }

    /**
     * buildLogData が指定した項目をすべて含む配列を返すことを確認する。
     */
    public function testBuildLogDataReturnsExpectedStructure()
    {
        $logData = $this->service->buildLogData(
            CookieConsentService::STATUS_ACCEPTED,
            10,
            'session-id-123',
            '192.0.2.1',
            'Mozilla/5.0',
            'popup',
            CookieConsentService::STATUS_REJECTED
        );

        $this->assertSame('accepted', $logData['consent_status']);
        $this->assertSame(10, $logData['customer_id']);
        $this->assertSame('session-id-123', $logData['session_id']);
        $this->assertSame('192.0.2.1', $logData['ip_address']);
        $this->assertSame('Mozilla/5.0', $logData['user_agent']);
        $this->assertSame('popup', $logData['source']);
        $this->assertSame('rejected', $logData['previous_status']);
        $this->assertArrayHasKey('timestamp', $logData);
    }

    /**
     * ゲスト（customer_id=null）・previous_status 省略時も妥当な構造を返すことを確認する。
     */
    public function testBuildLogDataForGuest()
    {
        $logData = $this->service->buildLogData(
            CookieConsentService::STATUS_REJECTED,
            null,
            'session-id-456',
            '192.0.2.2',
            'UA',
            'settings_page'
        );

        $this->assertNull($logData['customer_id']);
        $this->assertNull($logData['previous_status']);
        $this->assertSame('settings_page', $logData['source']);
    }

    /**
     * saveLog が cookie_consent チャンネルへ info レベルで記録することを確認する。
     */
    public function testSaveLogWritesToCookieConsentChannel()
    {
        // サービスが内部で利用する logs() ヘルパと同一のロガーインスタンスを取得する
        $logger = logs(CookieConsentLogService::LOG_CHANNEL);
        $testHandler = new TestHandler();
        $logger->pushHandler($testHandler);

        try {
            $logData = $this->service->buildLogData(
                CookieConsentService::STATUS_ACCEPTED,
                null,
                'session-xyz',
                '192.0.2.10',
                'UA-test',
                'popup'
            );
            $this->service->saveLog($logData);

            $this->assertTrue($testHandler->hasInfoRecords());
            $records = $testHandler->getRecords();
            $this->assertNotEmpty($records);
            $context = $records[0]['context'];
            $this->assertSame('accepted', $context['consent_status']);
            $this->assertSame('session-xyz', $context['session_id']);
        } finally {
            $logger->popHandler();
        }
    }

    /**
     * ログ出力が失敗しても例外を投げず処理を継続する（ベストエフォート）ことを確認する。
     */
    public function testSaveLogIsBestEffortAndDoesNotThrow()
    {
        // 必須キーを欠いた不完全なデータでも例外を投げないこと
        /** @var array<string, mixed> $invalid */
        $invalid = ['consent_status' => 'accepted'];

        $this->service->saveLog($invalid);

        $this->addToAssertionCount(1);
    }
}
