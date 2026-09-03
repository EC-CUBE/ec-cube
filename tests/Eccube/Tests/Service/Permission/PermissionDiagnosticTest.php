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
        // umask(0000) の影響でアプリケーションが 0777 で作成したディレクトリ.
        // Web サーバーからは書けるが, 任意のローカルユーザーからも書ける
        $finding = $this->evaluate(WriteLane::WEB, self::WEB_UID, self::WEB_UID, 0777);

        $this->assertSame(FindingSeverity::WARN, $finding->severity);
        $this->assertStringContainsString('umask(0000)', (string) $finding->hint);
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
        // Web サーバーと共有するグループに書き込みビットがあると書けてしまう
        $finding = $this->evaluate(WriteLane::SSH, self::SSH_UID, self::WEB_UID, 0775);

        $this->assertSame(FindingSeverity::NG, $finding->severity);
        $this->assertSame('Web サーバーから書き込み可能です (想定: 読み取りのみ)', $finding->message);
    }

    public function testSshLaneIsNgWhenWorldWritable(): void
    {
        // umask(0000) の影響で CLI が 0777 で作成したディレクトリ
        $finding = $this->evaluate(WriteLane::SSH, self::SSH_UID, self::SSH_UID, 0777);

        $this->assertSame(FindingSeverity::NG, $finding->severity);
        $this->assertSame('任意のローカルユーザーから書き込めます (想定: 読み取りのみ)', $finding->message);
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

    public function testSshLaneSkipsTheCliCheckWhenCurrentUserIsUnknown(): void
    {
        // ext-posix が無効で実行ユーザーを特定できない場合, CLI 側の書き込み可否は判定しない
        $requirement = new PermissionRequirement('/path', WriteLane::SSH, 'app/template');
        $finding = $this->diagnostic()->evaluate(
            $requirement,
            new PathOwnership('/path', true, 1001, 1001, 0755, true),
            $this->webServer(),
            null
        );

        $this->assertSame(FindingSeverity::OK, $finding->severity);
    }

    public function testUnreachablePathIsNg(): void
    {
        // 対象自身は Web サーバー所有 0755 でも, 親が 0700 なら到達できない
        $requirement = new PermissionRequirement('/project/html/upload/temp_image', WriteLane::WEB, 'html/upload/temp_image');
        $finding = $this->diagnostic()->evaluate(
            $requirement,
            new PathOwnership('/project/html/upload/temp_image', true, self::WEB_UID, self::WEB_UID, 0755, true, [
                new PathOwnership('/project', true, self::SSH_UID, self::SSH_UID, 0755, true),
                new PathOwnership('/project/html', true, self::SSH_UID, self::SSH_UID, 0755, true),
                new PathOwnership('/project/html/upload', true, self::SSH_UID, self::SSH_UID, 0700, true),
            ]),
            $this->webServer(),
            $this->cli()
        );

        $this->assertSame(FindingSeverity::NG, $finding->severity);
        $this->assertSame('Web サーバーから到達できません', $finding->message);
        $this->assertStringContainsString('/project/html/upload', (string) $finding->hint);
    }

    public function testUnknownAncestorIsWarn(): void
    {
        $requirement = new PermissionRequirement('/project/var', WriteLane::WEB, 'var');
        $finding = $this->diagnostic()->evaluate(
            $requirement,
            new PathOwnership('/project/var', true, self::WEB_UID, self::WEB_UID, 0755, true, [
                new PathOwnership('/', false, -1, -1, 0, false),
            ]),
            $this->webServer(),
            $this->cli()
        );

        $this->assertSame(FindingSeverity::WARN, $finding->severity);
        $this->assertStringContainsString('open_basedir', (string) $finding->hint);
    }

    public function testDirectoryWithoutExecuteBitIsNg(): void
    {
        // レーン S の 0644 は一覧できても配下のファイルを開けない
        $finding = $this->evaluate(WriteLane::SSH, self::SSH_UID, self::SSH_UID, 0644);

        $this->assertSame(FindingSeverity::NG, $finding->severity);
        $this->assertSame('Web サーバーから読み取れません', $finding->message);
        $this->assertStringContainsString('実行 (x) 権限', (string) $finding->hint);
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
            new PathOwnership('/path', true, self::SSH_UID, self::SSH_UID, 0750, true),
            null,
            $this->cli()
        );

        // Web サーバーの uid が分からなければ, 読み取れるかどうかも書けるかどうかも判定できない
        $this->assertSame(FindingSeverity::WARN, $finding->severity);
    }

    public function testWorldWritableSshLaneIsNgEvenWhenWebServerUserIsUnknown(): void
    {
        $requirement = new PermissionRequirement('/path', WriteLane::SSH, 'app/template');
        $finding = $this->diagnostic()->evaluate(
            $requirement,
            new PathOwnership('/path', true, self::SSH_UID, self::SSH_UID, 0777, true),
            null,
            $this->cli()
        );

        // 任意のローカルユーザーから書ける以上, Web サーバーの uid を問わずレーン S の前提が崩れている
        $this->assertSame(FindingSeverity::NG, $finding->severity);
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
        return new UserIdentity(self::SSH_UID, self::SSH_UID, 'posix_geteuid()');
    }
}
