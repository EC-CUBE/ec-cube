import { test, expect } from '@playwright/test';
import { OrderDisplaySettingPage } from '../pages/order-display-setting.page';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';

const orderHeader = '#search_result thead';

async function goOrderList(page: import('@playwright/test').Page) {
  await page.goto(`/${adminRoute}/order`);
  await page.waitForLoadState('load');
}

// 各テスト後に「全項目表示」へ戻し、後続テスト・他スペックへの影響を排除する
test.afterEach(async ({ page }) => {
  const settingPage = await OrderDisplaySettingPage.go(page, adminRoute);
  await settingPage.全て表示する();
  await settingPage.登録();
});

test.describe('受注一覧 表示項目設定', () => {
  test('項目を非表示にすると受注一覧の列見出しが消える', async ({ page }) => {
    const settingPage = await OrderDisplaySettingPage.go(page, adminRoute);
    await settingPage.列を非表示にする('支払方法');
    await settingPage.登録();

    // 保存後、非表示項目に「支払方法」が含まれること
    const settingAfter = await OrderDisplaySettingPage.go(page, adminRoute);
    expect(await settingAfter.非表示項目の並び()).toContain('支払方法');

    // 受注一覧の見出しから「支払方法」が消えていること
    await goOrderList(page);
    await expect(page.locator(orderHeader)).not.toContainText('支払方法');
  });

  test('チェックボックス列は設定に関わらず常に表示される', async ({ page }) => {
    const settingPage = await OrderDisplaySettingPage.go(page, adminRoute);
    // すべての項目を非表示にする
    for (const label of await settingPage.表示項目の並び()) {
      await settingPage.列を非表示にする(label);
    }
    await settingPage.登録();

    await goOrderList(page);
    // 全選択チェックボックスは常に存在する
    await expect(page.locator('#toggle_check_all')).toBeVisible();
  });

  test('並び順の変更が受注一覧の列順に反映される', async ({ page }) => {
    const settingPage = await OrderDisplaySettingPage.go(page, adminRoute);
    // 「お届け先」を先頭へ移動して保存
    await settingPage.先頭へ移動('お届け先');
    await settingPage.登録();

    const settingAfter = await OrderDisplaySettingPage.go(page, adminRoute);
    const order = await settingAfter.表示項目の並び();
    expect(order[0]).toBe('お届け先');

    // 受注一覧の最初のデータ列見出しが「お届け先（お届け先）」になっていること
    await goOrderList(page);
    const firstDataHeader = page.locator('#search_result thead th').nth(1);
    await expect(firstDataHeader).toContainText('お届け先');
  });

  test('受注検索は表示項目設定の影響を受けない（回帰）', async ({ page }) => {
    const settingPage = await OrderDisplaySettingPage.go(page, adminRoute);
    await settingPage.列を非表示にする('支払方法');
    await settingPage.登録();

    await goOrderList(page);
    // 検索フォームが従来通り利用できること
    await page.locator('#admin_search_order_multi').fill('');
    await page.locator('#search_form #search_submit').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#search_form #search_total_count')).toBeVisible();
  });
});
