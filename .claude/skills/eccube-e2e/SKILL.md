---
name: eccube-e2e
description: EC-CUBE 4.4 の E2E テスト（Playwright・`e2e/` 配下）を実装・修正するときの規約。「E2Eテストを書いて」「Playwrightテストを追加して」「E2Eが落ちる/flakyを直して」「spec を追加して」などと言われたとき、または `e2e/tests` `e2e/pages` `e2e/models` `e2e/helpers` `e2e/fixtures` 配下を作成・編集するときに使用する。
---

# E2E テスト規約（Playwright・EC-CUBE 4.4）

**対象**: `e2e/tests/*.spec.ts`, `e2e/{pages,models,helpers,fixtures}/*.ts`
**前提**: Playwright（TypeScript）。`cd e2e && npm ci` 済み。baseURL は環境変数 `BASE_URL`（既定 `http://127.0.0.1:8000`）。

> EC-CUBE の E2E は `e2e/` 配下の Playwright が正。`codeception/` はレガシー残置で CI 無効（`AGENTS.md` 参照）。
> 混同しないこと。

## 基本ルール

- **spec ファイル名の接頭辞 = プロジェクト = 認証状態**（`e2e/playwright.config.ts` の `testMatch`）。新規 spec はこの規則で命名する:
  - `admin-*` / `plugin-*` … storageState=`.auth/admin.json` で**管理者ログイン済み** → spec 側でログイン不要。
  - `front-*` / `deny*` … 空 state（`{ cookies: [], origins: [] }`）で**未認証** → 会員操作は spec 内でログインするか、admin 経由で会員を作成する。
- **命名規約**: `test.describe('… (EFxx)')`（フロント）/ `(EAxx)`（管理）。test 名は `EF0401-UC01-T01 会員登録 …`（機能ID-ユースケース-テストID + 日本語説明）。既存 `front-customer.spec.ts` / `admin-customer.spec.ts` に倣う。
- **状態を引き継ぐ一連のテスト**（create→search→edit→delete 等）は `test.describe.configure({ mode: 'serial' })` を付ける。`playwright.config.ts` は `workers: 1` / `fullyParallel: false`。
- **テストデータは投入済み前提**: `global-setup.ts` が `setup-fixtures.php` を実行し、会員・商品・受注を投入する。固定会員 `playwright@test.test`（ACTIVE）などを再利用し、新規作成データはメール等を `nist_${Date.now()}@example.com` のように一意化して衝突を避ける。
- **パスは baseURL 相対**（`page.goto('/entry')`）。ホスト/ポートの直書きは禁止（環境差・ローカル/CI 差を吸収するため）。

## 実装パターン

- **認証**: admin/plugin spec はログイン不要。認証フロー自体を検証するときだけ `test.use({ storageState: { cookies: [], origins: [] } })` で上書きして毎回ログインする（`admin-auth.spec.ts`）。
- **POM（Page Object Model）**: `tsconfig.json` のパスエイリアスで import する。
  - `@pages/*` … UI ページ単位の操作（例 `PluginManagePage`）。`static at(page)` でページ到達を検証して返す。
  - `@models/*` … 操作チェーン＋DB/FS 検証（例 `StorePlugin.インストール()` が UI 操作後に `db.getPlugin()` で状態確認）。
  - `@helpers/*` … 低レベル: `db-client`（テーブル/カラム/プラグイン状態）、`file-helper`（proxy への trait 注入確認）、`tar-helper`（プラグイン .tgz 圧縮）。
- **plugin 系 spec** は拡張 fixture（`e2e/fixtures/plugin-test.ts`）の `{ page, db, config }` を test 引数で受け取る。
- **待機は web-first assertion** を使う: `await expect(locator).toBeVisible()` / `toContainText()` / `page.waitForURL()`。`expect.timeout` / `actionTimeout` は 30s に設定済み。
- **入力ページ残留の判定**は、確認画面でパスワードが hidden になる性質を使い、入力欄（例 `#entry_plain_password_first`）の `toBeVisible()` で行う。

## よくある間違い

- ❌ `waitForTimeout(固定ms)` で同期を取る → ✅ web-first assertion（`expect().toBeVisible()` / `waitForFunction()`）で「状態」を待つ。**例外**: yubinbango 郵便番号の自動入力など外部 JS 由来の待機のみ許容（理由をコメントで明記）。
- ❌ `front-*` spec で管理者ログイン済みを前提にする → ✅ front は未認証 state。spec 内でログインするか admin 経由で会員作成する。
- ❌ 固定件数で assert（`検索結果：1件が該当`）→ ✅ 正規表現（`/検索結果：\d+件が該当/`）。retry 時の重複データに強くする（`admin-product.spec.ts` 修正例）。
- ❌ セレクタが複数要素にマッチ（strict mode violation）→ ✅ `.first()` か `data-*` 属性（例 `button[data-bs-target="#initializationConfirm"]`）で一意化する。
- ❌ テスト境界で管理者セッションが切れて 401 → ✅ `ensureAdminLoggedIn()` 等で再ログインしてから操作（`admin-basicinfo.spec.ts` 修正例）。
- ❌ retry でプラグイン/データが残留し「既にインストール済み」で再失敗 → ✅ `beforeEach`/`afterEach` で cleanup（無効化→削除→ディレクトリ削除。`plugin-misc.spec.ts` 修正例）。
- ❌ パスワードを見た目の文字数で作る → ✅ NFKC 正規化後で 15 文字以上か数える（min15。`[...str.normalize('NFKC')].length` で確認。#6488）。
- ❌ 更新直後の値を、それを表示する任意の画面から読んで検証する → ✅ **`APP_ENV=e2e` は Doctrine の結果キャッシュが有効**（dev/test は `result_cache_driver: ~` で無効）。`enableResultCache` を使う画面は最大 10 秒前の値を返すので、キャッシュしない画面から読む（#7016 の在庫は商品編集画面ではなく商品一覧）。
- ❌ 在庫を戻す受注ステータス遷移を PHPUnit の Web テストで書く → ✅ 悲観ロックが `TransactionRequiredException` になるため E2E で書く（test 環境は `TransactionListener` 無効・e2e は有効。#7016）。
- ❌ 新規 `admin-*`/`front-*` spec を作ったのに CI で実行されない → ✅ `.github/workflows/e2e-test.yml` の `suite:` 配列にファイル名（接尾辞 `.spec.ts` 抜き）を追加する。

## 実行・確認方法

```bash
cd e2e && npm ci
# project は接頭辞で決まる。setup（ログイン/データ投入の前段）を併せて指定する
npx playwright test --project=setup --project=front-tests  front-product.spec.ts
npx playwright test --project=setup --project=admin-tests  admin-customer.spec.ts
npx playwright test --project=setup --project=plugin-tests plugin-install.spec.ts
```

CI 構造（spec 群ごとに担当ワークフローが分かれている）:

- `e2e-test.yml` … `admin-*` / `front-*` を `matrix.suite` で 1 ファイル = 1 シャード実行。**新規 admin-/front- spec はここに追加**。
- `plugin-test.yml` … plugin 系を `npx playwright test plugin-install --grep "${METHOD}"` 等で method 単位実行。
- `e2e-test-throttling.yml` … `front-throttling.spec.ts` を `-g "${METHOD}"` で実行。
- `deny-test.yml` … `deny.spec.ts` を実行。
