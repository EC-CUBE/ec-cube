---
name: security
description: EC-CUBE 4.4 の認証・認可・CSRF などセキュリティを実装・改修・点検するときの規約。「認可を追加して」「アクセス制御を直して」「このルートに権限チェックを入れて」「CSRF対策を確認して」「Voterを作って」「セキュリティ監査して」などと言われたとき、または src/Eccube/Security 配下・app/config/eccube/packages/security.yaml を作成・編集するとき、認可漏れ/CSRF漏れ/IDOR を点検するときに使用する。
---

# セキュリティ規約 — 認証・認可・CSRF（EC-CUBE 4.4）

**対象**: `src/Eccube/Security/**/*.php`, `app/config/eccube/packages/security.yaml`,
および全コントローラの認可・CSRF 判断（コントローラ側の作法は Skill `controller` も参照）
**前提**: Symfony 7.4 / PHP 8.2+

> 目的: EC-CUBE の「ファイアウォール＋ロール＋Voter」というアクセス制御モデルを正しく理解し、
> 認可漏れ・CSRF 漏れ・IDOR（他人のリソース参照）を作り込まない／見逃さないこと。

## アクセス制御モデル（まず全体像を掴む）

EC-CUBE は **個別アクションの `#[IsGranted]` ではなく、ファイアウォール＋ロール＋Voter** で制御する。
設定は `app/config/eccube/packages/security.yaml`。

- **firewalls** は 3 つ:
  - `admin`: `pattern: '^/%eccube_admin_route%/'` — `Member`(管理者) を認証。`enable_csrf: true`、login throttling 有り。
  - `customer`: `pattern: '^/'`（サイト全体）— `Customer`(会員) を認証。remember_me 有り。
  - `dev`: `security: false` — 静的リソース等を認証対象外にする。
- **access_decision**: `strategy: unanimous` / `allow_if_all_abstain: false`。
  → **1 つでも Voter が DENY すれば拒否**。全 Voter が棄権したら拒否（明示的に許可が必要）。
- **ロール**:
  - `ROLE_ADMIN` — `Member::getRoles()` が固定で返す（管理者）。
  - `ROLE_USER` — フロント会員の暗黙ロール。
  - 管理者の権限細分は **`Authority` マスタ**（`ADMIN=0` システム管理者 / `OWNER=1` 店舗オーナー）。

**最重要の含意**: 新規の管理アクションは、認可の主装置が**ファイアウォール**なので
**必ず `%eccube_admin_route%` プレフィックス配下のパス**に置く。配下に置けば認証が要求される。
配下から外すと無認証で到達できる（よくある重大な事故）。

## 基本ルール

- 管理アクションは `#[Route(path: '/%eccube_admin_route%/...')]` に置く（admin firewall の保護下に入れる）。
- 管理画面内のさらに細かい権限制御は **`AuthorityVoter`**（`src/Eccube/Security/Voter/AuthorityVoter.php`）が担う。
  `AuthorityRole` の `deny_url`（正規表現）に基づき、URL 単位で `ACCESS_DENIED` を返す。
  → 「特定の権限にこの画面を見せない」は **コントローラ改変ではなく `dtb_authority_role` の設定**で実現される。
- **GET 以外の状態変更（更新・削除・Ajax）は必ず CSRF トークンを検証する**。
  フォーム経由（`handleRequest`＋`isValid`）は保護込み。フォームを介さない処理は `$this->isTokenValid()` を明示的に呼ぶ。
- **認証状態の使い分け**:
  - パスワード変更・退会・購入確定など重要操作 → **`IS_AUTHENTICATED_FULLY`**（remember-me を除外）。
  - 単なるログイン状態の確認 → `IS_AUTHENTICATED_REMEMBERED` でよい。
- **フロントで `{id}` 等の他人のリソースを受け取るアクションは所有権を検証する**（後述の IDOR）。
- パスワードは `PasswordHasher`（`algorithm: 'auto'`）任せ。**自前ハッシュ・平文比較を書かない**。
- Twig 出力のエスケープ（XSS）は Skill `twig-template` を参照（`|raw` の濫用に注意）。

## 実装パターン

### CSRF トークン検証（フォームを介さない削除・Ajax）
基底クラス `AbstractController::isTokenValid()` を呼ぶ。トークン名は `Constant::TOKEN_NAME`（`src/Eccube/Common/Constant.php`、値は `'_token'`）、
リクエストパラメータ `_token` またはヘッダ `ECCUBE-CSRF-TOKEN` から取得し、失敗時は例外を投げる。

```php
// src/Eccube/Controller/.../CustomerController.php の delete が定石
$this->isTokenValid();   // CSRF 検証。失敗で AccessDeniedHttpException
$this->customerService->delete($Customer);
```

### 認可チェック（コントローラ内で明示する場合）
```php
// ROLE で分岐（例: ログイン済み管理者をホームへ）
if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) { ... }

// 重要操作は FULLY を要求（remember-me を弾く）
if ($this->isGranted('IS_AUTHENTICATED_FULLY')) { ... }
```

### URL 単位の権限制御（Voter 経由・コア標準）
`AuthorityVoter` は `Member` の `Authority` に紐づく `AuthorityRole.deny_url` を取得し、
リクエストパスが一致したら `ACCESS_DENIED` を返す（`src/Eccube/Security/Voter/AuthorityVoter.php`）。
**新しい「見せない画面」を増やすときは Voter を書くのではなく deny_url 設定で対応できないか先に検討する。**

### 独自 Voter を追加する場合
`Symfony\Component\Security\Core\Authorization\Voter\Voter` を継承し `supports()`/`voteOnAttribute()` を実装する。
`services.yaml` の `autoconfigure: true` により `security.voter` タグは自動付与される（手動登録は不要。コアの既存 Voter に倣う）。
**`access_decision` が unanimous なので、棄権（ABSTAIN）と拒否（DENY）の使い分けを誤ると全体が拒否になる**点に注意。

## よくある間違い（認可・CSRF・IDOR — ツールでは検出できない観点）

- ❌ 管理アクションを `%eccube_admin_route%` 配下**以外**に置く → ✅ 配下に置き admin firewall の保護下にする
- ❌ フォームを介さない POST/DELETE/Ajax で CSRF 未検証 → ✅ `$this->isTokenValid()` を呼ぶ（GET 以外）
- ❌ Ajax 専用アクションで XHR 以外（直アクセス等）も受け付ける → ✅ CSRF 検証に加え **`$request->isXmlHttpRequest()`** を併用し XHR 由来に限定する（コア例: `NonMemberShoppingController` / `Admin/Content/MaintenanceController`）
- ❌ フロントで `{id}` から取得したエンティティを所有権チェックせず編集/削除（**IDOR**）
  → ✅ `$this->getUser()` と突き合わせ、他人のリソースなら `AccessDeniedHttpException`
- ❌ パスワード変更・退会など重要操作を `IS_AUTHENTICATED_REMEMBERED` で許可
  → ✅ `IS_AUTHENTICATED_FULLY` を要求（盗難 Cookie での実行を防ぐ）
- ❌ 独自 Voter で「対象外」を `ACCESS_DENIED` で返す → ✅ 対象外は `ACCESS_ABSTAIN`（unanimous 戦略で誤拒否を防ぐ）
- ❌ 自前でパスワードをハッシュ/平文比較 → ✅ `PasswordHasher` 経由に統一
- ❌ ユーザー入力を Twig で `|raw` 出力 → ✅ エスケープを効かせる（Skill `twig-template`）
- ❌ アップロード/ファイル操作を伴う管理ルートを新設したが `eccube_restrict_file_upload` の遮断対象を考慮しない → ✅ コアは `eccube_restrict_file_upload === '1'` のとき `eccube_restrict_file_upload_urls` のルートを `RestrictFileUploadListener` で遮断する。新規ルートを遮断対象に含めるべきか検討する

## 実行・確認方法

QA ツール（PHPUnit / PHPStan / PHP-CS-Fixer）の実行方法は AGENTS.md「開発コマンド」を参照。

- **Voter の単体テスト**: `tests/Eccube/Tests/Security/Voter/AuthorityVoterTest.php`（deny_url パターンごとの GRANTED/DENIED）。
- **管理ログイン/リダイレクトのテスト**: `tests/Eccube/Tests/Web/Admin/LoginControllerTest.php`
  （未ログインで admin 配下にアクセスすると 302 になることを確認）。
- **認可漏れの目視点検**: 新規 admin ルートが `%eccube_admin_route%` 配下にあるか、
  状態変更アクションで `isTokenValid()` またはフォーム検証を通っているか、
  フロントの `{id}` 取得に所有権チェックがあるかを確認する。
- 静的解析・整形は `vendor/bin/phpstan analyse src` / `vendor/bin/php-cs-fixer fix`（Docker 経由）。

---

実装・改修後は、Skill `review-responsibility` で責務分離とあわせて認可・CSRF・IDOR を点検すること。
