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
use Eccube\Service\Content\PageContentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * ページ (dtb_page + twig) を削除する.
 *
 * 管理画面と同じく, ユーザーが作成したページ (EDIT_TYPE_USER) のみ削除できる.
 */
#[AsCommand(name: 'eccube:page:remove', description: 'ページを削除します.')]
final class PageRemoveCommand extends Command
{
    use ContentCommandTrait;

    public function __construct(private readonly PageContentService $pageContentService)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('url', null, InputOption::VALUE_REQUIRED, '対象ページの URL')
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

        $url = $input->getOption('url');
        if (null === $url || '' === $url) {
            $io->error('--url を指定してください.');

            return Command::INVALID;
        }

        $Page = $this->pageContentService->findByUrl((string) $url);
        if (!$Page instanceof Page) {
            $io->error(sprintf('ページが見つかりません: %s', (string) $url));

            return 1;
        }

        if (Page::EDIT_TYPE_USER !== $Page->getEditType()) {
            $io->error(sprintf('既定ページのため削除できません: %s', (string) $url));

            return 1;
        }

        if (!$input->getOption('force') && !$io->confirm(sprintf('%s を削除しますか?', (string) $url), false)) {
            $io->text('中止しました.');

            return 1;
        }

        $result = $this->pageContentService->remove($Page);

        $this->renderResult($io, $output, $format, $result, false);

        if ($input->getOption('no-cache-clear')) {
            return 0;
        }

        return $this->clearContentCache($io) ? 0 : self::EXIT_MANUAL_ACTION_REQUIRED;
    }
}
