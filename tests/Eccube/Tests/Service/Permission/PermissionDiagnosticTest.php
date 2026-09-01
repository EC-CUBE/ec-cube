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

use Eccube\Service\Permission\FindingSeverity;
use Eccube\Service\Permission\PathOwnership;
use Eccube\Service\Permission\PermissionDiagnostic;
use Eccube\Service\Permission\PermissionFinding;
use Eccube\Service\Permission\PermissionRequirement;
use Eccube\Service\Permission\UserIdentity;
use Eccube\Service\Permission\WriteLane;
use PHPUnit\Framework\TestCase;

/**
 * レーンごとの判定を検証する.
 *
 * evaluate() はファイルシステムに触れないため, 依存を持つコンストラクタを通さずに検証する.
 */
final class PermissionDiagnosticTest extends TestCase
{
    private const WEB_UID = 33;
    private const SSH_UID = 1000;

    public function testWebLaneIsOkWhenWebServerCanWrite(): void
    {
        // Web サーバー所有 0755
        $finding = $this->evaluate(WriteLane::WEB, self::WEB_UID, self::WEB_UID, 0755);

        $this->assertSame(FindingSeverity::OK, $finding->severity);
    }

    public function testWebLaneWarnsWhenWorldWritable(): void
    {
        // ECCUBE_UMASK=0000 の影響でアプリケーションが 0777 で作成したディレクトリ.
        // Web サーバーからは書けるが, 任意のローカルユーザーからも書ける
        $finding = $this->evaluate(WriteLane::WEB, self::WEB_UID, self::WEB_UID, 0777);

        $this->assertSame(FindingSeverity::WARN, $finding->severity);
        $this->assertStringContainsString('ECCUBE_UMASK', (string) $finding->hint);
    }

    public function testWebLaneIsNgWhenWebServerCannotWrite(): void
    {
        // SSH ユーザー所有 0755 は Web サーバーから書き込めない
        $finding = $this->evaluate(WriteLane::WEB, self::SSH_UID, self::SSH_UID, 0755);

        $this->assertSame(FindingSeverity::NG, $finding->severity);
        $this->assertNotNull($finding->hint);
    }

    public function testSshLaneIsOkWhenWebServerCanOnlyRead(): void
    {
        $finding = $this->evaluate(WriteLane::SSH, self::SSH_UID, self::SSH_UID, 0755);

        $this->assertSame(FindingSeverity::OK, $finding->severity);
    }

    public function testSshLaneIsNgWhenWebServerCanWrite(): void
    {
        // other に書き込みビットがあると Web サーバーから書けてしまう
        $finding = $this->evaluate(WriteLane::SSH, self::SSH_UID, self::SSH_UID, 0757);

        $this->assertSame(FindingSeverity::NG, $finding->severity);
    }

    public function testSshLaneIsNgWhenWebServerCannotRead(): void
    {
        $finding = $this->evaluate(WriteLane::SSH, self::SSH_UID, self::SSH_UID, 0700);

        $this->assertSame(FindingSeverity::NG, $finding->severity);
    }

    public function testSshLaneWarnsWhenDiagnosingUserCannotWrite(): void
    {
        // 別の SSH ユーザー所有: Web サーバーは読み取りのみだが, 診断の実行ユーザーからも書き込めない
        $finding = $this->evaluate(WriteLane::SSH, 1001, 1001, 0755);

        $this->assertSame(FindingSeverity::WARN, $finding->severity);
    }

    public function testMissingOptionalPathIsOk(): void
    {
        $requirement = new PermissionRequirement('/path', WriteLane::WEB, 'var/cache/prod', true);
        $finding = $this->diagnostic()->evaluate(
            $requirement,
            new PathOwnership('/path', false, -1, -1, 0, false),
            $this->webServer(),
            $this->cli()
        );

        $this->assertSame(FindingSeverity::OK, $finding->severity);
    }

    public function testMissingRequiredPathIsNg(): void
    {
        $requirement = new PermissionRequirement('/path', WriteLane::WEB, 'html/upload/save_image');
        $finding = $this->diagnostic()->evaluate(
            $requirement,
            new PathOwnership('/path', false, -1, -1, 0, false),
            $this->webServer(),
            $this->cli()
        );

        $this->assertSame(FindingSeverity::NG, $finding->severity);
    }

    public function testUnknownWebServerUserIsWarn(): void
    {
        $requirement = new PermissionRequirement('/path', WriteLane::SSH, 'app/template');
        $finding = $this->diagnostic()->evaluate(
            $requirement,
            new PathOwnership('/path', true, self::SSH_UID, self::SSH_UID, 0777, true),
            null,
            $this->cli()
        );

        // 0777 は誰でも書けるが, Web サーバーの uid が分からない以上 NG とは断定しない
        $this->assertSame(FindingSeverity::WARN, $finding->severity);
    }

    private function evaluate(WriteLane $lane, int $ownerUid, int $ownerGid, int $permissions): PermissionFinding
    {
        $requirement = new PermissionRequirement('/path', $lane, 'path/for/test');

        return $this->diagnostic()->evaluate(
            $requirement,
            new PathOwnership('/path', true, $ownerUid, $ownerGid, $permissions, true),
            $this->webServer(),
            $this->cli()
        );
    }

    private function diagnostic(): PermissionDiagnostic
    {
        /** @var PermissionDiagnostic $diagnostic */
        $diagnostic = (new \ReflectionClass(PermissionDiagnostic::class))->newInstanceWithoutConstructor();

        return $diagnostic;
    }

    private function webServer(): UserIdentity
    {
        return new UserIdentity(self::WEB_UID, self::WEB_UID, 'var/sessions/prod/sess_test');
    }

    private function cli(): UserIdentity
    {
        return new UserIdentity(self::SSH_UID, self::SSH_UID, 'getmyuid()');
    }
}
