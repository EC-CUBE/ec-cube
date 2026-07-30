/**
 * E2E テスト共通のクレデンシャル・接続情報。
 *
 * EC-CUBE 2 系の `e2e-tests/config/default.config.ts` と同じく、環境変数の
 * フォールバック付き定数を 1 箇所へ集約する。各 spec はここから import し、
 * パスワード等をベタ書きしない（NIST SP 800-63B-4 準拠でパスワード要件が
 * 変わっても、修正箇所をこのファイルに閉じ込めるため）。
 */

/** 管理画面のルーティングプレフィックス（既定 `admin`）。 */
export const ADMIN_ROUTE = process.env.ECCUBE_ADMIN_ROUTE ?? 'admin';

/** 管理者ログイン ID（初期管理者）。 */
export const ADMIN_USER = process.env.ADMIN_USER ?? 'admin';

/** 管理者ログインパスワード（初期管理者の照合用。登録時要件は非適用）。 */
export const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD ?? 'password';

/** 既存フィクスチャ会員のログイン照合用パスワード。 */
export const CUSTOMER_PASSWORD = process.env.CUSTOMER_PASSWORD ?? 'password';

/**
 * 会員登録・パスワード変更で使う「有効なパスワード」。
 * NIST SP 800-63B-4（PR #6858 / Issue #6488）で 15 文字以上が必須になったため、
 * 15 文字未満・ブロックリスト該当・漏洩既知の値は使用不可。
 */
export const VALID_PASSWORD = process.env.VALID_PASSWORD ?? 'EccubeE2ePassword1';

/**
 * NFKC 正規化テスト用のパスワード（`front-password-nist.spec.ts`）。
 * 半角カナ + 数字。NFKC 正規化で全角へ変換され、正規化後 15 文字以上（min15 要件）。
 * 環境変数で差し替える場合も「NFKC で正規化後の文字列が変わる（＝往復テストが成立する）」
 * 値であること。純 ASCII など正規化で不変な値を渡すと NIST spec の往復検証が壊れる。
 */
export const RAW_PASSWORD = process.env.RAW_PASSWORD ?? 'ﾊﾟｽﾜｰﾄﾞﾃｽﾄ1234567';

/**
 * {@link RAW_PASSWORD} を NFKC 正規化した文字列（既定値では全角・15 文字）。
 * RAW から導出するため、環境変数で RAW を差し替えても正規化後の値と常に整合する。
 */
export const NORMALIZED_PASSWORD = RAW_PASSWORD.normalize('NFKC');
