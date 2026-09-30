---
name: eccube-command
description: EC-CUBE 4.4 のコンソールコマンド（Symfony Console・#[AsCommand]）を実装するときの規約。「コマンドを作って」「バッチを実装して」「cronで動かす処理を作って」「コンソールコマンドを追加して」などと言われたとき、または src/Eccube/Command・プラグインの Command 配下を作成・編集するときに使用する。
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
- **`execute(InputInterface $input, OutputInterface $output): int` に処理を書き、`int` を返す**。終了コードの意味は固定:
  - `0` = 正常終了（`Command::SUCCESS`。既存コマンドはリテラル `0` も使う）
  - `1` = 失敗。本処理は完了していない（`Command::FAILURE`）
  - `2` = 引数・オプション不正（`Command::INVALID`。`--format` の値不正など）
  - **`3` = 本処理は完了したが手動操作が必要**（`EXIT_MANUAL_ACTION_REQUIRED`。`PluginCommandTrait` / `ContentCommandTrait` /
    `CacheBuildCommand` / `EnvSetCommand` が定義）。キャッシュを削除できなかった・ビルドの再生成が別途要る・`.env.local.php` があって
    変更が反映されない、など。**必要な操作（`bin/console eccube:cache:build` 等）を `$io->warning()` で必ず添える。**
    `2` は `Command::INVALID` が使用済みのため避ける
  - Symfony 標準の `cache:clear` 等に非ゼロを返させない。symfony/flex の auto-scripts が `composer install` を中断する
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

### 書き込み系コマンド（`apply` / `--dry-run` / `--format=json` / 標準入力）

ファイルや設定を書き換えるコマンドは `src/Eccube/Command/Content/*` の形に揃える（`ContentCommandTrait` を `use`）。
CI・エージェントから扱えるよう、入出力と終了コードを機械可読にするのが目的。

- **サブコマンドは `list` / `show` / `apply` / `remove`**（静的ファイルは `put`）。`new` / `edit` に分けず、**`apply` は upsert で冪等**にする
  （指定しなかった項目は既存値を維持し、同じ入力を何度適用しても結果が同じ）。`show` は `apply` の逆操作
  （`show --route=guide > guide.twig` → `apply --route=guide --body-file=guide.twig`）。
- **`addWriteOptions()`** が `--body`（`-` で標準入力）/ `--body-file` / `--dry-run` / `--no-cache-clear` / `--format=table|json` を足す。
  本文は `readBody()` で取る（`--body` と `--body-file` の同時指定はエラー。読み込み失敗の `false` を空文字列へ丸めない）。
- **`--dry-run` は差分を表示して適用しない**。Service 側が `$dryRun` を受け取り、`ContentResult` に変更前後を載せる。
- **`--format=json`** は `renderResult()` が `{"dry_run": bool, ...ContentResult}` を出力する。値不正は `Command::INVALID`。
- **書き込み失敗は `ContentWriteException`** を受けて `reportWriteFailure()` に渡す（`eccube:doctor:permissions` を案内して `1`）。
  権限を分離した構成では Web サーバーのユーザーで実行したときだけ失敗するため、実行ユーザーの誤りを最初に疑わせる。
- 反映にキャッシュの削除が要るものは `clearContentCache()` を最後に呼び、`false` なら `EXIT_MANUAL_ACTION_REQUIRED`（`3`）。
  削除できない理由と手順（`eccube:cache:build` / Web サーバーのユーザーで `cache:pool:clear`）はトレイトが案内する。
- 分離した構成では**レーン W（`var/runtime` / `var/log`）へ CLI から書かない**。詳細は Skill `eccube-permission-lanes`。

```php
#[AsCommand(name: 'eccube:example:apply', description: '...')]
final class ExampleApplyCommand extends Command
{
    use ContentCommandTrait;

    protected function configure(): void
    {
        $this->addOption('key', null, InputOption::VALUE_REQUIRED, '登録・更新の鍵');
        $this->addWriteOptions();   // --body / --body-file / --dry-run / --no-cache-clear / --format
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $format = (string) $input->getOption('format');
        if (!$this->isValidFormat($format)) {
            $this->invalidFormat($io);

            return Command::INVALID;
        }
        try {
            $body = $this->readBody($input);   // --body=- なら標準入力
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        }
        $dryRun = (bool) $input->getOption('dry-run');
        try {
            $result = $this->exampleContentService->apply([...], $dryRun);   // ContentResult
        } catch (ContentValidationException $e) {
            $io->error($e->getErrors());

            return Command::FAILURE;
        } catch (ContentWriteException $e) {
            return $this->reportWriteFailure($io, '保存できません', $e);
        }
        $this->renderResult($io, $output, $format, $result, $dryRun);
        if ($dryRun || ContentStatus::Unchanged === $result->status || $input->getOption('no-cache-clear')) {
            return Command::SUCCESS;
        }

        return $this->clearContentCache($io) ? Command::SUCCESS : self::EXIT_MANUAL_ACTION_REQUIRED;
    }
}
```

### 子プロセスで `bin/console` を実行する

`cache:clear` や `eccube:cache:build` を別プロセスで呼ぶときは **cwd に `kernel.project_dir` を渡し、`setTimeout(null)`** にする
（`PluginCommandTrait::clearCache()` / `EnvSetCommand`）。cwd を省略するとプロジェクトルート以外から実行したときに `bin/console` を
解決できず、`Process` の既定タイムアウト（60 秒）を超えると子プロセスが kill されてキャッシュが中途半端に消える。
実行中のプロセスは自身のコンパイル済みコンテナを作り直せない（子が新しいコンテナを作ると親のディレクトリが消える）ため、
コンテナの内容を変える操作の後は `eccube:cache:build` の実行を案内して `3` を返す。

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
- ❌ `execute()` の戻り値を書かない／`void` にする → ✅ `int` を返す（`0` 正常 / `1` 失敗 / `2` 引数不正 / `3` 完了したが手動操作が必要）
- ❌ キャッシュ削除に失敗しても `$io->error()` を出して `return 0` → ✅ `3`（`EXIT_MANUAL_ACTION_REQUIRED`）と必要な操作の案内を返す
- ❌ 書き込み系を `new` / `edit` に分ける、`--dry-run` / `--format=json` が無い → ✅ `apply`（upsert・冪等）＋ `ContentCommandTrait`
- ❌ 子プロセスの `bin/console` を cwd 無し・既定タイムアウトで実行 → ✅ `kernel.project_dir` を cwd に、`setTimeout(null)`
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
bin/console <コマンド名> --format=json | jq .   # 機械可読出力の確認
bin/console <コマンド名> ...; echo $?           # 終了コード (0 / 1 / 2 / 3) の確認
```

新規コマンドが `bin/console list` に現れれば autoconfigure による登録は成功している。

---

実装・改修後は、Skill `eccube-review-responsibility` で責務分離を点検すること。
