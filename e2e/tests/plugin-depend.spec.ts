import { test, expect } from '../fixtures/plugin-test';
import { HorizonStore } from '../models/plugins/horizon-store';
import { EmperorStore } from '../models/plugins/emperor-store';
import { BoomerangStore } from '../models/plugins/boomerang-store';
import { Boomerang10Store } from '../models/plugins/boomerang10-store';
import { PluginSearchPage } from '../pages/plugin-search.page';
import { emptyDir } from '../helpers/tar-helper';
import { compressPlugin } from '../helpers/tar-helper';

test.describe('Plugin Depend', () => {
  test.beforeEach(async ({ config }) => {
    emptyDir(config.reposDir);
  });

  test('test_dependency_each_install_plugin', async ({ page, db, config }) => {
    const horizon = await HorizonStore.start(page, db, config);
    const emperor = await EmperorStore.start(page, db, config);

    await horizon.インストール();
    await horizon.有効化();
    await emperor.インストール();
    await emperor.有効化();
  });

  test('test_dependency_plugin_install', async ({ page, db, config }) => {
    const horizon = await HorizonStore.start(page, db, config);
    const emperor = await EmperorStore.start(page, db, config, horizon);

    await emperor.インストール();
    await emperor.依存より先に有効化();

    await horizon.有効化();
    await emperor.有効化();

    await horizon.依存されているのが有効なのに無効化();
    await emperor.無効化();
    await horizon.無効化();

    await horizon.依存されているのが削除されていないのに削除();
    await emperor.削除();
    await horizon.削除();
  });

  test('test_dependency_plugin_update', async ({ page, db, config }) => {
    const horizon = await HorizonStore.start(page, db, config);
    const emperor = await EmperorStore.start(page, db, config, horizon);

    await emperor.インストール();

    await horizon.検証();
    await horizon.有効化();

    await emperor.有効化();
    await emperor.無効化();
    await emperor.アップデート();

    await horizon.検証();
  });

  test('test_install_error', async ({ page, db, config }) => {
    await compressPlugin('InstallError', config.reposDir, config.pluginDataDir);
    const horizon = await HorizonStore.start(page, db, config);

    const searchPage = await PluginSearchPage.go(page, config.adminRoute);
    const confirmPage = await searchPage.入手する('InstallError');
    await confirmPage.インストール('システムエラーが発生しました。');

    // エラー後に他のプラグインがインストールできる
    await horizon.インストール();
  });

  test('test_install_enable_disable_enable_disable_remove_store', async ({ page, db, config }) => {
    const horizon = await HorizonStore.start(page, db, config);
    await horizon.インストール();
    await horizon.有効化();
    await horizon.無効化();
    await horizon.有効化();
    await horizon.無効化();
    await horizon.削除();
  });

  test('test_enhance_plugin_entity', async ({ page, db, config }) => {
    const boomerang = await BoomerangStore.start(page, db, config);
    await boomerang.インストール();
    await boomerang.有効化();
    await boomerang.カート作成();

    await expect(page.getByText('[1]')).toBeVisible();

    const boomerang10 = await Boomerang10Store.start(page, db, config, boomerang);
    await boomerang10.インストール();
    await boomerang10.有効化();

    await boomerang.カート一覧();
    await expect(page.getByText('[1]')).toBeVisible();
  });
});
