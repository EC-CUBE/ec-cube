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
use Eccube\Service\Content\UserDataFileService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * put の逆操作. 既定ではファイルの内容をそのまま標準出力へ書き出す.
 */
#[AsCommand(name: 'eccube:user-data:show', description: 'html/user_data 配下のファイルを出力します.')]
final class UserDataShowCommand extends Command
{
    use ContentCommandTrait;

    public function __construct(private readonly UserDataFileService $userDataFileService)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addOption('path', null, InputOption::VALUE_REQUIRED, '対象ファイル (html/user_data からの相対パス)');
        $this->addFormatOption();
        $this->setHelp(<<<'EOF'
            <info>%command.name%</info> は put の逆操作です.

              <info>php %command.full_name% --path=assets/css/customize.css > customize.css</info>

            --format=json では, UTF-8 として解釈できない内容 (画像等) は body ではなく
            body_base64 に base64 で格納します.
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

        $path = $input->getOption('path');
        if (null === $path || '' === $path) {
            $io->error('--path を指定してください.');

            return Command::INVALID;
        }

        try {
            $body = $this->userDataFileService->read((string) $path);
        } catch (ContentValidationException $e) {
            $io->error($e->getErrors());

            return 1;
        }

        if ('json' === $format) {
            $resolved = $this->userDataFileService->resolve((string) $path);
            $payload = [
                'path' => $this->userDataFileService->toRelative($resolved),
                'size' => strlen($body),
            ];
            // json_encode は不正な UTF-8 を扱えないため, テキスト以外は base64 で返す
            $payload += mb_check_encoding($body, 'UTF-8')
                ? ['body' => $body]
                : ['body_base64' => base64_encode($body)];

            $output->writeln((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return 0;
        }

        $output->write($body, false, OutputInterface::OUTPUT_RAW);

        return 0;
    }
}
