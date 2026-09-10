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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'eccube:mail-template:list', description: 'メールテンプレートの一覧を表示します.')]
final class MailTemplateListCommand extends Command
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

        $rows = [];
        /** @var MailTemplate $Mail */
        foreach ($this->mailTemplateRepository->findBy([], ['id' => 'ASC']) as $Mail) {
            $rows[] = [
                'id' => $Mail->getId(),
                'name' => (string) $Mail->getName(),
                'file_name' => (string) $Mail->getFileName(),
                'mail_subject' => (string) $Mail->getMailSubject(),
                'path' => $this->mailTemplateContentService->getFilePath($Mail),
            ];
        }

        if ('json' === $format) {
            $output->writeln((string) json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return 0;
        }

        $io->table(
            ['ID', 'テンプレート名', 'ファイル名', '件名'],
            array_map(static fn (array $row): array => [
                $row['id'],
                $row['name'],
                $row['file_name'],
                $row['mail_subject'],
            ], $rows)
        );

        return 0;
    }
}
