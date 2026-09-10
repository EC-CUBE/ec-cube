import { test, expect } from '@playwright/test';
import { ADMIN_ROUTE } from '../config/default.config';

/**
 * 権限を分離した構成 (docker-compose.permission-lanes.yml) の管理画面。
 *
 * Web サーバー (www-data) は app/template・html/user_data・.env・app/Plugin へ書き込めないため、
 * 該当する管理画面は ECCUBE_RESTRICT_FILE_UPLOAD=1 で読み取り専用になる。
 * 内容の表示は残したまま保存操作だけを無効化し、代替の CLI コマンドを案内する、という
 * Phase 4 の設計が Apache + mod_php 経由でも成立していることを固定する。
 *
 * この spec は permission-lanes-tests project でのみ実行する
 * (.github/workflows/permission-lanes-test.yml)。 通常の CI では
 * ECCUBE_RESTRICT_FILE_UPLOAD が未設定のため成立しない。
 */

const adminRoute = ADMIN_ROUTE;

/** notice_read_only.twig が表示する警告 (admin.common.restrict_file_upload_read_only) */
const READ_ONLY_NOTICE = 'この画面は読み取り専用です。';

/** CLI で導入するプラグイン。 ワークフローが eccube:plugin:install/enable で入れておく */
const CLI_PLUGIN_CODE = 'Boomerang';

/** CLI で作成するページ名。 ワークフローが eccube:page:apply で作っておく */
const CLI_PAGE_NAME = '権限分離テストページ';

test.describe('権限を分離した構成の管理画面', () => {
  test('CSS 管理は内容を表示したまま保存だけ無効化される', async ({ page }) => {
    await page.goto(`/${adminRoute}/content/css`);

    await expect(page.getByText(READ_ONLY_NOTICE)).toBeVisible();
    // 代替の CLI コマンドを案内する
    await expect(page.getByText('bin/console eccube:asset:apply --type=css')).toBeVisible();
    // 403 で塞がず内容は表示する (Phase 3b で読み取りを書き込み権限から切り離した)
    await expect(page.locator('#editor')).toBeVisible();
    await expect(page.locator('#save-button')).toBeDisabled();
  });

  test('ページ管理は一覧を表示したまま保存操作だけ無効化される', async ({ page }) => {
    await page.goto(`/${adminRoute}/content/page`);

    await expect(page.getByText(READ_ONLY_NOTICE)).toBeVisible();
    await expect(page.getByText('bin/console eccube:page:apply')).toBeVisible();
    // 一覧は読める
    const rows = page.locator('tr[id^="ex-page-"]');
    expect(await rows.count()).toBeGreaterThan(0);
  });

  test('CLI で作成したページが管理画面へ反映され、削除が無効化されている', async ({ page }) => {
    // eccube:page:apply --route=permission_lanes で作成済み (ワークフローが実行する)。
    // 削除の UI はユーザーが作成したページ (EDIT_TYPE_USER) にしか出ないため、
    // 標準のフィクスチャだけでは削除の無効化を検証できない
    await page.goto(`/${adminRoute}/content/page`);

    const row = page.locator('tr[id^="ex-page-"]').filter({ hasText: CLI_PAGE_NAME }).first();
    await expect(row).toBeVisible();

    // 削除リンクは押せない (a 要素のため disabled クラスと aria-disabled で無効化する)
    const deleteLink = page.locator('a.btn-ec-delete[href*="/delete"]').first();
    await expect(deleteLink).toHaveClass(/disabled/);
    await expect(deleteLink).toHaveAttribute('aria-disabled', 'true');
  });

  test('ファイル管理はアップロードと新規作成が無効化される', async ({ page }) => {
    await page.goto(`/${adminRoute}/content/file_manager`);

    await expect(page.getByText(READ_ONLY_NOTICE)).toBeVisible();
    await expect(page.getByText('bin/console eccube:user-data:put')).toBeVisible();
    for (const selector of ['a.action-upload', 'a.action-create']) {
      await expect(page.locator(selector)).toHaveClass(/disabled/);
      await expect(page.locator(selector)).toHaveAttribute('aria-disabled', 'true');
    }
  });

  test('メールテンプレートは内容を表示したまま登録だけ無効化される', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/shop/mail`);

    await expect(page.getByText(READ_ONLY_NOTICE)).toBeVisible();
    await expect(page.getByText('bin/console eccube:mail-template:apply')).toBeVisible();
    await expect(page.locator('#mail_template')).toBeVisible();
    await expect(page.locator('button.btn-ec-conversion[type="submit"]')).toBeDisabled();
  });

  test('プラグイン管理は一覧を表示したまま操作だけ無効化される', async ({ page }) => {
    await page.goto(`/${adminRoute}/store/plugin`);

    await expect(page.getByText(READ_ONLY_NOTICE)).toBeVisible();
    await expect(page.getByText('bin/console eccube:plugin:enable')).toBeVisible();
  });

  test('CLI で導入したプラグインが管理画面へ反映される', async ({ page }) => {
    await page.goto(`/${adminRoute}/store/plugin`);

    // eccube:plugin:install --path=... → eccube:plugin:enable --code=... で導入済み。
    // 管理画面からのプラグイン操作が塞がれていても CLI が代替導線として成立していること
    const row = page.locator('tr').filter({ hasText: CLI_PLUGIN_CODE }).first();
    await expect(row).toContainText('有効');
    // 有効なプラグインの行には無効化リンクが出る。 それが押せないこと
    const disableLink = row.locator('a[href*="/disable"]');
    await expect(disableLink).toHaveClass(/disabled/);
    await expect(disableLink).toHaveAttribute('aria-disabled', 'true');
  });

  test('制限中でもナビゲーションから対象画面へ辿れる', async ({ page }) => {
    // 辿れないと「内容は表示する」が成立しないため、 Phase 4 でメニューの非表示化をやめた
    await page.goto(`/${adminRoute}/`);

    const nav = page.locator('.c-mainNavArea');
    for (const path of ['content/css', 'content/js', 'content/file_manager']) {
      await expect(nav.locator(`a[href$="/${adminRoute}/${path}"]`)).toHaveCount(1);
    }
  });

  test('書き込みリクエストは UI を経由しなくても 403 になる', async ({ page }) => {
    // page.request はブラウザと同じコンテキスト (ログイン済みのセッション) を使う。
    // RestrictFileUploadListener は kernel.request で判定するため、 フォームを組み立てず
    // 直接 POST しても 403 になる
    await page.goto(`/${adminRoute}/`);

    const css = await page.request.post(`/${adminRoute}/content/css`, { form: {} });
    expect(css.status()).toBe(403);

    const mail = await page.request.post(`/${adminRoute}/setting/shop/mail`, { form: {} });
    expect(mail.status()).toBe(403);

    // ファイル管理はディレクトリ移動も POST のため、 FileController が create / upload だけを拒否する
    const create = await page.request.post(`/${adminRoute}/content/file_manager`, {
      form: { mode: 'create' },
    });
    expect(create.status()).toBe(403);

    const move = await page.request.post(`/${adminRoute}/content/file_manager`, {
      form: { mode: 'move' },
    });
    expect(move.status()).not.toBe(403);
  });

  test('安全なメソッドは通る', async ({ page }) => {
    // 書き込みを伴うメソッドだけを 403 にする (GET まで塞ぐと内容が読めなくなる)
    const response = await page.request.get(`/${adminRoute}/content/css`);
    expect(response.status()).toBe(200);
  });
});
