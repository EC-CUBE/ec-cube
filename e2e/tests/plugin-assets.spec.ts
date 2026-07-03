import { test, expect } from '../fixtures/plugin-test';
import { fileExists } from '../helpers/file-helper';
import { compressPlugin } from '../helpers/tar-helper';
import { PluginLocalInstallPage } from '../pages/plugin-local-install.page';
import { PluginSearchPage } from '../pages/plugin-search.page';
import { PluginManagePage } from '../pages/plugin-manage.page';
import { emptyDir } from '../helpers/tar-helper';
import path from 'path';

test.describe('Plugin Assets', () => {
  test.beforeEach(async ({ config }) => {
    emptyDir(config.reposDir);
  });

  test('test_install_assets_local', async ({ page, db, config }) => {
    // plugin_html_realdir は通常 html/plugin/
    const pluginHtmlDir = path.join(config.projectDir, 'html', 'plugin');
    const assetsPath = path.join(pluginHtmlDir, 'Assets', 'assets', 'assets.js');
    const updatedPath = path.join(pluginHtmlDir, 'Assets', 'assets', 'updated.js');

    expect(fileExists(assetsPath), 'assets.js は存在しない').toBe(false);
    expect(fileExists(updatedPath), 'updated.js は存在しない').toBe(false);

    // インストール
    await compressPlugin('Assets-1.0.0', config.pluginDataDir, config.pluginDataDir);
    const installPage = await PluginLocalInstallPage.go(page, config.adminRoute);
    const managePage = await installPage.アップロード(
      path.join(config.pluginDataDir, 'Assets-1.0.0.tgz')
    );
    expect(fileExists(assetsPath), 'インストール後 assets.js がある').toBe(true);
    expect(fileExists(updatedPath), 'インストール後 updated.js はない').toBe(false);

    // 有効化
    await managePage.独自プラグイン_有効化('Assets');
    expect(fileExists(assetsPath), '有効化後 assets.js がある').toBe(true);
    expect(fileExists(updatedPath), '有効化後 updated.js はない').toBe(false);

    // 無効化
    await managePage.独自プラグイン_無効化('Assets');
    expect(fileExists(assetsPath), '無効化後 assets.js がある').toBe(true);
    expect(fileExists(updatedPath), '無効化後 updated.js はない').toBe(false);

    // アップデート
    await compressPlugin('Assets-1.0.1', config.pluginDataDir, config.pluginDataDir);
    await managePage.独自プラグイン_アップデート(
      'Assets',
      path.join(config.pluginDataDir, 'Assets-1.0.1.tgz')
    );
    expect(fileExists(assetsPath), 'アップデート後 assets.js がある').toBe(true);
    expect(fileExists(updatedPath), 'アップデート後 updated.js がある').toBe(true);

    // 削除
    await managePage.独自プラグイン_削除('Assets');
    expect(fileExists(assetsPath), '削除後 assets.js がない').toBe(false);
    expect(fileExists(updatedPath), '削除後 updated.js がない').toBe(false);
  });

  test('test_install_assets_store', async ({ page, db, config }) => {
    // 最初のバージョンを作成
    await compressPlugin('Assets-1.0.0', config.reposDir, config.pluginDataDir);

    const pluginHtmlDir = path.join(config.projectDir, 'html', 'plugin');
    const assetsPath = path.join(pluginHtmlDir, 'Assets', 'assets', 'assets.js');
    const updatedPath = path.join(pluginHtmlDir, 'Assets', 'assets', 'updated.js');

    expect(fileExists(assetsPath), 'assets.js は存在しない').toBe(false);
    expect(fileExists(updatedPath), 'updated.js は存在しない').toBe(false);

    // ストアからインストール
    const searchPage = await PluginSearchPage.go(page, config.adminRoute);
    const confirmPage = await searchPage.入手する('Assets');
    const managePage = await confirmPage.インストール();

    expect(fileExists(assetsPath), 'インストール後 assets.js はない').toBe(false);
    expect(fileExists(updatedPath), 'インストール後 updated.js はない').toBe(false);

    // 有効化
    await managePage.ストアプラグイン_有効化('Assets');
    expect(fileExists(assetsPath), '有効化後 assets.js がある').toBe(true);
    expect(fileExists(updatedPath), '有効化後 updated.js はない').toBe(false);

    // 無効化
    await managePage.ストアプラグイン_無効化('Assets');
    expect(fileExists(assetsPath), '無効化後 assets.js がある').toBe(true);
    expect(fileExists(updatedPath), '無効化後 updated.js はない').toBe(false);

    // 新しいバージョンを作成
    await compressPlugin('Assets-1.0.1', config.reposDir, config.pluginDataDir);

    await page.reload();
    const upgradePage = await managePage.ストアプラグイン_アップデート('Assets');
    await upgradePage.アップデート();
    expect(fileExists(assetsPath), 'アップデート後 assets.js がある').toBe(true);
    expect(fileExists(updatedPath), 'アップデート後 updated.js がある').toBe(true);

    // 削除
    await managePage.ストアプラグイン_削除('Assets');
    expect(fileExists(assetsPath), '削除後 assets.js がない').toBe(false);
    expect(fileExists(updatedPath), '削除後 updated.js がない').toBe(false);
  });
});
