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

namespace Eccube\Tests\Service\Permission;

use Eccube\Common\EccubeConfig;
use Eccube\Service\Permission\UserIdentity;
use Eccube\Service\Permission\WebServerUserResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Web サーバーの実行ユーザーの判定を検証する.
 *
 * テストが生成するファイルの所有者は診断の実行ユーザーと同じになるため,
 * 「Web サーバーでのみ生成されるもの」と「CLI でも生成されるもの」の扱いの違いを検証できる.
 */
final class WebServerUserResolverTest extends TestCase
{
    private string $projectDir;

    private Filesystem $fs;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fs = new Filesystem();
        $this->projectDir = sys_get_temp_dir().'/eccube-permission-'.bin2hex(random_bytes(6));
        $this->fs->mkdir($this->projectDir);
    }

    protected function tearDown(): void
    {
        $this->fs->remove($this->projectDir);
        parent::tearDown();
    }

    public function testReturnsNullWhenNoCandidateExists(): void
    {
        $this->assertNotInstanceOf(UserIdentity::class, $this->resolver()->resolve());
    }

    public function testSessionFileIsUsedEvenWhenOwnerIsTheCurrentUser(): void
    {
        // セッションファイルは Web リクエストでのみ生成されるため, 実行ユーザーと同じ uid でも採用する
        $this->fs->dumpFile($this->projectDir.'/var/sessions/prod/sess_0123456789', '');

        $identity = $this->resolver()->resolve();

        $this->assertInstanceOf(UserIdentity::class, $identity);
        $this->assertSame(getmyuid(), $identity->uid);
        $this->assertStringContainsString('sess_0123456789', $identity->source);
    }

    public function testLogFileOwnedByTheCurrentUserIsIgnored(): void
    {
        // ログは bin/console 実行でも書き込まれるため, 実行ユーザーと同じ uid では判定材料にならない
        $this->fs->dumpFile($this->projectDir.'/var/log/prod/site.log', '');

        $this->assertNotInstanceOf(UserIdentity::class, $this->resolver()->resolve());
    }

    public function testUploadedFileIsUsedButDotFileIsIgnored(): void
    {
        $this->fs->dumpFile($this->projectDir.'/html/upload/temp_image/.gitkeep', '');

        $this->assertNotInstanceOf(UserIdentity::class, $this->resolver()->resolve());

        $this->fs->dumpFile($this->projectDir.'/html/upload/temp_image/uploaded.jpg', '');

        $identity = $this->resolver()->resolve();

        $this->assertInstanceOf(UserIdentity::class, $identity);
        $this->assertStringContainsString('uploaded.jpg', (string) $identity->source);
    }

    public function testUnreadableDirectoryFallsBackToTheNextCandidate(): void
    {
        if (getmyuid() === 0) {
            // root はパーミッションビットを無視するため, この検証は成立しない
            $this->markTestSkipped('root では読み取り不可のディレクトリを再現できません.');
        }

        // Web サーバー専用に絞った var/sessions (0700 等) は一覧できない。
        // 例外にせず次の候補へ移ることを確認する
        $this->fs->dumpFile($this->projectDir.'/var/sessions/prod/sess_0123456789', '');
        $this->fs->chmod($this->projectDir.'/var/sessions/prod', 0000);
        $this->fs->dumpFile($this->projectDir.'/html/upload/temp_image/uploaded.jpg', '');

        try {
            $identity = $this->resolver()->resolve();
        } finally {
            $this->fs->chmod($this->projectDir.'/var/sessions/prod', 0755);
        }

        $this->assertInstanceOf(UserIdentity::class, $identity);
        $this->assertStringContainsString('uploaded.jpg', $identity->source);
    }

    public function testCurrentUserIsResolvedFromTheProcess(): void
    {
        $cli = $this->resolver()->currentUser();

        $this->assertSame(getmyuid(), $cli->uid);
        $this->assertSame(getmygid(), $cli->gid);
    }

    private function resolver(): WebServerUserResolver
    {
        $values = [
            'kernel.project_dir' => $this->projectDir,
            'kernel.environment' => 'prod',
            'kernel.logs_dir' => $this->projectDir.'/var/log',
            'eccube_temp_image_dir' => $this->projectDir.'/html/upload/temp_image',
            'eccube_temp_refund_request_file_dir' => $this->projectDir.'/html/upload/refund_request/temp',
            'eccube_save_refund_request_file_dir' => $this->projectDir.'/html/upload/refund_request/save',
        ];

        $eccubeConfig = $this->createMock(EccubeConfig::class);
        $eccubeConfig->method('get')->willReturnCallback(static fn (string $key): mixed => $values[$key] ?? null);

        return new WebServerUserResolver($eccubeConfig);
    }
}
