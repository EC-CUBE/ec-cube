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

namespace Eccube\Tests\Command;

use Eccube\Command\DoctorPermissionsCommand;
use Eccube\Service\Permission\DiagnosticReport;
use Eccube\Service\Permission\FindingSeverity;
use Eccube\Service\Permission\PathOwnership;
use Eccube\Service\Permission\PermissionDiagnostic;
use Eccube\Service\Permission\PermissionFinding;
use Eccube\Service\Permission\PermissionRequirement;
use Eccube\Service\Permission\UserIdentity;
use Eccube\Service\Permission\WriteLane;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * 出力形式と終了コードを検証する.
 *
 * 実際の権限は環境ごとに異なるため, 診断結果は差し替えて検証する.
 */
final class DoctorPermissionsCommandTest extends TestCase
{
    public function testReturnsSuccessWhenNoErrorFound(): void
    {
        $tester = $this->tester($this->report(FindingSeverity::OK));

        $this->assertSame(0, $tester->execute([]));
    }

    public function testReturnsFailureWhenErrorFound(): void
    {
        $tester = $this->tester($this->report(FindingSeverity::NG));

        $this->assertSame(1, $tester->execute([]));
    }

    public function testWarningDoesNotFailTheCommand(): void
    {
        $tester = $this->tester($this->report(FindingSeverity::WARN));

        $this->assertSame(0, $tester->execute([]));
    }

    public function testInvalidFormatIsRejected(): void
    {
        $tester = $this->tester($this->report(FindingSeverity::OK));

        // 診断を実行する前にオプションを検証する
        $this->assertSame(Command::INVALID, $tester->execute(['--format' => 'yaml']));
    }

    public function testJsonFormat(): void
    {
        $tester = $this->tester($this->report(FindingSeverity::NG));
        $tester->execute(['--format' => 'json']);

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($tester->getDisplay(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame(['uid' => 33, 'gid' => 33, 'source' => 'var/sessions/prod/sess_test'], $decoded['web_server']);
        $this->assertSame(['ok' => 0, 'warn' => 0, 'ng' => 1], $decoded['summary']);
        $this->assertSame('app/template', $decoded['findings'][0]['path']);
        $this->assertSame('ssh', $decoded['findings'][0]['lane']);
        $this->assertSame('ng', $decoded['findings'][0]['severity']);
        $this->assertSame('0777', $decoded['findings'][0]['permissions']);
    }

    public function testTableFormatShowsPathAndHint(): void
    {
        $tester = $this->tester($this->report(FindingSeverity::NG));
        $tester->execute([], ['decorated' => false]);
        $display = $tester->getDisplay();

        $this->assertStringContainsString('app/template', $display);
        $this->assertStringContainsString('修正してください', $display);
        $this->assertStringContainsString('uid=33', $display);
    }

    public function testUnknownWebServerUserIsReported(): void
    {
        $report = new DiagnosticReport([], null, new UserIdentity(1000, 1000, 'posix_geteuid()'));
        $tester = $this->tester($report);
        $tester->execute([], ['decorated' => false]);

        $this->assertStringContainsString('特定できませんでした', $tester->getDisplay());
    }

    public function testUnknownCliUserIsReported(): void
    {
        $report = new DiagnosticReport([], new UserIdentity(33, 33, 'var/sessions/prod/sess_test'), null);
        $tester = $this->tester($report);
        $tester->execute([], ['decorated' => false]);

        $this->assertStringContainsString('診断の実行ユーザーを特定できませんでした', $tester->getDisplay());
    }

    public function testUnknownCliUserIsNullInJson(): void
    {
        $report = new DiagnosticReport([], new UserIdentity(33, 33, 'var/sessions/prod/sess_test'), null);
        $tester = $this->tester($report);
        $tester->execute(['--format' => 'json']);

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($tester->getDisplay(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertNull($decoded['cli']);
    }

    private function tester(DiagnosticReport $report): CommandTester
    {
        $diagnostic = $this->createMock(PermissionDiagnostic::class);
        $diagnostic->method('run')->willReturn($report);

        return new CommandTester(new DoctorPermissionsCommand($diagnostic));
    }

    private function report(FindingSeverity $severity): DiagnosticReport
    {
        $requirement = new PermissionRequirement('/path/app/template', WriteLane::SSH, 'app/template');
        $finding = new PermissionFinding(
            $requirement,
            new PathOwnership('/path/app/template', true, 1000, 1000, 0777, true),
            $severity,
            'テスト用の判定',
            '修正してください.'
        );

        return new DiagnosticReport(
            [$finding],
            new UserIdentity(33, 33, 'var/sessions/prod/sess_test'),
            new UserIdentity(1000, 1000, 'posix_geteuid()')
        );
    }
}
