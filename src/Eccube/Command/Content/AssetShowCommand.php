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
use Eccube\Service\Content\AssetContentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * apply の逆操作. 既定では customize.css / customize.js の内容を標準出力へ書き出す.
 */
#[AsCommand(name: 'eccube:asset:show', description: 'カスタマイズ用の CSS / JS を出力します.')]
final class AssetShowCommand extends Command
{
    use ContentCommandTrait;

    public function __construct(private readonly AssetContentService $assetContentService)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addOption('type', null, InputOption::VALUE_REQUIRED, sprintf('対象の種別 (%s)', implode('|', $this->assetContentService->getTypes())));
        $this->addFormatOption();
        $this->setHelp(<<<'EOF'
            <info>%command.name%</info> は apply の逆操作です.

              <info>php %command.full_name% --type=css > customize.css</info>
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

        $type = (string) $input->getOption('type');
        if (!$this->assetContentService->isValidType($type)) {
            $io->error(sprintf('--type は %s のいずれかで指定してください.', implode(' / ', $this->assetContentService->getTypes())));

            return Command::INVALID;
        }

        try {
            $body = $this->assetContentService->read($type);
        } catch (ContentValidationException $e) {
            $io->error($e->getErrors());

            return 1;
        }

        if ('json' === $format) {
            $output->writeln((string) json_encode([
                'type' => $type,
                'path' => $this->assetContentService->getFilePath($type),
                'body' => $body,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return 0;
        }

        $output->write($body);

        return 0;
    }
}
