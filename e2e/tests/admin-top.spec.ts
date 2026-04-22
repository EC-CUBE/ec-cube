import { test, expect } from '@playwright/test';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
const pageTitle = '.c-pageTitle h2.c-pageTitle__title';

test.describe('Admin Top (EA01)', () => {
  test('top_001 TOPページ 初期表示', async ({ page }) => {
    await page.goto(`/${adminRoute}/`);

    // TOP画面に各セクションが表示されている
    await expect(page.locator('#order-status .card-body .d-block:nth-child(1) a')).toContainText('新規受付');
    await expect(page.locator('#ec-cube-news .card-header .card-title')).toContainText('お知らせ');
    await expect(page.locator('#chart-statistics .card-header .card-title')).toContainText('売上状況');
    await expect(page.locator('#shop-statistical .card-header .card-title')).toContainText('ショップ状況');
    await expect(page.locator('#ec-cube-plugin .card-header .card-title')).toContainText('おすすめのプラグイン');
  });

  test('top_001 新規受付リンクから受注一覧へ遷移', async ({ page }) => {
    await page.goto(`/${adminRoute}/`);
    await page.locator('#order-status .card-body .d-block:nth-child(1) a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('受注一覧');
    await expect(page.locator('#admin_search_order_status_1')).toBeChecked();
    await expect(page.locator('#admin_search_order_status_6')).not.toBeChecked();
    await expect(page.locator('#admin_search_order_status_4')).not.toBeChecked();
  });

  test('top_001 入金済みリンクから受注一覧へ遷移', async ({ page }) => {
    await page.goto(`/${adminRoute}/`);
    await page.locator('#order-status .card-body .d-block:nth-child(2) a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('受注一覧');
    await expect(page.locator('#admin_search_order_status_6')).toBeChecked();
    await expect(page.locator('#admin_search_order_status_1')).not.toBeChecked();
    await expect(page.locator('#admin_search_order_status_4')).not.toBeChecked();
  });

  test('top_001 対応中リンクから受注一覧へ遷移', async ({ page }) => {
    await page.goto(`/${adminRoute}/`);
    await page.locator('#order-status .card-body .d-block:nth-child(3) a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('受注一覧');
    await expect(page.locator('#admin_search_order_status_4')).toBeChecked();
    await expect(page.locator('#admin_search_order_status_1')).not.toBeChecked();
    await expect(page.locator('#admin_search_order_status_6')).not.toBeChecked();
  });

  test('top_001 在庫切れ商品リンクから商品一覧へ遷移', async ({ page }) => {
    await page.goto(`/${adminRoute}/`);
    await page.locator('#shop-statistical > div.card-body.p-0 > div:nth-child(1) > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品一覧');
    await expect(page.locator('#admin_search_product_stock_1')).toBeChecked();
    await expect(page.locator('#admin_search_product_stock_0')).not.toBeChecked();
  });

  test('top_001 取扱商品数リンクから商品一覧へ遷移', async ({ page }) => {
    await page.goto(`/${adminRoute}/`);
    await page.locator('#shop-statistical > div.card-body.p-0 > div:nth-child(2) > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品一覧');
  });

  test('top_001 会員数リンクから会員一覧へ遷移', async ({ page }) => {
    await page.goto(`/${adminRoute}/`);
    await page.locator('#shop-statistical > div.card-body.p-0 > div:nth-child(3) > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('会員一覧');
    await expect(page.locator('#admin_search_customer_customer_status_2')).toBeChecked();
    await expect(page.locator('#admin_search_customer_customer_status_1')).not.toBeChecked();
    await expect(page.locator('#admin_search_customer_customer_status_3')).not.toBeChecked();
  });

  test('top_001 お知らせのiframe内リンクが遷移する', async ({ page }) => {
    await page.goto(`/${adminRoute}/`);

    const informationFrame = page.frameLocator('iframe[name="information"]');
    const link = informationFrame.locator('.news_area .link_list .tableish a').first();
    await expect(link).toBeVisible({ timeout: 30_000 });

    const [newPage] = await Promise.all([
      page.context().waitForEvent('page'),
      link.click(),
    ]);
    await newPage.waitForLoadState();
    expect(newPage.url()).not.toBe('about:blank');
    await newPage.close();
  });

  test('top_001_11 プラグインモーダル表示', async ({ page }) => {
    await page.goto(`/${adminRoute}/`);

    // おすすめプラグインは外部APIから取得するため、表示されない場合はスキップ
    const pluginLink = page.locator('#ec-cube-plugin a[data-bs-toggle="modal"]').first();
    try {
      await pluginLink.waitFor({ timeout: 10_000 });
    } catch {
      test.skip(true, 'おすすめプラグインが読み込まれていない');
    }
    await pluginLink.click();
    await page.waitForTimeout(3000);
    await expect(page.locator('#ec-cube-plugin .modal.show')).toBeVisible();
  });
});
