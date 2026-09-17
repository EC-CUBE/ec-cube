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

use Eccube\Entity\MailTemplate;
use Eccube\Exception\ContentWriteException;
use Eccube\Repository\MailTemplateRepository;
use Eccube\Service\Content\MailTemplateContentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * メールテンプレート (dtb_mail_template + twig) を削除する.
 *
 * 管理画面と同じく, 削除可能なテンプレートのみ削除できる.
 */
#[AsCommand(name: 'eccube:mail-template:remove', description: 'メールテンプレートを削除します.', help: <<<'TXT'
<info>%command.name%</info> は dtb_mail_template とテンプレートファイルを対で削除します.

  <info>php %command.full_name% --file-name=custom_mail --force</info>

初期データのテンプレート (deletable = false) は削除できません.
HTML パートのファイルも併せて削除します.
TXT)]
final class MailTemplateRemoveCommand extends Command
{
    use ContentCommandTrait;

    public function __construct(
        private readonly MailTemplateContentService $mailTemplateContentService,
        private readonly MailTemplateRepository $mailTemplateRepository,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('file-name', null, InputOption::VALUE_REQUIRED, '対象テンプレートのファイル名 (xxx / Mail/xxx.twig のどちらでも可)')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, '対象テンプレートの ID')
            ->addOption('force', 'f', InputOption::VALUE_NONE, '確認せずに削除する')
            ->addOption('no-cache-clear', null, InputOption::VALUE_NONE, 'キャッシュの削除を省略する');
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

        $fileName = $input->getOption('file-name');
        $id = $input->getOption('id');
        if ((null === $fileName || '' === $fileName) && (null === $id || '' === $id)) {
            $io->error('--file-name または --id を指定してください.');

            return Command::INVALID;
        }

        $identifier = (string) ($fileName ?? $id);
        $Mail = null === $fileName || '' === $fileName
            ? $this->mailTemplateRepository->find((int) $id)
            : $this->mailTemplateContentService->findByFileName((string) $fileName);
        if (!$Mail instanceof MailTemplate) {
            $io->error(sprintf('メールテンプレートが見つかりません: %s', $identifier));

            return Command::FAILURE;
        }

        if (!$Mail->isDeletable()) {
            $io->error(sprintf('削除できないメールテンプレートです: %s', (string) $Mail->getFileName()));

            return Command::FAILURE;
        }

        if (!$input->getOption('force') && !$io->confirm(sprintf('%s を削除しますか?', (string) $Mail->getFileName()), false)) {
            $io->text('中止しました.');

            return Command::FAILURE;
        }

        try {
            $result = $this->mailTemplateContentService->remove($Mail);
        } catch (ContentWriteException $e) {
            return $this->reportWriteFailure(
                $io,
                sprintf('メールテンプレートを削除できません: %s', (string) $Mail->getFileName()),
                $e
            );
        }

        $this->renderResult($io, $output, $format, $result, false);

        if ($input->getOption('no-cache-clear')) {
            return Command::SUCCESS;
        }

        return $this->clearContentCache($io) ? 0 : self::EXIT_MANUAL_ACTION_REQUIRED;
    }
}
