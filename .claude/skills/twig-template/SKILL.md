---
name: twig-template
description: EC-CUBE 4.4 の Twig 拡張（Extension/Filter/Function）とテンプレートを実装・改修・点検するときの規約。「Twig拡張を作って」「フィルタ/関数を追加して」「テンプレートを上書きして」「このテンプレートを直して」「XSS/エスケープを確認して」「rawの使い方を点検して」などと言われたとき、または src/Eccube/Twig/Extension・app/template・Resource/template 配下を作成・編集するときに使用する。
---

# Twig 拡張・テンプレート規約（EC-CUBE 4.4）

**対象**: `src/Eccube/Twig/Extension/**/*.php`, `src/Eccube/Resource/template/**/*.twig`, `app/template/**/*.twig`
**前提**: Symfony 7.4 / Twig 3.x / PHP 8.2+

> 目的: オートエスケープを前提に **XSS を作り込まない**こと、テンプレートの**上書きパス・名前空間を正しく**選ぶこと。
> 直近でコアに XSS 修正が入っている領域なので、`|raw` と `is_safe` の扱いは特に慎重に。

## オートエスケープと XSS（最優先）

- Twig は **HTML オートエスケープがデフォルト有効**（`packages/twig.yaml` に明示設定はなく Twig 既定動作）。
  通常の `{{ value }}` は自動でエスケープされる。**わざわざ `|raw` を付けない限り安全**、が大原則。
- **`|raw` はエスケープを無効化する**。ユーザー入力・DB 由来の文字列に `|raw` を付けると XSS になる。
  `|raw` を書く前に「この値は本当に信頼できる HTML か？」を必ず自問する。
- **コンテキストに応じたエスケープ**を使う:
  - JavaScript の中に値を埋めるなら `{{ value|escape('js') }}`（`|e('js')`）。HTML エスケープでは JS 文脈の XSS を防げない。
  - 例: 管理画面 `Order/search_product.twig` は `{{ Product.id|escape('js') }}` と JS 文脈エスケープを使っている。
- PHP 側で HTML を返すフィルタ/関数は **`['is_safe' => ['html']]`** を付ける（付けないと二重エスケープされる）。
  **ただし `is_safe` を付ける＝そのフィルタの出力責任を開発者が負う**ということ。中で生成する HTML に
  外部入力を混ぜるなら `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')` で**自前エスケープしてから**返す。

## Twig 拡張の実装パターン

`src/Eccube/Twig/Extension/` に Twig 標準の `AbstractExtension`（`\Twig\Extension\AbstractExtension`）を継承して置く。`autoconfigure: true`（`services.yaml`）で
自動的に Twig 拡張として登録される（手動タグ不要）。代表例: `EccubeExtension` / `TaxExtension` / `CsrfExtension` / `IntlExtension`。

```php
class ExampleExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // HTML を返すフィルタは is_safe を明示。中の外部入力は自前でエスケープする
            new TwigFilter('file_ext_icon', $this->getExtensionIcon(...), ['is_safe' => ['html']]),
            new TwigFilter('price', $this->getPriceFilter(...)),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('product', $this->getProduct(...)),
            new TwigFunction('class_categories_as_json', $this->getClassCategoriesAsJson(...)),
        ];
    }
}
```

- 既存のフィルタ例: `price` / `date_format` / `ellipsis` / `no_image_product` / `file_ext_icon`。
- 既存の関数例: `has_errors()` / `active_menus()` / `product()` / `class_categories_as_json()` / `currency_symbol()`。
- Twig で使えるグローバル: `BaseInfo` / `eccube_config` / `Layout` / `Page` / `event_dispatcher`（`TwigInitializeListener` が注入）。

## テンプレートの配置と上書き

コアテンプレートは `src/Eccube/Resource/template/` にあり、`app/template/` に**同じ相対パス**で置くと上書きできる。
名前空間と探索優先順は `packages/twig.yaml` の `paths` で決まる。

| 用途 | コア（既定） | 上書き先 | 名前空間 |
|---|---|---|---|
| 店頭（フロント） | `src/Eccube/Resource/template/default/` | `app/template/{テーマ}/` | なし（既定） |
| 管理画面 | `src/Eccube/Resource/template/admin/` | `app/template/admin/` | `@admin` |
| ユーザーデータ | — | `app/template/user_data/` | `@user_data` |

- **管理画面テンプレートを参照するときは `@admin` 名前空間**を付ける（例: `{{ include('@admin/...') }}`）。名前空間を忘れると探索先を誤る。
- 上書きは `app/template/` 直下ではなく、**`admin/` か `default(テーマ)/` の正しいサブディレクトリ**に置く。
- **ユーザーが編集できるテンプレート文字列（CMS コンテンツ・フリーエリア・メール本文等）を描画するときは Twig サンドボックスを通す**。
  文字列テンプレートは `template_from_string(...)` ＋ `sandboxed = true` で描画する（コアの定石。例: `default_frame.twig` の CMS メタタグ）。
  許可するタグ/フィルタ/関数はコアの `SecurityPolicyDecorator`（`src/Eccube/Twig/Sandbox/`）で制御されており、**サンドボックスを外すとテンプレートインジェクションになる**（過去の脆弱性修正の中心領域）。

## テンプレートイベント（差し込み）

全テンプレートは描画時に **ファイル名をイベント名として** `TemplateEvent` が dispatch される
（`TemplateEventExtension` / `Twig/Template.php`）。プラグイン・カスタマイズはここに差し込む。

```php
public function onTemplateCart(TemplateEvent $event): void
{
    $event->addAsset('@MyPlugin/cart_script.twig');   // <head> 等へアセット追加
    $event->addSnippet('@MyPlugin/cart_footer.twig'); // 既定位置へスニペット挿入
    // $event->setSource(...) でテンプレート本体を置換も可能
}
```

詳細なイベントの購読方法は Skill `event-subscriber` を参照。**テンプレートイベントは見た目の調整に使い、業務ロジック（永続化等）を書かない**。

## よくある間違い（XSS・上書き — ツールでは検出しにくい観点）

- ❌ ユーザー入力・DB 値に `{{ value|raw }}` → ✅ `|raw` を外す。HTML が必要なら出力前にサニタイズ
- ❌ JS の中に `{{ value }}`（HTML エスケープのみ）→ ✅ `{{ value|escape('js') }}`
- ❌ `is_safe => ['html']` を付けた関数内で外部入力を未エスケープ連結 → ✅ `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`
- ❌ HTML を返すフィルタに `is_safe` を付け忘れ → ✅ 付ける（さもないと二重エスケープで `&lt;` 等が表示される）
- ❌ 上書きを `app/template/` 直下に置く / `@admin` 名前空間を付け忘れる → ✅ 正しいサブディレクトリ・名前空間に置く
- ❌ **管理画面テンプレートだから安全**と油断して `|raw` する → ✅ admin 配下も XSS シンク（過去の XSS 修正は管理画面テンプレートに多い）。DB/入力由来の値は admin でも必ずエスケープする
- ❌ テンプレートイベントにエンティティ永続化など業務処理を書く → ✅ 見た目調整のみ。業務は対応するコントローライベントへ

## 実行・確認方法

QA ツール（PHPUnit / PHPStan / PHP-CS-Fixer）の実行方法は AGENTS.md「開発コマンド」を参照。

```bash
bin/console lint:twig src/Eccube/Resource/template/   # Twig 構文チェック
bin/console cache:clear                                # テンプレート変更の反映（var/cache/{env} クリア）
```

- 上書きが効かない／変更が反映されない場合は、まず `cache:clear` と上書きパス・名前空間を疑う。
- `|raw` を追加・改修したら、その値の出所（ユーザー入力か固定か）を必ず確認する。

---

実装・改修後は、Skill `review-responsibility` でエスケープ漏れ・上書きパスを点検すること。
