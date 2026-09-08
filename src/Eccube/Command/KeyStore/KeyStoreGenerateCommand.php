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

namespace Eccube\Command\KeyStore;

use Eccube\Service\AgentCommerce\Security\KeyPurposeInterface;
use Eccube\Service\AgentCommerce\Security\KeyPurposeRegistry;
use Eccube\Service\AgentCommerce\Security\KeyStoreEntry;
use Eccube\Service\AgentCommerce\Security\KeyStoreInspector;
use Eccube\Service\AgentCommerce\Security\KeyStoreInterface;
use Eccube\Service\AgentCommerce\Security\WebReadability;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 鍵を生成してキーストアへ保管する.
 *
 * 権限を分離した構成では app/keystore は CLI ユーザーの所有 (レーン S) となり,
 * Web サーバーからは書き込めない. 実行時の自動生成が働かないため, 本コマンドで事前に配置する.
 *
 * 冪等. 既にある鍵は上書きしない (--force を指定したときだけ差し替える).
 */
#[AsCommand(name: 'eccube:keystore:generate', description: '鍵を生成してキーストアへ保管します.')]
final class KeyStoreGenerateCommand extends Command
{
    use KeyStoreCommandTrait;

    public function __construct(
        private readonly KeyStoreInterface $keyStore,
        private readonly KeyStoreInspector $keyStoreInspector,
        private readonly KeyPurposeRegistry $keyPurposeRegistry,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('purposes', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, '生成する鍵の用途 (省略時は未生成のものをすべて)')
            ->addOption('force', null, InputOption::VALUE_NONE, '既存の鍵を差し替える')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, '対象を表示するだけで生成しない');
        $this->addFormatOption();
        $this->setHelp(<<<'EOF'
            <info>%command.name%</info> は鍵を生成してキーストアへ保管します.

              <info>php %command.full_name%</info>
              <info>php %command.full_name% ucp_signing --dry-run</info>
              <info>php %command.full_name% ucp_signing --force</info>

            既にある鍵は上書きしません. --force を指定したときだけ差し替えますが,
            署名鍵を差し替えると広告済みの公開鍵 (kid) が変わり, 旧鍵で署名した
            メッセージの検証は失敗します.

            生成後に Web サーバーから鍵を読み取れるかを判定します. 署名は
            リクエスト処理中に行われるため, 読み取れない状態はエラーとして扱います.
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

        try {
            /** @var list<string> $names */
            $names = (array) $input->getArgument('purposes');
            $targets = $this->resolveTargets($names);
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        }

        if ($targets === []) {
            $io->warning('鍵の用途が 1 つも登録されていません.');

            return Command::SUCCESS;
        }

        $force = (bool) $input->getOption('force');
        $dryRun = (bool) $input->getOption('dry-run');

        /** @var list<array{status: string, entry: KeyStoreEntry, error: string|null}> $results */
        $results = [];
        foreach ($targets as $purpose) {
            $results[] = $this->apply($purpose, $force, $dryRun);
        }

        return $this->report($io, $output, $format, $results, $dryRun);
    }

    /**
     * @return array{status: string, entry: KeyStoreEntry, error: string|null}
     */
    private function apply(KeyPurposeInterface $purpose, bool $force, bool $dryRun): array
    {
        $before = $this->keyStoreInspector->inspect($purpose);

        // 読み取れないだけの鍵を未生成とみなして上書きしないよう, 有無だけで判断する.
        if ($before->exists && !$force) {
            return ['status' => 'unchanged', 'entry' => $before, 'error' => null];
        }

        if ($dryRun) {
            return ['status' => $before->exists ? 'would_replace' : 'would_create', 'entry' => $before, 'error' => null];
        }

        try {
            $this->keyStore->write($purpose->getPurpose(), $purpose->generate());
        } catch (\Throwable $e) {
            return ['status' => 'failed', 'entry' => $before, 'error' => $e->getMessage()];
        }

        return [
            'status' => $before->exists ? 'replaced' : 'created',
            'entry' => $this->keyStoreInspector->inspect($purpose),
            'error' => null,
        ];
    }

    /**
     * @param list<string> $names
     *
     * @return list<KeyPurposeInterface>
     *
     * @throws \InvalidArgumentException 未登録の purpose を指定した場合
     */
    private function resolveTargets(array $names): array
    {
        if ($names === []) {
            return array_values($this->keyPurposeRegistry->all());
        }

        return array_map(
            fn (string $name): KeyPurposeInterface => $this->keyPurposeRegistry->get($name),
            $names
        );
    }

    /**
     * @param list<array{status: string, entry: KeyStoreEntry, error: string|null}> $results
     */
    private function report(SymfonyStyle $io, OutputInterface $output, string $format, array $results, bool $dryRun): int
    {
        $written = array_filter($results, static fn (array $r): bool => in_array($r['status'], ['created', 'replaced'], true));
        $failed = array_filter($results, static fn (array $r): bool => $r['status'] === 'failed');
        // 生成したものだけでなく, 既にあった鍵も対象にする. 差し替えなかった鍵が読めないまま
        // 終了コード 0 を返すと, 「実行したのにサイトが 500 のまま」を見逃すため.
        $unreadable = array_filter(
            $results,
            static fn (array $r): bool => in_array($r['status'], ['created', 'replaced', 'unchanged'], true)
                && $r['entry']->webReadability === WebReadability::Unreadable
        );

        if ($format === 'json') {
            $this->renderJson($output, array_map(
                static fn (array $r): array => ['status' => $r['status'], 'write_error' => $r['error']] + $r['entry']->toArray(),
                $results
            ));

            return $failed === [] && $unreadable === [] ? Command::SUCCESS : Command::FAILURE;
        }

        $rows = [];
        foreach ($results as $result) {
            $rows[] = [
                $result['entry']->purpose,
                $result['status'],
                $this->webReadabilityLabel($result['entry']->webReadability),
                $result['entry']->path ?? '-',
            ];
        }
        $io->table(['用途', '結果', 'Web サーバー', '保管先'], $rows);

        $this->renderHints($io, array_map(static fn (array $r): KeyStoreEntry => $r['entry'], $results));

        foreach ($failed as $result) {
            $io->error([
                sprintf('%s の鍵を保管できませんでした.', $result['entry']->purpose),
                (string) $result['error'],
                '保管先 (レーン S) を所有するユーザーで実行してください.'
                .' 期待値と実際の所有者は bin/console eccube:doctor:permissions で確認できます.',
            ]);
        }

        if ($unreadable !== []) {
            $io->error([
                '鍵を Web サーバーから読み取れません. このままでは署名と '
                .'/.well-known/ucp の応答が失敗します.',
                '上記の対処を実施するか, ECCUBE_KEYSTORE_STRICT_PERMISSIONS を解除して'
                .' bin/console eccube:keystore:generate --force を再実行してください.',
            ]);
        }

        if ($failed !== [] || $unreadable !== []) {
            return Command::FAILURE;
        }

        if (array_filter($results, static fn (array $r): bool => $r['status'] === 'replaced') !== []) {
            $io->warning([
                '既存の鍵を差し替えました.',
                '広告済みの公開鍵 (kid) が変わるため, 旧鍵で署名したメッセージの検証は失敗します.',
            ]);
        }

        if ($dryRun) {
            $io->note('dry-run のため生成していません.');

            return Command::SUCCESS;
        }

        $io->success(sprintf('生成: %d 件 / 変更なし: %d 件', count($written), count($results) - count($written)));

        return Command::SUCCESS;
    }
}
