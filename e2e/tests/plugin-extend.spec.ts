import { test, expect } from '../fixtures/plugin-test';
import { HorizonStore } from '../models/plugins/horizon-store';
import { HorizonLocal } from '../models/plugins/horizon-local';
import { BoomerangStore } from '../models/plugins/boomerang-store';
import { BoomerangLocal } from '../models/plugins/boomerang-local';
import { emptyDir } from '../helpers/tar-helper';

test.describe('Plugin Extend', () => {
  test.beforeEach(async ({ config }) => {
    emptyDir(config.reposDir);
  });

  test('test_extend_same_table_store', async ({ page, db, config }) => {
    const horizon = await HorizonStore.start(page, db, config);
    const boomerang = await BoomerangStore.start(page, db, config);

    await horizon.インストール();
    await horizon.有効化();
    await boomerang.インストール();
    await boomerang.有効化();

    await horizon.検証();
    await horizon.無効化();
    await horizon.削除();
    await boomerang.検証();
    await boomerang.無効化();
    await boomerang.削除();
  });

  test('test_extend_same_table_disabled_remove_store', async ({ page, db, config }) => {
    const horizon = await HorizonStore.start(page, db, config);
    const boomerang = await BoomerangStore.start(page, db, config);

    await horizon.インストール();
    await horizon.有効化();
    await horizon.無効化();
    await boomerang.インストール();
    await boomerang.有効化();
    await boomerang.無効化();

    await horizon.検証();
    await horizon.削除();
    await boomerang.検証();
    await boomerang.削除();
  });

  test('test_extend_same_table_local', async ({ page, db, config }) => {
    const horizon = await HorizonLocal.start(page, db, config);
    const boomerang = await BoomerangLocal.start(page, db, config);

    await horizon.インストール();
    await horizon.有効化();
    await boomerang.インストール();
    await boomerang.有効化();

    await horizon.検証();
    await horizon.無効化();
    await horizon.削除();
    await boomerang.検証();
    await boomerang.無効化();
    await boomerang.削除();
  });

  test('test_extend_same_table_disabled_remove_local', async ({ page, db, config }) => {
    const horizon = await HorizonLocal.start(page, db, config);
    const boomerang = await BoomerangLocal.start(page, db, config);

    await horizon.インストール();
    await horizon.有効化();
    await horizon.無効化();
    await boomerang.インストール();
    await boomerang.有効化();
    await boomerang.無効化();

    await horizon.検証();
    await horizon.削除();
    await boomerang.検証();
    await boomerang.削除();
  });

  test('test_extend_same_table_crossed_store', async ({ page, db, config }) => {
    const horizon = await HorizonStore.start(page, db, config);
    const boomerang = await BoomerangStore.start(page, db, config);

    await horizon.インストール();
    await horizon.有効化();
    await horizon.無効化();
    await boomerang.インストール();
    await boomerang.有効化();

    await horizon.検証();
    await horizon.削除();
    await boomerang.検証();
    await boomerang.無効化();
    await boomerang.削除();
  });

  test('test_extend_same_table_crossed_local', async ({ page, db, config }) => {
    const horizon = await HorizonLocal.start(page, db, config);
    const boomerang = await BoomerangLocal.start(page, db, config);

    await horizon.インストール();
    await horizon.有効化();
    await horizon.無効化();
    await boomerang.インストール();
    await boomerang.有効化();

    await horizon.検証();
    await horizon.削除();
    await boomerang.検証();
    await boomerang.無効化();
    await boomerang.削除();
  });
});
