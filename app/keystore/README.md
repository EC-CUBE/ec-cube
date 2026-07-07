# app/keystore — 暗号鍵・シークレット保管庫

EC-CUBE 本体 (core) の機能が使う暗号鍵・シークレット素材の共通保管庫です。
機能単位で `app/keystore/<feature>/` にサブディレクトリを切り、鍵ファイルを配置します。

```
app/keystore/
  .gitkeep
  .htaccess                       # deny from all (多重防御)
  README.md
  <feature>/                      # 機能単位のサブディレクトリ (例: agent-commerce)
    <purpose>.key                 # 鍵ファイル (PEM 等)。VCS 管理外・パーミッション 600
```

現在の利用機能:

| feature | 内容 | 実装 |
|---------|------|------|
| `agent-commerce` | UCP (RFC 9421) の EC P-256 署名鍵 (`ucp_signing.key`) | `Eccube\Service\Security\FilesystemKeyStore` / `Eccube\Service\AgentCommerce\Security\UcpMessageSigner` |

## 保護 (重要: `app/` は web ルート内)

EC-CUBE は `composer.json` の `"public-dir": "."` により**プロジェクトルート自体が docroot** です。
そのため `app/keystore/` も web ルート内に置かれ、web サーバ設定による拒否で保護します。
以下の多重防御を前提とします。

1. **`app/.htaccess`** の `deny from all` が `app/` 配下へ再帰適用される (Apache)。
2. **`app/keystore/.htaccess`** の `deny from all` (本ディレクトリ直下にも明示配置)。
3. **ルート `.htaccess`** の拡張子ブロックに `.key` を含む (`AllowOverride None` 環境でサブディレクトリ `.htaccess` が無視されても `.key` を直接配信させない核心ガード)。
4. **`.gitignore`** で鍵ファイルを VCS から除外 (`.gitkeep` / `.htaccess` / `README.md` のみ追跡)。
5. ファイルパーミッションは生成時に **600**、格納ディレクトリは **700**。

### 設置形態別の注意

- **Apache**: `.htaccess` を honor する構成 (`AllowOverride` が有効) であること。`AllowOverride None` の場合はルート `.htaccess` の `.key` 拡張子ブロックが効かないため、後述の env 経路を強く推奨。
- **Nginx**: 同梱の `nginx.conf.sample` にある `location ~ ^/(var|test|vendor|app|src|bin) { deny all; }` で `/app` 配下を拒否すること。自前設定でこの deny を入れ忘れると鍵が配信されうる。

## 鍵の解決順 (env → 既定ファイル)

`FilesystemKeyStore` は以下の順で鍵パスを解決します。

1. **環境変数によるパス上書き** — purpose ごとに絶対パスを環境変数で指定できる。
   AWS Secrets Manager / Azure Key Vault 等からマウントしたパスを指す運用を想定 (本番・Enterprise 推奨)。
2. **既定ファイル** — `app/keystore/<feature>/<purpose>.key`。env を使えない環境 (共有レンサバ等) のフォールバック。
   初回利用時に鍵が無ければ自動生成し、パーミッション 600 で永続化する。

例 (agent-commerce の UCP 署名鍵): 環境変数 `ECCUBE_AGENT_COMMERCE_UCP_SIGNING_KEY` にパスを設定すると
既定ファイルより優先されます (`app/config/eccube/services.yaml` の `eccube.keystore.agent_commerce` を参照)。

## 保管方式の差し替え (app/Customize)

`Eccube\Service\Security\KeyStoreInterface` を実装したクラスへ差し替えることで、
ファイル以外の保管先 (DB・Secrets Manager SDK 連携等) を注入できます。
`app/Customize` でのサービスデコレーション / 置き換えで対応してください。

## セキュリティ留意点

- `app/keystore/` は **web ルート内**のため、`.htaccess` を honor しない構成では漏洩しうる。
  これは EC-CUBE 既存の構造的条件 (`.env` 等も同条件) だが、**漏洩耐性は env → Secrets Manager / Key Vault 経路が上位**。
  可能な環境ではファイル保管ではなく env 経路を推奨する。
- 鍵素材のみを置き、PCI / 個人情報を鍵ファイルに含めない。

## 新しい機能を追加する場合

1. `app/config/eccube/services.yaml` で抽象 `Eccube\Service\Security\FilesystemKeyStore` を parent に、
   `$feature: '<feature>'` を束ねた `eccube.keystore.<feature>` インスタンスを登録する。
2. 利用側サービスへ `@eccube.keystore.<feature>` を注入する。
3. 必要なら env によるパス上書き (`$envPathOverrides`) を追加する。
