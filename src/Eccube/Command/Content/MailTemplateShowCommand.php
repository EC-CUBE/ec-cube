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
use Eccube\Repository\MailTemplateRepository;
use Eccube\Service\Content\MailTemplateContentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'eccube:mail-template:show', description: 'メールテンプレートを出力します.')]
final class MailTemplateShowCommand extends Command
{
    use ContentCommandTrait;

    public function __construct(
        private readonly MailTemplateRepository $mailTemplateRepository,
        private readonly MailTemplateContentService $mailTemplateContentService,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('file-name', null, InputOption::VALUE_REQUIRED, '対象テンプレートのファイル名 (例: order または Mail/order.twig)')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, '対象テンプレートの ID')
            ->addOption('html', null, InputOption::VALUE_NONE, 'HTML パートを出力する');
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
        if (null === $fileName && null === $id) {
            $io->error('--file-name または --id を指定してください.');

            return Command::INVALID;
        }

        $Mail = null === $id
            ? $this->mailTemplateContentService->findByFileName((string) $fileName)
            : $this->mailTemplateRepository->find((int) $id);

        if (!$Mail instanceof MailTemplate) {
            $io->error(sprintf('メールテンプレートが見つかりません: %s', (string) ($fileName ?? $id)));

            return 1;
        }

        $body = $this->mailTemplateContentService->readTemplate($Mail);
        $htmlBody = $this->mailTemplateContentService->readHtmlTemplate($Mail);

        if ('json' === $format) {
            $output->writeln((string) json_encode([
                'id' => $Mail->getId(),
                'name' => (string) $Mail->getName(),
                'file_name' => (string) $Mail->getFileName(),
                'mail_subject' => (string) $Mail->getMailSubject(),
                'deletable' => $Mail->isDeletable(),
                'path' => $this->mailTemplateContentService->getFilePath($Mail),
                'html_path' => $this->mailTemplateContentService->getHtmlFilePath($Mail),
                'body' => $body,
                'html_body' => $htmlBody,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return 0;
        }

        if ($input->getOption('html')) {
            if (null === $htmlBody) {
                $io->error(sprintf('HTML パートがありません: %s', (string) $Mail->getFileName()));

                return 1;
            }

            $output->write($htmlBody);

            return 0;
        }

        $output->write($body);

        return 0;
    }
}
