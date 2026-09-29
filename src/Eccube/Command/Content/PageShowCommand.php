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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * apply の逆操作. 既定ではテンプレートの本文だけを標準出力へ書き出す.
 */
#[AsCommand(name: 'eccube:page:show', description: 'ページのテンプレートを出力します.', help: <<<'TXT'
<info>%command.name%</info> は apply の逆操作です.

  <info>php %command.full_name% --route=guide > guide.twig</info>
TXT)]
final class PageShowCommand extends Command
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
        $this
            ->addOption('route', null, InputOption::VALUE_REQUIRED, '対象ページのルーティング名 (例: product_list)')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, '対象ページの ID');
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

        $route = $input->getOption('route');
        $id = $input->getOption('id');
        if (null === $route && null === $id) {
            $io->error('--route または --id を指定してください.');

            return Command::INVALID;
        }

        $Page = null === $id
            ? $this->pageContentService->findByRoute((string) $route)
            : $this->pageRepository->find((int) $id);

        if (!$Page instanceof Page) {
            $io->error(sprintf('ページが見つかりません: %s', (string) ($route ?? $id)));

            return Command::FAILURE;
        }

        $body = $this->pageContentService->readTemplate($Page);

        if ('json' === $format) {
            $output->writeln((string) json_encode([
                'id' => $Page->getId(),
                'route' => (string) $Page->getUrl(),
                'name' => (string) $Page->getName(),
                'file_name' => (string) $Page->getFileName(),
                'author' => $Page->getAuthor(),
                'description' => $Page->getDescription(),
                'keyword' => $Page->getKeyword(),
                'meta_robots' => $Page->getMetaRobots(),
                'meta_tags' => $Page->getMetaTags(),
                'layouts' => array_map(static fn ($Layout): array => [
                    'id' => $Layout->getId(),
                    'name' => $Layout->getName(),
                    'device_type_id' => $Layout->getDeviceType()->getId(),
                ], $Page->getLayouts()),
                'path' => $this->pageContentService->getFilePath($Page),
                'body' => $body,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return Command::SUCCESS;
        }

        $output->write($body);

        return Command::SUCCESS;
    }
}
