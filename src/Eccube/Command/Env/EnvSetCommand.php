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

namespace Eccube\Command\Env;

use Eccube\Exception\ContentWriteException;
use Eccube\Service\EnvFileService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;
use Symfony\Component\Process\Process;

/**
 * .env の値を書き換える.
 *
 * 権限を分離した構成では .env は CLI ユーザーの所有 (レーン S) となり,
 * 管理画面 (セキュリティ管理・テンプレート管理) からは書き換えられない.
 */
#[AsCommand(name: 'eccube:env:set', description: '.env の値を設定します.')]
final class EnvSetCommand extends Command
{
    /**
     * 本処理は完了したが, 手動での操作が必要な状態を表す終了コード.
     *
     * PluginCommandTrait::EXIT_MANUAL_ACTION_REQUIRED と同じ意味づけ.
     * (2 は Symfony の Command::INVALID が使用済みのため避ける)
     */
    public const EXIT_MANUAL_ACTION_REQUIRED = 3;

    public function __construct(
        private readonly EnvFileService $envFileService,
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('assignments', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'KEY=VALUE 形式の設定 (複数指定可)')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, '変更内容を表示するだけで適用しない')
            ->addOption('no-cache-clear', null, InputOption::VALUE_NONE, 'ビルドディレクトリの再生成を省略する')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, '出力形式 (table|json)', 'table')
            ->setHelp(<<<'EOF'
                <info>%command.name%</info> は .env の値を設定します.

                  <info>php %command.full_name% ECCUBE_TEMPLATE_CODE=default</info>
                  <info>php %command.full_name% ECCUBE_FORCE_SSL=1 TRUSTED_HOSTS='^example\.com$' --dry-run</info>

                値は .env の行へそのまま書き出します. 空白や記号を含む値は
                <info>KEY="'値'"</info> のようにクォートを含めて渡してください.

                .env の変更はコンパイル済みコンテナへ焼き込まれる値 (テンプレートのパス等) を含むため,
                書き込みの後に eccube:cache:build を別プロセスで実行します
                (同一プロセスでは起動時に読み込んだ古い値が焼き込まれるため).
                EOF
            );
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $format = (string) $input->getOption('format');
        if (!in_array($format, ['table', 'json'], true)) {
            $io->error('--format は table / json のいずれかで指定してください.');

            return Command::INVALID;
        }

        try {
            /** @var list<string> $assignments */
            $assignments = (array) $input->getArgument('assignments');
            $values = self::parseAssignments($assignments);
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        }

        $reasons = $this->envFileService->getIneffectiveReasons();
        if (in_array(EnvFileService::REASON_NOT_FOUND, $reasons, true)) {
            $io->error(sprintf('%s が存在しません. .env を作成してから実行してください.', $this->envFileService->getPath()));

            return 1;
        }
        if (in_array(EnvFileService::REASON_NOT_WRITABLE, $reasons, true)) {
            $io->error([
                sprintf('%s へ書き込めません.', $this->envFileService->getPath()),
                '.env を所有するユーザー (レーン S) で実行してください.'
                .' 期待値と実際の所有者は bin/console eccube:doctor:permissions で確認できます.',
            ]);

            return 1;
        }

        $changes = [];
        foreach ($values as $key => $value) {
            $before = $this->envFileService->get($key);
            if ($before !== $value) {
                $changes[$key] = [$before ?? '', $value];
            }
        }

        $dryRun = (bool) $input->getOption('dry-run');

        if ('json' === $format) {
            $output->writeln((string) json_encode([
                'dry_run' => $dryRun,
                'path' => $this->envFileService->getPath(),
                'changes' => $changes,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        } else {
            foreach ($changes as $key => [$before, $after]) {
                $io->writeln(sprintf('  %s: %s -> %s', $key, '' === $before ? '(未設定)' : $before, $after));
            }
        }

        if ($dryRun) {
            if ('json' !== $format) {
                $io->note('dry-run のため適用していません.');
            }

            return 0;
        }

        if ([] === $changes) {
            if ('json' !== $format) {
                $io->text('変更はありません.');
            }

            return 0;
        }

        try {
            $this->envFileService->set($values);
        } catch (ContentWriteException $e) {
            $io->error($e->getMessage());

            return 1;
        }

        if ('json' !== $format) {
            // 値は差分表示にのみ出す. 成功メッセージにはキー名だけを載せる
            $io->success(sprintf('%s を更新しました: %s', $this->envFileService->getPath(), implode(', ', array_keys($changes))));
        }

        $manualActionRequired = false;

        if (in_array(EnvFileService::REASON_LOCAL_PHP, $reasons, true)) {
            $io->warning([
                '.env.local.php があるため, .env の変更は実行時に反映されません.',
                'composer dump-env <環境> を実行して .env.local.php を作り直してください.',
            ]);
            $manualActionRequired = true;
        }

        $overriddenKeys = $this->envFileService->getOverriddenKeys(array_keys($changes));
        if ([] !== $overriddenKeys) {
            $io->warning(sprintf(
                'OS の環境変数 (またはカスケードファイル) が優先されるため, 次のキーは .env を変更しても反映されません: %s',
                implode(', ', $overriddenKeys)
            ));
            $manualActionRequired = true;
        }

        if (!$input->getOption('no-cache-clear') && !$this->rebuild($io)) {
            $manualActionRequired = true;
        }

        return $manualActionRequired ? self::EXIT_MANUAL_ACTION_REQUIRED : 0;
    }

    /**
     * ビルドディレクトリを再生成する.
     *
     * 現在のプロセスは起動時に読み込んだ .env の値を保持しているため, 必ず別プロセスで実行する.
     *
     * @return bool 再生成できた場合 true
     */
    private function rebuild(SymfonyStyle $io): bool
    {
        $command = ['bin/console', 'eccube:cache:build'];
        try {
            $io->text(sprintf('<info>Run %s</info>...', implode(' ', $command)));
            // cwd を明示しない場合, プロジェクトルート以外から実行すると bin/console を解決できない
            $process = new Process($command, $this->projectDir);
            // Process の既定タイムアウトは 60 秒. 超過すると子プロセスが kill され,
            // ビルドディレクトリが中途半端な状態のまま残る
            $process->setTimeout(null);
            $process->mustRun();
            $io->text($process->getOutput());

            return true;
        } catch (ProcessExceptionInterface $e) {
            $io->error($e->getMessage());
            $io->warning(sprintf(
                'ビルドディレクトリを再生成できませんでした. 書き込み権限のあるユーザーで %s を実行してください.',
                implode(' ', $command)
            ));

            return false;
        }
    }

    /**
     * KEY=VALUE の並びを連想配列へ変換する.
     *
     * 値に = を含められるよう, 最初の = だけで分割する.
     *
     * @param list<string> $assignments
     *
     * @return array<string, string>
     *
     * @throws \InvalidArgumentException
     */
    private static function parseAssignments(array $assignments): array
    {
        $values = [];
        foreach ($assignments as $assignment) {
            $pos = strpos($assignment, '=');
            if (false === $pos || 0 === $pos) {
                throw new \InvalidArgumentException(sprintf('KEY=VALUE の形式で指定してください: %s', $assignment));
            }

            $key = substr($assignment, 0, $pos);
            if (!preg_match('/\A[A-Za-z_][A-Za-z0-9_]*\z/', $key)) {
                throw new \InvalidArgumentException(sprintf('環境変数名として使用できません: %s', $key));
            }

            $values[$key] = substr($assignment, $pos + 1);
        }

        return $values;
    }
}
