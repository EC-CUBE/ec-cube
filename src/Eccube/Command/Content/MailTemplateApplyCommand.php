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
use Eccube\Service\Content\ContentStatus;
use Eccube\Service\Content\MailTemplateContentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * メールテンプレート (dtb_mail_template + twig) を登録・更新する.
 *
 * ファイル名は新規登録時のみ指定できる (管理画面と同じ制約).
 */
#[AsCommand(name: 'eccube:mail-template:apply', description: 'メールテンプレートを登録・更新します.')]
final class MailTemplateApplyCommand extends Command
{
    use ContentCommandTrait;

    public function __construct(private readonly MailTemplateContentService $mailTemplateContentService)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('file-name', null, InputOption::VALUE_REQUIRED, '対象テンプレートのファイル名 (登録・更新の鍵)')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, '対象テンプレートの ID')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'テンプレート名')
            ->addOption('subject', null, InputOption::VALUE_REQUIRED, 'メールの件名')
            ->addOption('html-body', null, InputOption::VALUE_REQUIRED, 'HTML パートの本文. "-" を指定すると標準入力から読み込む')
            ->addOption('html-body-file', null, InputOption::VALUE_REQUIRED, 'HTML パートの本文を読み込むファイルのパス')
            ->addOption('remove-html', null, InputOption::VALUE_NONE, 'HTML パートを削除する');
        $this->addWriteOptions();
        $this->setHelp(<<<'EOF'
            <info>%command.name%</info> は dtb_mail_template とテンプレートファイルを対で登録・更新します.

              <info>cat order.twig | php %command.full_name% --file-name=order --body=-</info>

            HTML パートは --html-body / --html-body-file で指定し, --remove-html で削除します.
            いずれも指定しない場合は現在の内容を維持します.
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

        $fileName = $input->getOption('file-name');
        $id = $input->getOption('id');
        if (null === $fileName && null === $id) {
            $io->error('--file-name または --id を指定してください.');

            return Command::INVALID;
        }

        $removeHtml = (bool) $input->getOption('remove-html');

        try {
            $body = $this->readBody($input);
            $htmlBody = $this->readBody($input, 'html-body', 'html-body-file');
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        }

        if ($removeHtml && null !== $htmlBody) {
            $io->error('--remove-html と --html-body / --html-body-file は同時に指定できません.');

            return Command::INVALID;
        }

        $payload = [];
        if (null !== $fileName) {
            $payload['file_name'] = (string) $fileName;
        }
        if (null !== $id) {
            $payload['id'] = (int) $id;
        }
        if (null !== $body) {
            $payload['body'] = $body;
        }
        if (null !== $htmlBody) {
            $payload['html_body'] = $htmlBody;
        }
        if ($removeHtml) {
            $payload['remove_html'] = true;
        }
        foreach (['name', 'subject'] as $option) {
            $value = $input->getOption($option);
            if (null !== $value) {
                $payload[$option] = (string) $value;
            }
        }

        $dryRun = (bool) $input->getOption('dry-run');

        try {
            $result = $this->mailTemplateContentService->apply($payload, $dryRun);
        } catch (ContentValidationException $e) {
            $io->error(array_merge([sprintf('メールテンプレートを保存できません: %s', (string) ($fileName ?? $id))], $e->getErrors()));

            return 1;
        }

        $this->renderResult($io, $output, $format, $result, $dryRun);

        if ($dryRun || ContentStatus::Unchanged === $result->status || $input->getOption('no-cache-clear')) {
            return 0;
        }

        return $this->clearContentCache($io) ? 0 : self::EXIT_MANUAL_ACTION_REQUIRED;
    }
}
