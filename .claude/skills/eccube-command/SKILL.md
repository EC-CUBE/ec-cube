---
name: eccube-command
description: EC-CUBE 4.4 のコンソールコマンド（Symfony Console・#[AsCommand]）を実装するときの規約。「コマンドを作って」「バッチを実装して」「cronで動かす処理を作って」「コンソールコマンドを追加して」などと言われたとき、またはコンソールコマンドを作成・編集するとき（コア・プラグインのいずれでも）に使用する。
---

# Command 規約 — コンソールコマンド／バッチ（EC-CUBE 4.4）

**対象**: `src/Eccube/Command/**/*.php`, `app/Customize/Command/**/*.php`, `app/Plugin/*/Command/**/*.php`
**前提**: Symfony 7.4 / PHP 8.2+

> 目的: コンソールコマンド（バッチ・cron 用途含む）を「入出力と起動の薄い層」に保ち、
> 業務ロジックは Service／Repository へ寄せる。Skill `eccube-controller` / `eccube-service` と同じ責務分離をコマンドにも適用する。
> コマンドは「もう 1 つの入口」であって、ロジックの置き場所ではない。

## 基本ルール

- **`Symfony\Component\Console\Command\Command` を継承**し、クラスに **`#[AsCommand(name: ..., description: ...)]` 属性**を付ける。
  - 属性は `Symfony\Component\Console\Attribute\AsCommand`。
  - コマンド名は **`eccube:` を接頭辞**にしたコロン区切り（実例: `eccube:delete-carts` / `eccube:fixtures:generate` / `eccube:generate:proxies` / `eccube:plugin:enable`）。
- **手動登録は不要**。`app/config/eccube/services.yaml` の `_defaults` で `autoconfigure: true` が効いており、
  `Eccube\` / `Customize\` / `Plugin\` 配下のクラスは `#[AsCommand]` を付けるだけで `console.command` として自動登録される。
  サービス定義に手書きでタグを足さない。
- **依存はコンストラクタインジェクション**で受ける（`private readonly`／既存実装は `protected` も混在）。
  リポジトリ・サービス・`EntityManagerInterface`・`EccubeConfig` 等を注入する。
  - 注: コマンドの場合 `parent::__construct()` の呼び出しが必須（後述）。トレイト経由で依存を渡したいときだけ
    `#[Required]` セッター注入を使う（`PluginCommandTrait` が `setPluginService()` 等で採用）。
- **`configure()` で引数・オプションを宣言**する。`addArgument()` / `addOption()`、必要に応じて `setHelp()`。
- **`execute(InputInterface $input, OutputInterface $output): int` に処理を書き、`int` を返す**。
  正常終了は `0`、異常終了は非 0（`1` 等）。
  `Command::SUCCESS` / `Command::FAILURE` 定数も使えるが、**EC-CUBE コアは一貫して `return 0;` のリテラルを使っている**ので踏襲する。
- **出力は `SymfonyStyle`** を使う（`$io->success()` / `$io->error()` / `$io->comment()` / `$io->title()` 等）。
  低レベルに `$output->writeln()` を使う実装もあるが、ユーザ向けメッセージは `SymfonyStyle` に寄せる。
- **業務ロジックはコマンドに直書きしない**。Service／Repository／PurchaseFlow へ委譲し、コマンドは
  「引数の取得 → 委譲 → 結果の出力 → 終了コード」に徹する（Skill `eccube-service` 参照）。

## 実装パターン

### 基本形（引数＋DI＋委譲）

`DeleteCartsCommand` を基にした骨格。コンストラクタで依存を受け、`configure()` で引数を宣言し、
`execute()` は委譲と出力に徹する。

```php
<?php

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

namespace Eccube\Command;

use Eccube\Repository\ExampleRepository;
use Eccube\Service\ExampleService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'eccube:example:run', description: 'Run the example batch')]
class ExampleRunCommand extends Command
{
    public function __construct(
        private readonly ExampleService $exampleService,
        private readonly ExampleRepository $exampleRepository,
    ) {
        parent::__construct();   // ← コマンドでは必須
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('date', InputArgument::REQUIRED, 'Process records before the specified date');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $date = $input->getArgument('date');

        // 業務処理は Service へ委譲する（コマンドにロジックを書かない）
        $count = $this->exampleService->purgeBefore(new \DateTime($date));

        $io->success(sprintf('Purged %d records.', $count));

        return 0;
    }
}
```

### オプション（`addOption`）とデフォルト値

`GenerateDummyDataCommand` の実例。`InputOption::VALUE_REQUIRED`（値あり・デフォルト指定可）と
`InputOption::VALUE_NONE`（フラグ）を使い分ける。

```php
protected function configure(): void
{
    $this
        ->addOption('with-locale', null, InputOption::VALUE_REQUIRED, 'Set to the locale.', 'ja_JP')
        ->addOption('without-image', null, InputOption::VALUE_NONE, 'Do not generate images.')
        ->addOption('products', null, InputOption::VALUE_REQUIRED, 'Number of Products.', 100);
}

protected function execute(InputInterface $input, OutputInterface $output): int
{
    $locale = $input->getOption('with-locale');
    $notImage = $input->getOption('without-image');
    // ...
    return 0;
}
```

### バッチ（大量データ）でのトランザクションと flush

大量レコードを扱うバッチは、**一定件数ごとにまとめて `flush()`** する（ループ内で毎回 `flush()` しない）。
`GenerateDummyDataCommand` は `$batchSize = 100` でまとめて flush している。
明示的なトランザクション境界が必要なら `DeleteCartsCommand` のように `beginTransaction()`／`commit()`／`rollback()` で囲む。

```php
$batchSize = 100;
foreach ($records as $i => $record) {
    $this->exampleService->process($record);
    if ((($i + 1) % $batchSize) === 0) {
        $this->entityManager->flush();
    }
}
$this->entityManager->flush();   // 端数を flush
```

```php
// 明示トランザクション（DeleteCartsCommand の定石）: 失敗時は rollback して非 0 を返す
try {
    $this->entityManager->beginTransaction();
    // ... 処理 ...
    $this->entityManager->flush();
    $this->entityManager->commit();
} catch (\Exception) {
    $io->error('Failed. Rollbacked.');
    $this->entityManager->rollback();

    return 1;
}
```

### プラグイン／Customize のコマンド

`#[AsCommand]` を付けて `app/Plugin/{Code}/Command/` または `app/Customize/Command/` に置くだけで、
`Plugin\` / `Customize\` 名前空間も `autoconfigure: true` の対象なので自動登録される。
共通処理をトレイトに切り出すなら `PluginCommandTrait` のように `#[Required]` セッター注入で依存を受ける。

## cron / 定期実行

- **EC-CUBE 4.4 のコアには独自のスケジューラ／cron 機構は無い**（`composer.json` に `symfony/scheduler` も含まれない。
  `#[AsCronTask]` 等の属性も未使用）。推測でスケジューラ機能を持ち出さない。
- **定期実行は OS の cron（または systemd timer 等）から `bin/console <コマンド名>` を叩く**のが事実上の手段。
  そのため、cron 用途のコマンドは「副作用が冪等／安全に再実行できる」「引数で対象範囲を絞れる」設計にしておく。

## よくある間違い

整形・型・属性変換（`vendor/bin/rector` / `phpstan` / `php-cs-fixer`）が扱える範囲はここに挙げない。
**ツールでは判断できない**観点だけ:

- ❌ `execute()` に業務的な計算・判定・複数 Repository 横断処理を直書き → ✅ Service／Repository へ委譲し、コマンドは入出力と終了コードに徹する
- ❌ コンストラクタで `parent::__construct()` を呼び忘れる → ✅ コマンドでは必須（呼ばないと実行時エラー）
- ❌ サービス定義に手書きで `console.command` タグを足す → ✅ `#[AsCommand]` ＋ `autoconfigure` 任せ（手動登録不要）
- ❌ `execute()` の戻り値を書かない／`void` にする → ✅ `int` を返す（正常 `0`、異常は非 0）
- ❌ ループ内で毎回 `flush()` してバッチが遅い → ✅ バッチサイズごとにまとめて `flush()`、端数も最後に flush
- ❌ 「Symfony Scheduler で定期実行」と推測で書く → ✅ コアに機構は無い。OS の cron から `bin/console` を叩く前提で冪等に作る
- ❌ コマンド名を独自の命名で付ける → ✅ `eccube:` 接頭辞のコロン区切り（既存コマンドに倣う）

## 実行・確認方法

QA ツール（PHPUnit / PHPStan / PHP-CS-Fixer / Rector）の実行手順は **AGENTS.md「開発コマンド」**を参照。
コマンド固有の確認は以下:

```bash
bin/console list              # 登録済みコマンド一覧（自分のコマンドが出るか）
bin/console list eccube       # eccube: 名前空間のコマンド一覧
bin/console help <コマンド名>  # 引数・オプションの確認
bin/console <コマンド名> --dry-run 等   # 副作用のあるバッチは小さい入力で試す
```

新規コマンドが `bin/console list` に現れれば autoconfigure による登録は成功している。

---

実装・改修後は、Skill `eccube-review-responsibility` で責務分離を点検すること。
