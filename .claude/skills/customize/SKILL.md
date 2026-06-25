---
name: customize
description: EC-CUBE 4.4 の app/Customize によるカスタマイズ（コアのエンティティ/フォーム/サービス/テンプレートをアップグレード安全に拡張・上書き）の規約。「app/Customizeでカスタマイズして」「コアのエンティティにフィールドを足して」「既存フォームに項目を追加して」「コアのサービスを上書き/デコレートして」「テンプレートを上書きして」などと言われたとき、または app/Customize 配下を作成・編集するときに使用する。
---

# app/Customize 規約（EC-CUBE 4.4）

**対象**: `app/Customize/**`, `app/template/**`（テンプレート上書き）
**前提**: Symfony 7.4 / PHP 8.2+ / Doctrine ORM 3.x

## 対象 / 前提

`app/Customize/` は **プロジェクト固有の改変** を置く場所。コア（`src/Eccube/`）を直接書き換えず、
ここで拡張・上書きすることで **コアのアップグレード（composer update）の影響を受けない** のが目的。

- PSR-4 で **`Customize\` ＝ `app/Customize/`**（`composer.json` の `autoload.psr-4`）。
- `app/config/eccube/services.yaml` で `Customize\` 名前空間は **autowire / autoconfigure 済み**として登録される
  （`_defaults` が `autowire: true` / `autoconfigure: true`）。`Customize\Controller\` は `controller.service_arguments` タグ付き。
- 除外: `Customize\` のサービス登録は `{Entity,Resource,Tests}` を除外する（Entity はサービスではないため）。

### plugin との使い分け（混同しない）

| | `app/Customize/`（本 Skill） | `app/Plugin/{Code}/`（Skill `plugin`） |
|---|---|---|
| 名前空間 | **`Customize\`** | `Plugin\{Code}\`（独立名前空間） |
| 想定 | **プロジェクト固有・1 回限りの改変** | **着脱・再配布できる機能パッケージ** |
| ライフサイクル | なし（常時有効） | install/enable/disable/uninstall あり |
| メタデータ | なし | `composer.json` の `extra.code` 必須 |

> プロジェクト固有の改変は `app/Customize/`、着脱・再配布するものは `app/Plugin/`。
> エンティティ拡張・フォーム拡張・proxy 再生成といった**作法そのものは両者で共通**（trait＋`#[EntityExtension]` 等）。

## 基本ルール

- PHP ファイル先頭に EC-CUBE ライセンスヘッダ。型宣言を付ける（PHPStan level 6）。
- 依存はコンストラクタインジェクション（autowire が効く）。
- 各レイヤの作法は対応 Skill に従う（`entity` / `formtype` / `controller` / `service` / `repository`）。
  本 Skill は **「Customize ならではの置き場所と上書き方法」** に絞る。

## 実装パターン

### 1. エンティティ拡張（コアエンティティにフィールド追加）

コアを書き換えず、`app/Customize/Entity/` に **trait** を置き、`#[EntityExtension(対象::class)]` を付ける。
`Eccube\Attribute\EntityExtension` は `TARGET_CLASS | IS_REPEATABLE` の属性で、`value`（対象エンティティの FQCN）を取る。

```php
// app/Customize/Entity/ProductTrait.php
namespace Customize\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Attribute\EntityExtension;

#[EntityExtension(\Eccube\Entity\Product::class)]
trait ProductTrait
{
    #[ORM\Column(name: 'custom_note', type: 'string', length: 255, nullable: true)]
    private ?string $customNote = null;

    public function getCustomNote(): ?string
    {
        return $this->customNote;
    }

    public function setCustomNote(?string $customNote): self
    {
        $this->customNote = $customNote;

        return $this;
    }
}
```

- trait を足したら **proxy 再生成**（`bin/console eccube:generate:proxies`）で `app/proxy/entity/` に反映される。
  `#[EntityExtension]` を付け忘れると proxy に乗らずカラムが認識されない。
- カラムを足すだけならマイグレーション不要（属性が源泉。`schema:update --force` が反映）。詳細は Skill `entity` / `migration`。

### 2. フォーム拡張（既存フォームに項目追加）

`Symfony\Component\Form\AbstractTypeExtension` を継承し、`getExtendedTypes()` で対象 FormType を返す。
`app/Customize/Form/Extension/` に置く（autoconfigure で `form.type_extension` として自動登録される）。

```php
// app/Customize/Form/Extension/ProductTypeExtension.php
namespace Customize\Form\Extension;

use Eccube\Form\Type\Admin\ProductType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('custom_note', TextType::class, [
            'required' => false,
            'mapped' => true,   // エンティティ拡張のプロパティに紐づける場合
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductType::class];
    }
}
```

- 実例（コア側の AbstractTypeExtension）: `src/Eccube/Form/Extension/HelpTypeExtension.php`（`getExtendedTypes()` で `FormType::class` を返し全フィールドを拡張）。
- 追加項目を画面に出すには、対応するテンプレート側にも出力を足す（下記 4）。詳細は Skill `formtype`。

### 3. サービスの上書き / デコレーション

EC-CUBE は `#[AsDecorator]` 属性は使っていない（コアに用例なし）。
**`app/config/eccube/services.yaml` に明示的な定義を足し、Symfony のデコレーション（`decorates`）で包む**のが基本。

```yaml
# app/config/eccube/services.yaml に追記
services:
    Customize\Service\MyCartServiceDecorator:
        decorates: Eccube\Service\CartService
        # 元サービスは .inner で受け取る（コンストラクタ DI）
        arguments:
            $inner: '@.inner'
```

```php
// app/Customize/Service/MyCartServiceDecorator.php
namespace Customize\Service;

use Eccube\Service\CartService;

class MyCartServiceDecorator
{
    public function __construct(private CartService $inner)
    {
    }

    // 必要なメソッドだけ振る舞いを変え、それ以外は $this->inner に委譲する
}
```

- `decorates` / `decoration_priority` / `decoration_inner_name` 等のサービスキーが使える
  （`app/config/eccube/reference.php` の DefaultsType/InstanceofType に定義あり）。
- 単純に同名サービス ID で**置き換えたい**場合は、`services.yaml` で同じクラス ID に `class:` を上書き定義する手もあるが、
  元の振る舞いを残したい拡張は **デコレーション**が安全。元クラスへ依存している箇所を壊さないよう、型は元サービスを満たすこと。
- ロジックの責務分離は Skill `service`。コントローラを追加する場合は `app/Customize/Controller/` に置く
  （`routes.yaml` の `customize_controllers` が `type: attribute` で `#[Route]` を走査、services.yaml で `controller.service_arguments` タグ付き）。

### 4. テンプレート上書き

`app/template/` の **コアと同じ相対パス**に同名ファイルを置くと上書きできる（`app/config/eccube/packages/twig.yaml` の `paths`）。
Twig の検索パスは **`app/template/...`（`eccube_theme_front_dir` / `eccube_theme_admin_dir`）がコア既定（`*_default_dir`）より先**に登録されているため、同名なら app 側が優先される。

| 対象 | 置き場所（上書き先） | コア原本 |
|---|---|---|
| 店頭（フロント） | `app/template/{テーマコード}/...`（`eccube.theme` ＝ `ECCUBE_TEMPLATE_CODE`、既定 `default`） | `src/Eccube/Resource/template/default/...` |
| 管理画面 | `app/template/admin/...`（Twig 名前空間 `admin`） | `src/Eccube/Resource/template/admin/...` |

- 例: フロントの `Product/detail.twig` を変えるなら `app/template/{テーマコード}/Product/detail.twig` にコピーして編集。
- 一部だけ差し込みたい場合は、コア改変や全文コピーより **テンプレートイベント**で挿入する方が壊れにくい（Skill `event-subscriber` / `twig-template`）。
- XSS・`raw` の扱いは Skill `twig-template`。

## よくある間違い

- ❌ コア（`src/Eccube/`）を直接書き換える → ✅ `app/Customize/` で拡張・上書きし、アップグレード安全にする
- ❌ プロジェクト固有の 1 回限りの改変をプラグイン化 → ✅ それは `app/Customize/`。着脱・再配布するものだけ `app/Plugin/`
- ❌ 名前空間を `Plugin\{Code}\` と混同 → ✅ Customize は **`Customize\` ＝ `app/Customize/`**
- ❌ エンティティ拡張の trait に `#[EntityExtension(対象::class)]` を付け忘れ → ✅ 付けないと proxy に乗らずカラムが認識されない
- ❌ trait 追加後に proxy 再生成を忘れる → ✅ `bin/console eccube:generate:proxies`
- ❌ カラム追加に ALTER マイグレーションを書く → ✅ 属性が源泉。`schema:update --force` が反映（マイグレーションは INSERT・型変更等に限る。Skill `migration`）
- ❌ 既存フォームを直接改変 → ✅ `AbstractTypeExtension` ＋ `getExtendedTypes()`（`app/Customize/Form/Extension/`）で拡張
- ❌ サービスを `#[AsDecorator]` で包む（コアの作法と不一致） → ✅ `services.yaml` で `decorates` ＋ `@.inner` 委譲
- ❌ テンプレートを上書こうとしてコア原本側を編集 → ✅ `app/template/` に同じ相対パスで同名ファイルを置く（app 側が優先）
- ❌ 上書きパスのテーマ名を間違える → ✅ フロントは `app/template/{ECCUBE_TEMPLATE_CODE}/`（既定 `default`）、管理画面は `app/template/admin/`

## 実行・確認方法

コンソール・QA ツール（PHPUnit / PHPStan / PHP-CS-Fixer）の実行方法は AGENTS.md「開発コマンド」を参照。

```bash
bin/console eccube:generate:proxies      # エンティティ拡張(trait)を足したら proxy 再生成
bin/console doctrine:schema:update --dump-sql   # 追加カラムの差分プレビュー
bin/console doctrine:schema:update --force      # 属性差分を反映（単純なカラム追加）
bin/console cache:clear                  # services.yaml / テンプレート上書きの反映確認
bin/console doctrine:schema:validate     # スキーマ整合確認
```

- サービス上書きの反映は `bin/console debug:container <ID>` / `debug:autowiring` で確認できる。
- 追加・改修後は各レイヤ Skill（`entity` / `formtype` / `service` / `controller` / `twig-template`）と
  `review-responsibility` で責務分離・セキュリティを点検する。
