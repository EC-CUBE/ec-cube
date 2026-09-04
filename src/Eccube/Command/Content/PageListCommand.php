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

use Eccube\Entity\Page;
use Eccube\Repository\PageRepository;
use Eccube\Service\Content\PageContentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'eccube:page:list', description: 'ページの一覧を表示します.')]
final class PageListCommand extends Command
{
    use ContentCommandTrait;

    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageContentService $pageContentService,
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
        /** @var Page $Page */
        foreach ($this->pageRepository->getPageList() as $Page) {
            $rows[] = [
                'id' => $Page->getId(),
                'url' => (string) $Page->getUrl(),
                'name' => (string) $Page->getName(),
                'file_name' => (string) $Page->getFileName(),
                'editable' => $this->pageContentService->isUserDataPage($Page),
                'path' => $this->pageContentService->getFilePath($Page),
            ];
        }

        if ('json' === $format) {
            $output->writeln((string) json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return 0;
        }

        $io->table(
            ['ID', 'URL', 'ページ名', 'ファイル名', '編集可'],
            array_map(static fn (array $row): array => [
                $row['id'],
                $row['url'],
                $row['name'],
                $row['file_name'],
                $row['editable'] ? 'yes' : 'no',
            ], $rows)
        );

        return 0;
    }
}
