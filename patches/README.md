# Composer パッチ

`cweagans/composer-patches` が `composer install` / `update` 時に適用します。内容は `composer.json` の `extra.patches` を参照してください。

## `codeception-gherkin-i18n-path.patch`

- **対象**: `codeception/codeception` の `Gherkin.php`（Feature ファイル用ローダ）
- **理由**: 新しい `behat/gherkin` のディレクトリ構成では、Codeception 4 が参照する `i18n.php` のパスがずれ、Codeception 起動時に失敗することがあるため。
- **方針**: 従来パスを試し、無ければパッケージルートの `i18n.php` を読む。PHP 7.4 でも動く分岐のみ。
