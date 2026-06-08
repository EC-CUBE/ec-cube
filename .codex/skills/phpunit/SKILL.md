---
name: phpunit
description: EC-CUBE 4.4 の PHPUnit テストを実装・修正するときの規約。「テストを書いて」「PHPUnitテストを追加して」「コントローラのテストを作って」「テストが落ちる原因を調べて直して」などと言われたとき、または tests/Eccube/Tests 配下のテストコードを作成・編集するときに使用する。
---

# EC-CUBE 4.4 PHPUnit テスト規約

EC-CUBE 4.4（Symfony 7.4 / PHP 8.2+ / PHPUnit 11）のテストを実装・修正する際は、
必ず次のドキュメントを読み込み、その規約に従うこと。

**規約本文: [`docs/rules/phpunit.md`](../../../docs/rules/phpunit.md)**

要点:
- Web テストは `Eccube\Tests\Web\AbstractWebTestCase`、それ以外は `Eccube\Tests\EccubeTestCase` を継承する。
- テストデータは `createCustomer()` / `createProduct()` / `createOrder()` 等のフィクスチャヘルパで生成する。
- データプロバイダは `#[DataProvider]` 属性を使う（`@dataProvider` アノテーションは使わない）。
- 実装後は `bin/phpunit` と `vendor/bin/phpstan analyse src`（level 6）を通す。
