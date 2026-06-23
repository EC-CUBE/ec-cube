---
name: plugin
description: EC-CUBE 4.4 のプラグインを実装・改修するときの規約。「プラグインを作って」「プラグインで機能を追加して」「PluginManagerを書いて」「プラグインでエンティティ/フォーム/コントローラを拡張して」「composer.jsonのメタデータを直して」「プラグインのライフサイクル処理を実装して」などと言われたとき、または app/Plugin 配下を作成・編集するときに使用する。
---

# プラグイン規約（EC-CUBE 4.4）

**対象**: `app/Plugin/{PluginCode}/**`（コア側の仕組みは `src/Eccube/Plugin/`, `src/Eccube/Service/PluginService.php`）
**前提**: Symfony 7.4 / PHP 8.2+

> 目的: 自己完結したパッケージとして機能を追加し、コアやプロジェクト固有カスタマイズ（`app/Customize/`）と混同しないこと。
> プロジェクト固有の 1 回限りの改変は `app/Customize/`、再配布・着脱可能な機能は `app/Plugin/`。

## プラグインの最小構成と配置

プラグインは必ず **`app/Plugin/{PluginCode}/`** に置く。PSR-4 で `Plugin\{PluginCode}\` ＝ `app/Plugin/{PluginCode}/`。

```
app/Plugin/{PluginCode}/
  ├── composer.json        # 必須
  └── PluginManager.php     # 任意（ライフサイクル処理が要るときだけ）
```

- **PluginCode は `^\w+$`（英数字とアンダースコアのみ）**。ディレクトリ名・名前空間・クラス名に使われるため厳格。`-` は不可。
- `composer.json` の必須は **`version` と `extra.code`**。`extra.code` が無いと install で失敗する。
  推奨: `name`（`ec-cube/xxx`）, `description`, `type: "eccube-plugin"`, `require` に `ec-cube/plugin-installer`。

```json
{
  "name": "ec-cube/example",
  "version": "1.0.0",
  "description": "...",
  "type": "eccube-plugin",
  "require": { "ec-cube/plugin-installer": "*" },
  "extra": { "code": "Example" }
}
```

## ライフサイクル（PluginManager）

ライフサイクル処理が必要なときだけ **`Plugin\{Code}\PluginManager`**（クラス名は固定）を `AbstractPluginManager` を継承して作る。
5 メソッドはすべて**デフォルト no-op**なので、必要なものだけ override すればよい。

| メソッド | 呼ばれる契機 | 用途の例 |
|---|---|---|
| `install` | インストール時（postInstall 経由） | 初期データ投入 |
| `enable` | 有効化時 | マイグレーション適用 |
| `disable` | 無効化時 | マイグレーションを戻す |
| `update` | 更新時 | 差分マイグレーション |
| `uninstall` | アンインストール時（initialized 済みのみ） | クリーンアップ |

```php
namespace Plugin\Example;

use Eccube\Plugin\AbstractPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PluginManager extends AbstractPluginManager
{
    public function enable(array $meta, ContainerInterface $container): void
    {
        // マイグレーション適用（AbstractPluginManager::migration を利用）
        $conn = $container->get('doctrine')->getManager()->getConnection();
        $this->migration($conn, $meta['code']);
    }
}
```

- メソッドのシグネチャは **`(array $meta, ContainerInterface $container)`**。`$meta['code']` は composer.json の `extra.code`。
- **install 直後はデフォルト無効（enabled=false）**。有効化は `eccube:plugin:enable` コマンドか管理画面で行う（無効化はコンソールコマンドが無く、管理画面から行う）。

## 拡張パターン（プラグインから何を足すか）

| 拡張 | 置き場所 / 名前空間 | 作法 | 参照 Skill |
|---|---|---|---|
| **エンティティ拡張** | `Plugin\{Code}\Entity\*Trait` | トレイトに `#[EntityExtension(\Eccube\Entity\Target::class)]` を付け、`#[ORM\Column]` でカラム追加 | `entity` |
| **コントローラ追加** | `Plugin\{Code}\Controller` | `#[Route]` 属性でルーティング | `controller` |
| **フォーム拡張** | `Plugin\{Code}\Form\Extension` | `AbstractTypeExtension` を継承し `getExtendedTypes()` で対象指定 | `formtype` |
| **リポジトリ拡張** | `Plugin\{Code}\Repository` | — | `repository` |
| **イベント購読** | `Plugin\{Code}\EventListener` 等 | `EventSubscriberInterface`（autoconfigure で自動登録） | `event-subscriber` |
| **受注処理の拡張** | `Plugin\{Code}\Service\PurchaseFlow\Processor` | `#[CartFlow]` / `#[ShoppingFlow]` / `#[OrderFlow]` 属性で対象フローへ自動登録 | `service` |
| **マイグレーション** | `Plugin\{Code}\DoctrineMigrations\Version*` | `AbstractMigration` を継承（テーブルは `migration_{code}` で管理） | `migration` |

```php
// エンティティ拡張の例: app/Plugin/Example/Entity/CustomerExampleTrait.php
namespace Plugin\Example\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Attribute\EntityExtension;

#[EntityExtension(\Eccube\Entity\Customer::class)]
trait CustomerExampleTrait
{
    #[ORM\Column(name: 'example_no', type: 'smallint', nullable: true)]
    public $example_no;
}
```

## プロキシ再生成（忘れやすい急所）

エンティティ拡張（トレイト）を足したら **プロキシの再生成が必要**。
enable/disable/uninstall 時はコア（`PluginService`）が自動で再生成するが、**開発中に手で確認するときは明示実行する**:

```bash
bin/console eccube:generate:proxies   # app/proxy/entity/ を再生成
```

トレイトに `#[EntityExtension]` を付け忘れるとプロキシに反映されず、カラムが認識されない。

## よくある間違い

- ❌ `composer.json` に `extra.code` が無い → ✅ 必須。無いと install で失敗
- ❌ PluginCode に `-` を使う → ✅ `^\w+$`（英数字・アンダースコアのみ）
- ❌ install しただけで動くと思う → ✅ install 直後は無効。`eccube:plugin:enable --code=...` で有効化
- ❌ エンティティトレイトに `#[EntityExtension(Target::class)]` を付け忘れ → ✅ 付けないとプロキシに乗らない
- ❌ トレイト追加後にプロキシ再生成を忘れる → ✅ `bin/console eccube:generate:proxies`
- ❌ プロジェクト固有の 1 回限りの改変をプラグイン化 → ✅ それは `app/Customize/`。着脱・再配布するものだけプラグイン
- ❌ `app/Customize`（`Eccube\` を直接拡張）と `app/Plugin`（`Plugin\{Code}\` 独立名前空間）の名前空間を混同 → ✅ 置き場所で名前空間を使い分ける

## 実行・確認方法

コンソール・QA ツールは Docker 上で実行する（Skill `docker-qa`）。

```bash
bin/console eccube:plugin:install --code=Example   # 既存ディレクトリからインストール
bin/console eccube:plugin:enable  --code=Example   # 有効化
bin/console eccube:plugin:update  Example          # 更新（PluginManager::update を呼ぶ）
bin/console eccube:generate:proxies                # プロキシ再生成
bin/console doctrine:schema:validate               # スキーマ整合確認
```

- 状態確認は `dtb_plugin` テーブル（`code` / `enabled` / `initialized`）と `app/proxy/entity/` を見る。

---

実装・改修後は、各レイヤの Skill（`entity` / `controller` / `formtype` / `migration` 等）と
`review-responsibility` で責務分離を点検すること。
