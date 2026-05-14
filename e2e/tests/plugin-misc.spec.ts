import { test, expect } from '../fixtures/plugin-test';
import { HorizonStore } from '../models/plugins/horizon-store';
import { LocalPlugin } from '../models/local-plugin';
import { CacheManagePage } from '../pages/cache-manage.page';
import { PluginManagePage } from '../pages/plugin-manage.page';
import { emptyDir } from '../helpers/tar-helper';
import fs from 'fs';
import path from 'path';

/**
 * 前回テスト失敗時に残ったHorizonプラグインを削除する。
 * DB レコード + app/Plugin ディレクトリの両方をクリーンアップ。
 */
async function cleanupHorizon(page: import('@playwright/test').Page, db: import('../helpers/db-client').DbClient, config: import('../fixtures/plugin-test').PluginTestConfig) {
  const plugin = await db.getPlugin('Horizon');
  if (plugin) {
    await page.goto(`/${config.adminRoute}/store/plugin`);
    await page.waitForLoadState('load');
    const managePage = await PluginManagePage.at(page);

    if (plugin.enabled) {
      await managePage.ストアプラグイン_無効化('Horizon');
    }
    await managePage.ストアプラグイン_削除('Horizon');
  }

  // app/Plugin/Horizon ディレクトリが残っている場合も削除
  const pluginDir = path.join(config.projectDir, 'app', 'Plugin', 'Horizon');
  if (fs.existsSync(pluginDir)) {
    fs.rmSync(pluginDir, { recursive: true, force: true });
  }
}

test.describe('Plugin Misc', () => {
  test.beforeEach(async ({ config }) => {
    emptyDir(config.reposDir);
  });

  /**
   * @see https://github.com/EC-CUBE/ec-cube/pull/4527
   */
  test('test_template_overwrite', async ({ page, db, config }) => {
    const plugin = new LocalPlugin(page, db, config, 'Template');
    await plugin.インストール();
    await plugin.有効化();

    // テンプレートの確認
    await page.goto('/template');
    await expect(page.getByText('hello')).toBeVisible();

    // テンプレートを app/template/plugin/[Plugin Code] に設置
    const themeDir = path.join(config.projectDir, 'app', 'template', 'plugin', 'Template');
    fs.mkdirSync(themeDir, { recursive: true });
    fs.writeFileSync(path.join(themeDir, 'index.twig'), 'bye');

    // キャッシュ削除すると反映される
    const cachePage = await CacheManagePage.go(page, config.adminRoute);
    await cachePage.キャッシュ削除();

    // 上書きされていることを確認
    await page.goto('/template');
    await expect(page.getByText('bye')).toBeVisible();

    // クリーンアップ
    await page.goto(`/${config.adminRoute}/store/plugin`);
    await plugin.無効化();
    await plugin.削除();

    fs.rmSync(themeDir, { recursive: true, force: true });
  });

  // マルチタブでの有効化/無効化競合テスト
  // ヘッドレスChromiumではバックグラウンドタブのDOMが更新されるため不安定
  // TODO: Playwright のマルチタブ対応改善後に有効化
  test.fixme('test_install_enable_enable', async ({ page, db, config }) => {
    // 前回テスト失敗時の残りプラグインを削除
    await cleanupHorizon(page, db, config);

    const horizon = await HorizonStore.start(page, db, config);
    await horizon.インストール();

    // 新しいタブで開く
    const originalPage = await horizon.新しいタブで開く();
    await horizon.有効化();

    // 元のタブに戻る
    await horizon.タブを切り替え(originalPage);
    await horizon.既に有効なものを有効化();

    // クリーンアップ
    await horizon.無効化();
    await horizon.削除();
  });

  test.fixme('test_install_disable_disable', async ({ page, db, config }) => {
    // 前回テスト失敗時の残りプラグインを削除
    await cleanupHorizon(page, db, config);

    const horizon = await HorizonStore.start(page, db, config);
    await horizon.インストール();
    await horizon.有効化();

    // 新しいタブで開く
    const originalPage = await horizon.新しいタブで開く();
    await horizon.無効化();

    // 元のタブに戻る
    await horizon.タブを切り替え(originalPage);
    await horizon.既に無効なものを無効化();

    // クリーンアップ
    await horizon.削除();
  });

  test('test_install_enable_disable_enable_disable_remove_local_ファイルアップロード制限', async ({ page, config }) => {
    // 環境変数 ECCUBE_RESTRICT_FILE_UPLOAD=1 の場合のテスト
    // この環境変数はPHPサーバー側で設定される
    await page.goto(`/${config.adminRoute}/store/plugin/install`);

    // 制限がかかっている場合は制限メッセージが表示される
    // 制限がない場合はスキップ
    const restrictedText = page.getByText('この機能は管理者によって制限されています。');
    const isRestricted = await restrictedText.isVisible({ timeout: 5000 }).catch(() => false);
    if (!isRestricted) {
      test.skip(true, 'ECCUBE_RESTRICT_FILE_UPLOAD=0 のためスキップします');
    }
    await expect(restrictedText).toBeVisible();
  });
});
