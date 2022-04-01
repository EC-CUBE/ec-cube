import { test, expect, chromium, Page } from '@playwright/test';
import PlaywrightConfig from '../../playwright.config';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, ContextType, Risk, HttpMessage } from '../../utils/ZapClient';
import { ECCUBE_ADMIN_ROUTE } from '../../config/default.config';

const zapClient = new ZapClient();

const url = `${PlaywrightConfig.use.baseURL}/${ECCUBE_ADMIN_ROUTE}/product/category`;

test.describe.serial('カテゴリ管理>商品管理からCSVダウンロードのテストをします', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.startSession(ContextType.Admin, 'admin_product_category_export')
      .then(async () => expect(await zapClient.isForcedUserModeEnabled()).toBeTruthy());

    const browser = await chromium.launch();
    page = await browser.newPage();
    await page.goto(url);
  });

  test('カテゴリ管理>商品管理ページを表示します', async () => {
    await expect(page).toHaveTitle(/商品管理/);
  });

  test('タイトルを確認します', async () => {
    await page.textContent('.c-pageTitle__title')
      .then(title => expect(title).toContain('カテゴリ管理'));
  });

  test.describe('テストを実行します[GET] @attack', () => {
    let scanId: number;
    test('アクティブスキャンを実行します', async () => {
      scanId = await zapClient.activeScanAsUser(url, 2, 55, false, null, 'GET');
      await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000, page);
    });

    test('結果を確認します', async () => {
      await zapClient.getAlerts(url, 0, 1, Risk.High)
        .then(alerts => expect(alerts).toEqual([]));
    });
  });

  test('CSVファイルのダウンロードします', async () => {
    const [download] = await Promise.all([
        page.waitForEvent('download'),
        page.click('text=CSVダウンロード')
    ]);
  });

  const csvExportURL = `${PlaywrightConfig.use.baseURL}/${ECCUBE_ADMIN_ROUTE}/product/category/export`;
  test.describe('プロダクト用雛形テンプレートのダウンロード時のテストを実行します[GET] @attack', () => {
    let scanId: number;
    test('プロダクト用雛形テンプレートのダウンロード時のアクティブスキャンを実行します', async () => {
      scanId = await zapClient.activeScanAsUser(csvExportURL, 2, 55, false, null, 'GET');
      await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000, page);
    });

    test('プロダクト用雛形テンプレートのダウンロード時の結果を確認します', async () => {
      await zapClient.getAlerts(url, 0, 1, Risk.High)
        .then(alerts => expect(alerts).toEqual([]));
    });
  });
});
