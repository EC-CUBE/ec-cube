import { test, expect } from '@playwright/test';
import { randomBytes } from 'crypto';
import { InstallPage, type InstallDatabaseConfig } from '../pages/install.page';

/**
 * Web インストーラをインストール完了まで通す E2E.
 *
 * 前提条件 (満たされない場合はテスト冒頭で fail する):
 *   - EC-CUBE が未インストールであること (プロジェクトルートに .env が無い)
 *     index.php は .env / .env.local / .env.local.php がいずれも無いときだけ
 *     .env.install を読み込んで APP_ENV=install になる.
 *   - サーバ起動時に OS 環境変数 APP_ENV を設定していないこと
 *     設定していると .env 生成後も install 環境のままになり、完了後の
 *     管理画面への遷移が確認できない.
 *   - ECCUBE_AUTH_MAGIC を設定していないこと
 *     設定すると InstallController::isInstalled() が true になり、
 *     step3 が存在しない dtb_base_info を SELECT して落ちる.
 *   - step4 で指定するデータベースが作成済みであること
 *     step5 の dropTables() はスキーマを消すだけでデータベースは作らない.
 */

// 管理画面のディレクトリ名. Step3Type の NotEqualTo 制約により 'admin' は使えない
const ADMIN_DIR = process.env.INSTALL_ADMIN_DIR ?? 'e2einstall';
const SHOP_NAME = 'E2E インストールテストショップ';
const ADMIN_EMAIL = 'e2e-install@example.com';
const ADMIN_LOGIN_ID = 'e2eadmin';

/**
 * 管理画面パスワードを実行ごとに生成する.
 *
 * install 環境では NotCompromisedPassword (haveibeenpwned) が有効なため、
 * 固定値だと漏洩リスト入りした時点でテストが壊れる. ランダム値なら該当しない.
 * eccube_password_min_len は 15.
 */
const ADMIN_PASSWORD = `E2e!${randomBytes(12).toString('base64url')}`;

/** DB は E2E 実行環境の DATABASE_URL から組み立てる. 未設定なら sqlite */
function databaseConfig(): InstallDatabaseConfig {
  const url = process.env.DATABASE_URL;
  if (!url || url.startsWith('sqlite')) {
    return { driver: 'pdo_sqlite' };
  }

  const parsed = new URL(url);
  const driver = parsed.protocol.startsWith('postgres') ? 'pdo_pgsql' : 'pdo_mysql';
  return {
    driver,
    host: parsed.hostname,
    port: parsed.port,
    name: parsed.pathname.replace(/^\//, ''),
    user: decodeURIComponent(parsed.username),
    password: decodeURIComponent(parsed.password),
  };
}

// 2 つ目のテストは 1 つ目でインストールが完了していることが前提のため直列実行する
test.describe.serial('インストーラ', () => {
  test('インストール完了まで実行し、管理画面へ遷移できる', async ({ page }) => {
    // インストーラのフロー (/install/*) で 4xx/5xx が出ないことを検証する.
    // 完了後の管理画面 (/${ADMIN_DIR}/*) への遷移は対象外にする。完了画面の
    // 「管理画面を表示」ボタンはキャッシュ削除中の一時的な 500 を前提に
    // waitForAdminPage() で再試行するため、その 500 を集計すると最終的に
    // 遷移できてもテストが失敗してしまう。
    const responseErrors: string[] = [];
    page.on('response', (response) => {
      if (response.status() >= 400 && new URL(response.url()).pathname.startsWith('/install/')) {
        responseErrors.push(`${response.status()} ${response.request().method()} ${response.url()}`);
      }
    });

    // 前提条件: インストーラが開いていること
    const entry = await page.goto('/install/step1');
    expect(
      entry?.status(),
      'インストーラに到達できません。EC-CUBE がインストール済み (.env が存在する) の可能性があります'
    ).toBe(200);

    const install = await InstallPage.go(page);

    await install.step1_次へ();

    await install.step2_権限チェックが正常なこと();
    await install.step2_次へ();

    await install.step3_サイトの設定を入力({
      shopName: SHOP_NAME,
      email: ADMIN_EMAIL,
      loginId: ADMIN_LOGIN_ID,
      loginPass: ADMIN_PASSWORD,
      adminDir: ADMIN_DIR,
    });

    await install.step4_データベースの設定を入力(databaseConfig());

    await install.step5_データベースを初期化();

    await install.complete_完了していること();
    await install.complete_管理画面ボタンの有効化を待つ();

    // 「管理画面を表示」で管理画面へ遷移できること.
    // このボタンは install_plugin_redirect を fetch してから遷移する.
    await install.complete_管理画面を表示();
    await page.waitForURL(new RegExp(`/${ADMIN_DIR}/login`));
    await expect(page.locator('#login_id')).toBeVisible();

    // インストーラが通信エラーを起こしていないこと
    expect(responseErrors, `4xx/5xx が発生しました: ${responseErrors.join(', ')}`).toEqual([]);
  });

  test('インストール後、インストーラは閉じられ、店頭と管理画面が表示できる', async ({ page }) => {
    // 直前のテストでインストール済みであること (.env が生成され APP_ENV=prod になる) が前提

    // .env が生成され install 環境ではなくなるため、インストーラは閉じられる
    const installer = await page.goto('/install/step1');
    expect(installer?.status()).toBe(404);

    // 店頭が表示でき、step3 で入力した店舗名が反映されている (DB と .env の生成結果)
    await page.goto('/');
    await expect(page).toHaveTitle(new RegExp(SHOP_NAME));

    // step3 で指定した管理画面ディレクトリでログイン画面が表示できる
    // (.env の ECCUBE_ADMIN_ROUTE が反映されている)
    await page.goto(`/${ADMIN_DIR}/login`);
    await expect(page.locator('#login_id')).toBeVisible();
    await expect(page.locator('#password')).toBeVisible();
  });

  test('インストーラで登録した管理者でログインできる', async ({ page, baseURL }) => {
    // インストール後は APP_ENV=prod になる. prod の session cookie は
    // cookie_samesite: none / cookie_secure: auto のため、HTTP 接続では
    // Secure が付かず、ブラウザが SameSite=None の cookie を破棄してしまい
    // セッションを維持できない. install / e2e 環境にはこれを回避する override が
    // あるが、prod は HTTPS 前提のため override しない.
    // したがってこの検証には HTTPS が必要 (symfony serve など).
    test.skip(
      !baseURL?.startsWith('https://'),
      'prod の session cookie は SameSite=None のため HTTPS でのみ検証できる'
    );

    await page.goto(`/${ADMIN_DIR}/login`);
    await page.locator('#login_id').fill(ADMIN_LOGIN_ID);
    await page.locator('#password').fill(ADMIN_PASSWORD);
    await page.getByRole('button', { name: 'ログイン' }).click();
    await expect(page.locator('.c-pageTitle__titles')).toContainText('ホーム', { timeout: 30_000 });
  });
});
