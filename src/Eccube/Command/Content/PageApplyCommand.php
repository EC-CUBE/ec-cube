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
use Eccube\Service\Content\ContentStatus;
use Eccube\Service\Content\PageContentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * ページ (dtb_page + twig) をルーティング名を鍵に登録・更新する.
 *
 * 指定しなかった項目は既存の値を維持するため, 同じ入力を複数回適用しても結果は変わらない.
 */
#[AsCommand(name: 'eccube:page:apply', description: 'ページを登録・更新します.', help: <<<'TXT'
<info>%command.name%</info> は dtb_page とテンプレートファイルを対で登録・更新します.

  <info>cat guide.twig | php %command.full_name% --route=guide --name=ご利用ガイド --body=-</info>
  <info>php %command.full_name% --route=guide --body-file=guide.twig --dry-run</info>

--route は dtb_page.url に保存されるルート名です (管理画面のページ管理の「ルーティング名」列).
既定ページ (EDIT_TYPE_DEFAULT 以上) はルーティング名とファイル名を変更できません.
TXT)]
final class PageApplyCommand extends Command
{
    use ContentCommandTrait;

    /**
     * オプション名 => payload のキー.
     *
     * @var array<string, string>
     */
    private const FIELD_OPTIONS = [
        'name' => 'name',
        'file-name' => 'file_name',
        'author' => 'author',
        'description' => 'description',
        'keyword' => 'keyword',
        'meta-robots' => 'meta_robots',
        'meta-tags' => 'meta_tags',
        'pc-layout' => 'pc_layout',
        'sp-layout' => 'sp_layout',
    ];

    public function __construct(private readonly PageContentService $pageContentService)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('route', null, InputOption::VALUE_REQUIRED, '対象ページのルーティング名 (登録・更新の鍵. 例: product_list)')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'ページ名')
            ->addOption('file-name', null, InputOption::VALUE_REQUIRED, 'テンプレートのファイル名 (新規登録時の既定はルーティング名と同じ)')
            ->addOption('author', null, InputOption::VALUE_REQUIRED, 'author')
            ->addOption('description', null, InputOption::VALUE_REQUIRED, 'description')
            ->addOption('keyword', null, InputOption::VALUE_REQUIRED, 'keyword')
            ->addOption('meta-robots', null, InputOption::VALUE_REQUIRED, 'meta robots')
            ->addOption('meta-tags', null, InputOption::VALUE_REQUIRED, 'meta tags')
            ->addOption('pc-layout', null, InputOption::VALUE_REQUIRED, 'PC レイアウトの ID (空文字で解除)')
            ->addOption('sp-layout', null, InputOption::VALUE_REQUIRED, 'SP レイアウトの ID (空文字で解除)');
        $this->addWriteOptions();
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
        if (null === $route || '' === $route) {
            $io->error('--route を指定してください.');

            return Command::INVALID;
        }

        try {
            $body = $this->readBody($input);
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        }

        $payload = ['route' => (string) $route];
        if (null !== $body) {
            $payload['body'] = $body;
        }
        foreach (self::FIELD_OPTIONS as $option => $key) {
            $value = $input->getOption($option);
            if (null !== $value) {
                $payload[$key] = (string) $value;
            }
        }

        $dryRun = (bool) $input->getOption('dry-run');

        try {
            /** @var array{route: string} $payload */
            $result = $this->pageContentService->apply($payload, $dryRun);
        } catch (ContentValidationException $e) {
            $io->error(array_merge([sprintf('ページを保存できません: %s', (string) $route)], $e->getErrors()));

            return Command::FAILURE;
        } catch (ContentWriteException $e) {
            return $this->reportWriteFailure($io, sprintf('ページを保存できません: %s', (string) $route), $e);
        }

        $this->renderResult($io, $output, $format, $result, $dryRun);

        if ($dryRun || ContentStatus::Unchanged === $result->status || $input->getOption('no-cache-clear')) {
            return Command::SUCCESS;
        }

        return $this->clearContentCache($io) ? 0 : self::EXIT_MANUAL_ACTION_REQUIRED;
    }
}
