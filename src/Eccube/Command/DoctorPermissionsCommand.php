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

namespace Eccube\Command;

use Eccube\Service\Permission\DiagnosticReport;
use Eccube\Service\Permission\FindingSeverity;
use Eccube\Service\Permission\PermissionDiagnostic;
use Eccube\Service\Permission\PermissionFinding;
use Eccube\Service\Permission\UserIdentity;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 書き込み先の権限レーンと, 実際の所有者・パーミッションの差分を出力する.
 *
 * 判定は所有者 uid / グループ gid / パーミッションビットからの推定であり,
 * 補助グループ・ACL・SELinux は考慮しない.
 */
#[AsCommand(name: 'eccube:doctor:permissions', description: 'Web サーバーと CLI の書き込み権限を診断します.')]
final class DoctorPermissionsCommand extends Command
{
    /**
     * @var list<string>
     */
    private const FORMATS = ['table', 'json'];

    public function __construct(private readonly PermissionDiagnostic $permissionDiagnostic)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('format', null, InputOption::VALUE_REQUIRED, sprintf('出力形式 (%s)', implode('|', self::FORMATS)), 'table');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $format = (string) $input->getOption('format');
        if (!in_array($format, self::FORMATS, true)) {
            $io->error(sprintf('--format は %s のいずれかで指定してください.', implode(' / ', self::FORMATS)));

            return Command::INVALID;
        }

        $report = $this->permissionDiagnostic->run();

        if ($format === 'json') {
            $output->writeln((string) json_encode($report->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        } else {
            $this->renderTable($io, $report);
        }

        return $report->hasError() ? 1 : 0;
    }

    private function renderTable(SymfonyStyle $io, DiagnosticReport $report): void
    {
        $io->title('書き込み権限の診断');

        if ($report->webServer === null) {
            $io->warning([
                'Web サーバーの実行ユーザーを特定できませんでした.',
                'Web サーバーでのみ生成されるファイル (var/sessions/{env} 配下, html/upload 配下) が見つからないためです.',
                'サイトへ一度アクセスしてから再実行するか, ps aux | grep -E \'php-fpm|httpd|apache2\' で確認してください.',
            ]);
        } else {
            $io->text(sprintf(
                'Web サーバーの実行ユーザー: uid=%d gid=%d (%s の所有者から判定)',
                $report->webServer->uid,
                $report->webServer->gid,
                $report->webServer->source
            ));
        }

        if ($report->cli instanceof UserIdentity) {
            $io->text(sprintf('診断の実行ユーザー: uid=%d gid=%d', $report->cli->uid, $report->cli->gid));
        } else {
            $io->warning([
                '診断の実行ユーザーを特定できませんでした.',
                'ext-posix が無効なため, 実効 uid / gid を取得できません.',
                'レーン S を CLI から書き換えられるかどうかは判定していません.',
            ]);
        }

        $io->newLine();

        if ($report->webServer !== null && $report->cli instanceof UserIdentity && $report->webServer->uid === $report->cli->uid) {
            $io->note([
                'Web サーバーと診断の実行ユーザーが同じ uid です.',
                '共有ホスティング (suexec 等) で同一ユーザーとして動作している場合, 権限によるレーンの分離はできません.',
                '別ユーザーで運用しているはずの環境では, 判定に使ったファイルが CLI で生成された可能性があります.',
            ]);
        }

        $rows = [];
        foreach ($report->findings as $finding) {
            $rows[] = [
                $this->decorate($finding->severity),
                $finding->requirement->lane->value,
                $finding->requirement->label,
                $finding->ownership->exists ? sprintf('%d:%d', $finding->ownership->uid, $finding->ownership->gid) : '-',
                $finding->ownership->exists ? $finding->ownership->permissionsString() : '-',
                $finding->message,
            ];
        }

        $io->table(['', 'レーン', 'パス', '所有者', '権限', '判定'], $rows);

        $this->renderDetails($io, $report);

        $io->text('判定は所有者 uid / グループ gid / パーミッションビットからの推定です. 補助グループ・ACL・SELinux は考慮していません.');
        $io->newLine();

        $summary = sprintf(
            'OK: %d / WARN: %d / NG: %d',
            $report->countBy(FindingSeverity::OK),
            $report->countBy(FindingSeverity::WARN),
            $report->countBy(FindingSeverity::NG)
        );

        if ($report->hasError()) {
            $io->error($summary);
        } else {
            $io->success($summary);
        }
    }

    private function renderDetails(SymfonyStyle $io, DiagnosticReport $report): void
    {
        foreach ($report->findings as $finding) {
            if ($finding->severity === FindingSeverity::OK) {
                continue;
            }

            $io->text(sprintf('%s %s — %s', $this->decorate($finding->severity), $finding->requirement->label, $finding->message));

            foreach ($this->detailLines($finding) as $line) {
                $io->text('    '.$line);
            }
        }

        $io->newLine();
    }

    /**
     * @return list<string>
     */
    private function detailLines(PermissionFinding $finding): array
    {
        $lines = [];

        if ($finding->hint !== null) {
            $lines[] = $finding->hint;
        }

        if ($finding->requirement->note !== null) {
            $lines[] = $finding->requirement->note;
        }

        return $lines;
    }

    private function decorate(FindingSeverity $severity): string
    {
        return match ($severity) {
            FindingSeverity::OK => '<info>[OK]</info>',
            FindingSeverity::WARN => '<comment>[WARN]</comment>',
            FindingSeverity::NG => '<error>[NG]</error>',
        };
    }
}
