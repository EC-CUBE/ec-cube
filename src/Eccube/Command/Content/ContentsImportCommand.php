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

use Eccube\Exception\ContentValidationException;
use Eccube\Exception\ContentWriteException;
use Eccube\Service\Content\ContentsArchive;
use Eccube\Service\Content\ContentsImporter;
use Eccube\Service\Content\ContentsImportResult;
use Eccube\Service\Content\ContentStatus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * アーカイブからコンテンツ定義を取り込む.
 */
#[AsCommand(name: 'eccube:contents:import', description: 'ディレクトリからコンテンツ定義を取り込みます.', help: <<<'TXT'
<info>%command.name%</info> は eccube:contents:export が書き出した yaml を取り込みます.

  <info>php %command.full_name% --dry-run</info>
  <info>php %command.full_name%</info>
  <info>php %command.full_name% --prune --dry-run</info>

テンプレートの本文はアーカイブに含まれません. リポジトリ本来の位置にあるものが
そのまま使われるため, 取り込みでは本文が変わらない限りテンプレートを書き換えません.

--prune はアーカイブに無いものを削除します. 削除できるのはユーザーが作成した
ページ (EDIT_TYPE_USER), 削除可能なブロック・メールテンプレート, どのページからも
参照されていないレイアウトだけです. 先に --dry-run で対象を確認してください.
TXT)]
final class ContentsImportCommand extends Command
{
    use ContentCommandTrait;
    use ContentsArchiveTrait;

    public function __construct(private readonly ContentsImporter $contentsImporter)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, '取り込み元のディレクトリ', ContentsArchive::DEFAULT_DIR)
            ->addOption('prune', null, InputOption::VALUE_NONE, 'アーカイブに無いものを削除する')
            ->addOption('continue-on-error', null, InputOption::VALUE_NONE, 'エラーで中断せず最後まで進める')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, '変更内容を表示するだけで適用しない')
            ->addOption('no-cache-clear', null, InputOption::VALUE_NONE, 'キャッシュの削除を省略する');
        $this->addSectionOptions();
        $this->addFormatOption();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $format = (string) $input->getOption('format');
        if (!$this->isValidFormat($format)) {
            $this->invalidFormat($io);

            return Command::INVALID;
        }

        try {
            $sections = $this->resolveSections($input);
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        }

        $dir = $this->resolveArchiveDir((string) $input->getOption('from'));
        $dryRun = (bool) $input->getOption('dry-run');
        $prune = (bool) $input->getOption('prune');

        try {
            $report = $this->contentsImporter->import(
                $dir,
                $sections,
                $dryRun,
                $prune,
                (bool) $input->getOption('continue-on-error')
            );
        } catch (ContentValidationException $e) {
            $io->error(array_merge(
                ['コンテンツ定義を取り込めません.'],
                $e->getErrors(),
                ['--dry-run で事前に差分を確認できます.']
            ));

            return Command::FAILURE;
        } catch (ContentWriteException $e) {
            return $this->reportWriteFailure($io, 'コンテンツ定義を取り込めません.', $e);
        }

        $results = $report['results'];
        $warnings = $report['warnings'];

        if ('json' === $format) {
            $output->writeln((string) json_encode([
                'dry_run' => $dryRun,
                'dir' => $dir,
                'warnings' => $warnings,
                'results' => array_map(static fn (ContentsImportResult $r): array => $r->toArray(), $results),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return $this->exitCode($results, $dryRun, $input, $io, true);
        }

        foreach ($warnings as $warning) {
            $io->warning($warning);
        }

        // 変更の無いものは件数だけ示し, 差分のあるものを読めるようにする
        $changed = array_values(array_filter(
            $results,
            static fn (ContentsImportResult $r): bool => $r->isError() || ContentStatus::Unchanged !== $r->status
        ));
        if ([] !== $changed) {
            $io->table(
                ['セクション', '対象', '結果'],
                array_map(
                    static fn (ContentsImportResult $r): array => [
                        $r->section,
                        $r->identifier,
                        $r->isError() ? '<fg=red>error: '.$r->error.'</>' : (string) $r->status?->value,
                    ],
                    $changed
                )
            );
        }

        $io->text(sprintf('対象 %d 件 / 変更 %d 件', count($results), count($changed)));

        return $this->exitCode($results, $dryRun, $input, $io, false);
    }

    /**
     * @param list<ContentsImportResult> $results
     */
    private function exitCode(array $results, bool $dryRun, InputInterface $input, SymfonyStyle $io, bool $quiet): int
    {
        $errors = array_filter($results, static fn (ContentsImportResult $r): bool => $r->isError());
        if ([] !== $errors) {
            if (!$quiet) {
                $io->error(sprintf('%d 件を取り込めませんでした.', count($errors)));
            }

            return 1;
        }

        $applied = array_filter(
            $results,
            static fn (ContentsImportResult $r): bool => ContentStatus::Unchanged !== $r->status
        );
        if ($dryRun || [] === $applied || $input->getOption('no-cache-clear')) {
            if (!$quiet && $dryRun) {
                $io->note('dry-run のため適用していません.');
            }

            return 0;
        }

        if (!$quiet) {
            $io->success('取り込みました.');
        }

        return $this->clearContentCache($io) ? 0 : self::EXIT_MANUAL_ACTION_REQUIRED;
    }
}
