import { test } from '../fixtures/plugin-test';
import { HorizonStore } from '../models/plugins/horizon-store';
import { HorizonLocal } from '../models/plugins/horizon-local';
import { BundleStore } from '../models/plugins/bundle-store';
import { emptyDir } from '../helpers/tar-helper';

test.describe('Plugin Install', () => {
  test.beforeEach(async ({ config }) => {
    emptyDir(config.reposDir);
  });

  test('test_install_enable_disable_remove_store', async ({ page, db, config }) => {
    const horizon = await HorizonStore.start(page, db, config);
    await horizon.インストール();
    await horizon.有効化();
    await horizon.無効化();
    await horizon.削除();
  });

  test('test_install_enable_disable_remove_local', async ({ page, db, config }) => {
    const horizon = await HorizonLocal.start(page, db, config);
    await horizon.インストール();
    await horizon.有効化();
    await horizon.無効化();
    await horizon.削除();
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

  test('test_install_enable_disable_enable_disable_remove_local', async ({ page, db, config }) => {
    const horizon = await HorizonLocal.start(page, db, config);
    await horizon.インストール();
    await horizon.有効化();
    await horizon.無効化();
    await horizon.有効化();
    await horizon.無効化();
    await horizon.削除();
  });

  test('test_install_remove_local', async ({ page, db, config }) => {
    const horizon = await HorizonLocal.start(page, db, config);
    await horizon.インストール();
    await horizon.削除();
  });

  test('test_install_remove_store', async ({ page, db, config }) => {
    const horizon = await HorizonStore.start(page, db, config);
    await horizon.インストール();
    await horizon.削除();
  });

  test('test_bundle_install_enable_disable_remove_store', async ({ page, db, config }) => {
    const bundle = await BundleStore.start(page, db, config);
    await bundle.インストール();
    await bundle.有効化();
    await bundle.無効化();
    await bundle.削除();
  });

  test('test_bundle_install_update_enable_disable_remove_store', async ({ page, db, config }) => {
    const bundle = await BundleStore.start(page, db, config);
    await bundle.インストール();
    await bundle.有効化();
    await bundle.アップデート();
    await bundle.有効化();
    await bundle.無効化();
    await bundle.削除();
  });
});
