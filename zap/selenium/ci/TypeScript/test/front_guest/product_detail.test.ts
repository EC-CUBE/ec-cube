import { test, expect, chromium, Page } from '@playwright/test';
import PlaywrightConfig from '../../playwright.config';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, ContextType, Risk } from '../../utils/ZapClient';
const zapClient = new ZapClient();

const url = `${PlaywrightConfig.use.baseURL}/products/detail/2`;

test.describe.serial('商品詳細画面のテストをします', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.startSession(ContextType.FrontGuest, 'front_guest_product_detail');

    const browser = await chromium.launch();
    page = await browser.newPage();
    await page.goto(url);
  });

  test('商品詳細画面を表示します', async () => {
    await expect(page).toHaveTitle(/チェリーアイスサンド/);
  });

  test('タイトルを確認します', async () => {
    await expect(page.locator('.ec-headingTitle')).toContainText('チェリーアイスサンド');
  });

  test.describe('テストを実行します[GET] @attack', () => {
    let scanId: number;
    test('アクティブスキャンを実行します', async () => {
      scanId = await zapClient.activeScan(url, false, true, null, 'GET', null);
      await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000, page);
    });

    test('結果を確認します', async () => {
      await zapClient.getAlerts(url, 0, 1, Risk.High)
        .then(alerts => expect(alerts).toEqual([]));
    });
  });
});
