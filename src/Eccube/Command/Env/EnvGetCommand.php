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

namespace Eccube\Command\Env;

use Eccube\Service\EnvFileService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * .env の値を 1 件表示する.
 *
 * 一括ダンプは提供しない. .env には DATABASE_URL 等の資格情報が含まれるため,
 * 明示的に指定したキーだけを返す.
 */
#[AsCommand(name: 'eccube:env:get', description: '.env の値を表示します.')]
final class EnvGetCommand extends Command
{
    public function __construct(private readonly EnvFileService $envFileService)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('key', InputArgument::REQUIRED, '環境変数名 (例: ECCUBE_TEMPLATE_CODE)')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, '出力形式 (table|json)', 'table')
            ->setHelp(<<<'EOF'
                <info>%command.name%</info> は .env の値を表示します.

                  <info>php %command.full_name% ECCUBE_TEMPLATE_CODE</info>
                  <info>php %command.full_name% ECCUBE_FORCE_SSL --format=json</info>

                既定の出力は実行時に反映されている値のみを標準出力へ書き出すため,
                シェルの変数へそのまま代入できます. .env の値と実行時の値が食い違う場合
                (OS の環境変数などが上書きしている場合) は標準エラー出力へ警告します.
                EOF
            );
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $errorIo = $io->getErrorStyle();

        $format = (string) $input->getOption('format');
        if (!in_array($format, ['table', 'json'], true)) {
            $errorIo->error('--format は table / json のいずれかで指定してください.');

            return Command::INVALID;
        }

        $key = (string) $input->getArgument('key');

        $fileValue = $this->envFileService->get($key);
        $effectiveValue = $this->envFileService->getEffective($key);
        $overridden = [] !== $this->envFileService->getOverriddenKeys([$key]);
        $reasons = $this->envFileService->getIneffectiveReasons();

        if ('json' === $format) {
            $output->writeln((string) json_encode([
                'key' => $key,
                'file_value' => $fileValue,
                'effective_value' => $effectiveValue,
                'overridden' => $overridden,
                'ineffective_reasons' => array_values($reasons),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return 0;
        }

        if (null === $fileValue && null === $effectiveValue) {
            $errorIo->error(sprintf('%s は設定されていません.', $key));

            return 1;
        }

        if ($overridden) {
            $errorIo->warning(sprintf(
                '%s は .env ではなく OS の環境変数 (またはカスケードファイル) の値が使われています. .env の値: %s',
                $key,
                $fileValue ?? '(未設定)'
            ));
        }

        $output->writeln($effectiveValue ?? (string) $fileValue, OutputInterface::OUTPUT_RAW);

        return 0;
    }
}
