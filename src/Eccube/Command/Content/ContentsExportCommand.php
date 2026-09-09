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
use Eccube\Service\Content\ContentsExporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * コンテンツ定義をアーカイブへ書き出す.
 *
 * テンプレートの本文は複製しない (ContentsArchive のクラスコメントを参照).
 */
#[AsCommand(name: 'eccube:contents:export', description: 'コンテンツ定義をディレクトリへ書き出します.')]
final class ContentsExportCommand extends Command
{
    use ContentCommandTrait;
    use ContentsArchiveTrait;

    public function __construct(private readonly ContentsExporter $contentsExporter)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('to', null, InputOption::VALUE_REQUIRED, '書き出し先のディレクトリ', ContentsArchive::DEFAULT_DIR)
            ->addOption('dry-run', null, InputOption::VALUE_NONE, '書き出す内容を表示するだけで保存しない');
        $this->addSectionOptions();
        $this->addFormatOption();
        $this->setHelp(<<<'EOF'
            <info>%command.name%</info> は Git に残らない DB 側のコンテンツ定義を yaml へ書き出します.

              <info>php %command.full_name%</info>
              <info>php %command.full_name% --to=app/contents --only=pages,blocks</info>
              <info>php %command.full_name% --include=user_data</info>

            テンプレート (twig / css / js) の本文は書き出しません. リポジトリ本来の位置
            (src/Eccube/Resource/template, app/template, html/user_data) にあるものを Git で管理し、
            本コマンドはそれと対になる dtb_page / dtb_block / dtb_mail_template / dtb_layout の
            定義だけを扱います. 本文を複製すると二重管理になり, upstream との git merge で
            解決できなくなるためです.

            html/user_data は customize.css / customize.js 以外が .gitignore で除外されているため
            既定では扱いません. リポジトリ丸ごと管理する構成では --include=user_data を指定します.
            EOF
        );
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

        $dir = $this->resolveArchiveDir((string) $input->getOption('to'));
        $dryRun = (bool) $input->getOption('dry-run');

        try {
            $summary = $this->contentsExporter->export($dir, $sections, $dryRun);
        } catch (ContentValidationException $e) {
            $io->error(array_merge(['コンテンツ定義を書き出せません.'], $e->getErrors()));

            return 1;
        } catch (ContentWriteException $e) {
            return $this->reportWriteFailure($io, 'コンテンツ定義を書き出せません.', $e);
        }

        if ('json' === $format) {
            $output->writeln((string) json_encode(
                ['dry_run' => $dryRun, 'dir' => $dir, 'sections' => $summary],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ));

            return 0;
        }

        $io->table(
            ['セクション', '件数', '出力先'],
            array_map(
                static fn (array $row): array => [$row['section'], (string) $row['count'], $row['path']],
                $summary
            )
        );

        if ($dryRun) {
            $io->note('dry-run のため書き出していません.');

            return 0;
        }

        $io->success(sprintf('%s へ書き出しました.', $dir));

        return 0;
    }
}
