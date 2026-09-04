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

namespace Eccube\Command\Content;

use Eccube\Common\EccubeConfig;
use Eccube\Service\Content\ContentResult;
use Eccube\Service\Content\ContentStatus;
use Eccube\Util\CacheUtil;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * コンテンツ操作コマンドの共通処理.
 *
 * 入出力 (stdin / --format / --dry-run) と終了コード, キャッシュの削除を担う.
 */
trait ContentCommandTrait
{
    /**
     * 本処理は完了したが, 手動での操作が必要な状態を表す終了コード.
     *
     * PluginCommandTrait::EXIT_MANUAL_ACTION_REQUIRED と同じ意味づけ.
     * (2 は Symfony の Command::INVALID が使用済みのため避ける)
     */
    public const EXIT_MANUAL_ACTION_REQUIRED = 3;

    /**
     * @var list<string>
     */
    private const FORMATS = ['table', 'json'];

    /**
     * 差分表示の上限行数.
     */
    private const DIFF_MAX_LINES = 100;

    protected CacheUtil $cacheUtil;

    protected EccubeConfig $eccubeConfig;

    #[Required]
    public function setCacheUtil(CacheUtil $cacheUtil): void
    {
        $this->cacheUtil = $cacheUtil;
    }

    #[Required]
    public function setEccubeConfig(EccubeConfig $eccubeConfig): void
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    protected function addFormatOption(): void
    {
        $this->addOption('format', null, InputOption::VALUE_REQUIRED, sprintf('出力形式 (%s)', implode('|', self::FORMATS)), 'table');
    }

    /**
     * 本文の入力と, 副作用を制御するオプションを追加する.
     */
    protected function addWriteOptions(): void
    {
        $this
            ->addOption('body', null, InputOption::VALUE_REQUIRED, '本文. "-" を指定すると標準入力から読み込む')
            ->addOption('body-file', null, InputOption::VALUE_REQUIRED, '本文を読み込むファイルのパス')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, '変更内容を表示するだけで適用しない')
            ->addOption('no-cache-clear', null, InputOption::VALUE_NONE, 'キャッシュの削除を省略する');
        $this->addFormatOption();
    }

    protected function isValidFormat(string $format): bool
    {
        return in_array($format, self::FORMATS, true);
    }

    protected function invalidFormat(SymfonyStyle $io): void
    {
        $io->error(sprintf('--format は %s のいずれかで指定してください.', implode(' / ', self::FORMATS)));
    }

    /**
     * --body / --body-file から本文を取得する.
     *
     * どちらも指定されていない場合は null を返す (現在の内容を維持する).
     *
     * @throws \InvalidArgumentException 両方が指定された場合, またはファイルを読めない場合
     */
    protected function readBody(InputInterface $input, string $bodyOption = 'body', string $fileOption = 'body-file'): ?string
    {
        $body = $input->getOption($bodyOption);
        $file = $input->getOption($fileOption);

        if (null !== $body && null !== $file) {
            throw new \InvalidArgumentException(sprintf('--%s と --%s は同時に指定できません.', $bodyOption, $fileOption));
        }

        if (null !== $file) {
            if (!is_file((string) $file) || !is_readable((string) $file)) {
                throw new \InvalidArgumentException(sprintf('ファイルを読み込めません: %s', (string) $file));
            }

            return (string) file_get_contents((string) $file);
        }

        if ('-' === $body) {
            return $this->readStdin($input);
        }

        return null === $body ? null : (string) $body;
    }

    private function readStdin(InputInterface $input): string
    {
        $stream = $input instanceof StreamableInputInterface ? $input->getStream() : null;
        $stream ??= \STDIN;

        return (string) stream_get_contents($stream);
    }

    /**
     * テンプレートの更新を反映するためキャッシュを削除する.
     *
     * 権限を分離した構成では build ディレクトリを CLI から削除できないため,
     * その場合は本処理を完了させたうえで手動実行を案内する.
     *
     * @return bool すべて削除できた場合 true
     */
    protected function clearContentCache(SymfonyStyle $io): bool
    {
        // 削除の可否は実行前に判定する. 権限が無い場合, 削除処理は例外にはならず
        // 単に対象が残る (CacheUtil::clearTwigCache / cache:pool:clear).
        $buildDir = rtrim((string) $this->eccubeConfig->get('kernel.build_dir'), '/');
        $runtimeDir = rtrim((string) $this->eccubeConfig->get('eccube_runtime_dir'), '/');
        $buildTwigDir = $buildDir.'/twig';
        $runtimeTwigDir = $runtimeDir.'/twig';
        $poolDir = $runtimeDir.'/pools';

        $buildTwigClearable = !is_dir($buildTwigDir) || is_writable($buildDir);
        // ディレクトリの削除には親ディレクトリの書き込み権限が必要になる
        $runtimeTwigClearable = !is_dir($runtimeTwigDir) || is_writable($runtimeDir);
        $poolClearable = !is_dir($poolDir) || is_writable($poolDir);

        $this->cacheUtil->clearTwigCache();
        $this->cacheUtil->clearDoctrineCache();

        $cleared = true;

        if (!$buildTwigClearable) {
            $io->warning([
                sprintf('%s を削除できないため, 更新したテンプレートが反映されません.', $buildTwigDir),
                '書き込み権限のあるユーザーで bin/console eccube:cache:build を実行してください.',
            ]);
            $cleared = false;
        }

        // 実行時キャッシュは Web サーバー所有 (レーン W) のため, CLI からは削除できない.
        if (!$runtimeTwigClearable || !$poolClearable) {
            $io->warning([
                sprintf('%s を削除できないため, 実行時キャッシュに古い内容が残ります.', $runtimeDir),
                sprintf(
                    'Web サーバーのユーザーで bin/console cache:pool:clear %s を実行するか, 管理画面のキャッシュ管理から削除してください.',
                    CacheUtil::DOCTRINE_APP_CACHE_KEY
                ),
            ]);
            $cleared = false;
        }

        return $cleared;
    }

    protected function renderResult(SymfonyStyle $io, OutputInterface $output, string $format, ContentResult $result, bool $dryRun): void
    {
        if ('json' === $format) {
            $output->writeln((string) json_encode(
                ['dry_run' => $dryRun] + $result->toArray(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ));

            return;
        }

        foreach ($result->fieldChanges as $field => [$before, $after]) {
            $io->writeln(sprintf('  %s: %s -> %s', $field, self::inline($before), self::inline($after)));
        }
        foreach ($result->fileChanges as $path => [$before, $after]) {
            $this->renderFileDiff($io, $path, $before, $after);
        }
        foreach ($result->removedPaths as $path) {
            $io->writeln(sprintf('  removed: %s', $path));
        }

        $message = sprintf('%s: %s', $result->status->value, $result->identifier);
        if ($dryRun) {
            $io->note($message.' (dry-run のため適用していません)');

            return;
        }

        if (ContentStatus::Unchanged === $result->status) {
            $io->text($message);

            return;
        }

        $io->success($message);
    }

    /**
     * 変更前後の差分を表示する.
     *
     * 先頭・末尾の一致行を除いた範囲のみを出力する簡易差分.
     */
    private function renderFileDiff(SymfonyStyle $io, string $path, string $before, string $after): void
    {
        $io->writeln(sprintf('--- %s', $path));

        $beforeLines = '' === $before ? [] : explode("\n", $before);
        $afterLines = '' === $after ? [] : explode("\n", $after);

        $start = 0;
        while ($start < count($beforeLines) && $start < count($afterLines) && $beforeLines[$start] === $afterLines[$start]) {
            ++$start;
        }
        $endBefore = count($beforeLines) - 1;
        $endAfter = count($afterLines) - 1;
        while ($endBefore >= $start && $endAfter >= $start && $beforeLines[$endBefore] === $afterLines[$endAfter]) {
            --$endBefore;
            --$endAfter;
        }

        $lines = [];
        for ($i = $start; $i <= $endBefore; ++$i) {
            $lines[] = '<fg=red>- '.$beforeLines[$i].'</>';
        }
        for ($i = $start; $i <= $endAfter; ++$i) {
            $lines[] = '<fg=green>+ '.$afterLines[$i].'</>';
        }

        $total = count($lines);
        foreach (array_slice($lines, 0, self::DIFF_MAX_LINES) as $line) {
            $io->writeln($line);
        }
        if ($total > self::DIFF_MAX_LINES) {
            $io->writeln(sprintf('  ... (他 %d 行)', $total - self::DIFF_MAX_LINES));
        }
    }

    private static function inline(string $value): string
    {
        $value = str_replace("\n", ' ', $value);

        return mb_strlen($value) > 60 ? mb_substr($value, 0, 60).'...' : $value;
    }
}
