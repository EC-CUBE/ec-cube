import { test, expect } from '../fixtures/plugin-test';
import { HorizonStore } from '../models/plugins/horizon-store';
import { HorizonLocal } from '../models/plugins/horizon-local';
import { emptyDir } from '../helpers/tar-helper';

test.describe('Plugin Update', () => {
  test.beforeEach(async ({ config }) => {
    emptyDir(config.reposDir);
  });

  test('test_install_update_remove_store', async ({ page, db, config }) => {
    const horizon = await HorizonStore.start(page, db, config);
    await horizon.インストール();
    await horizon.アップデート();
    await horizon.削除();
  });

  test('test_install_update_remove_local', async ({ page, db, config }) => {
    const horizon = await HorizonLocal.start(page, db, config);
    await horizon.インストール();
    await horizon.アップデート();
    await horizon.削除();
  });

  test('test_install_enable_disable_update_enable_disable_remove_local', async ({ page, db, config }) => {
    const horizon = await HorizonLocal.start(page, db, config);
    await horizon.インストール();
    await horizon.有効化();
    await horizon.無効化();
    await horizon.アップデート();
    await horizon.有効化();
    await horizon.無効化();
    await horizon.削除();
  });

  test('test_install_enable_disable_update_enable_disable_remove_store', async ({ page, db, config }) => {
    const horizon = await HorizonStore.start(page, db, config);
    await horizon.インストール();
    await horizon.有効化();
    await horizon.無効化();
    await horizon.アップデート();
    await horizon.有効化();
    await horizon.無効化();
    await horizon.削除();
  });

  test('test_install_enable_update_disable_remove_store', async ({ page, db, config }) => {
    const horizon = await HorizonStore.start(page, db, config);
    await horizon.インストール();
    await horizon.有効化();
    await horizon.アップデート();
    await horizon.削除();
  });

  test('test_install_enable_update_disable_remove_local', async ({ page, db, config }) => {
    const horizon = await HorizonLocal.start(page, db, config);
    await horizon.インストール();
    await horizon.有効化();
    await horizon.アップデート();
    await horizon.無効化();
    await horizon.削除();
  });

  test('test_install_update_enable_disable_remove_store', async ({ page, db, config }) => {
    const horizon = await HorizonStore.start(page, db, config);
    await horizon.インストール();
    await horizon.アップデート();
    await horizon.有効化();
    await horizon.無効化();
    await horizon.削除();
  });

  test('test_install_update_enable_disable_remove_local', async ({ page, db, config }) => {
    const horizon = await HorizonLocal.start(page, db, config);
    await horizon.インストール();
    await horizon.アップデート();
    await horizon.有効化();
    await horizon.無効化();
    await horizon.削除();
  });
});
