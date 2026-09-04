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
#[AsCommand(name: 'eccube:page:show', description: 'ページのテンプレートを出力します.')]
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
            ->addOption('url', null, InputOption::VALUE_REQUIRED, '対象ページの URL')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, '対象ページの ID');
        $this->addFormatOption();
        $this->setHelp(<<<'EOF'
            <info>%command.name%</info> は apply の逆操作です.

              <info>php %command.full_name% --url=guide > guide.twig</info>
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

        $url = $input->getOption('url');
        $id = $input->getOption('id');
        if (null === $url && null === $id) {
            $io->error('--url または --id を指定してください.');

            return Command::INVALID;
        }

        $Page = null === $id
            ? $this->pageContentService->findByUrl((string) $url)
            : $this->pageRepository->find((int) $id);

        if (!$Page instanceof Page) {
            $io->error(sprintf('ページが見つかりません: %s', (string) ($url ?? $id)));

            return 1;
        }

        $body = $this->pageContentService->readTemplate($Page);

        if ('json' === $format) {
            $output->writeln((string) json_encode([
                'id' => $Page->getId(),
                'url' => (string) $Page->getUrl(),
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

            return 0;
        }

        $output->write($body);

        return 0;
    }
}
