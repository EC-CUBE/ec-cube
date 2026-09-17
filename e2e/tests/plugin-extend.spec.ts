import { test, expect } from '../fixtures/plugin-test';
import { HorizonStore } from '../models/plugins/horizon-store';
import { HorizonLocal } from '../models/plugins/horizon-local';
import { BoomerangStore } from '../models/plugins/boomerang-store';
import { BoomerangLocal } from '../models/plugins/boomerang-local';
import { MasterEntityExtensionLocal } from '../models/plugins/master-entity-extension-local';
import { MasterDataManagePage } from '../pages/master-data-manage.page';
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

  // Issue #5400 / #6273: Entity/Master 配下のマスタEntityをtraitで拡張すると、
  // マスタデータ管理ページのプルダウンから拡張したテーブルが消えてしまう回帰の防止。
  test('test_master_entity_extension', async ({ page, db, config }) => {
    const plugin = await MasterEntityExtensionLocal.start(page, db, config);

    await plugin.インストール();
    await plugin.有効化();

    // 拡張したマスタデータ(mtb_device_type)がプルダウンに残っており、選択・保存できること
    const masterPage = await MasterDataManagePage.go(page, config.adminRoute);
    expect(await masterPage.選択肢が存在する('mtb_device_type'), 'mtb_device_type がプルダウンに存在する').toBe(true);
    await masterPage.選択('mtb_device_type');
    await masterPage.保存();

    // 後片付け(無効化・削除)はプラグイン管理画面上で行うため、マスタデータ画面から戻る
    await page.goto(`/${config.adminRoute}/store/plugin`);
    await plugin.無効化();
    await plugin.削除();
  });
});
