import { test, expect, chromium, Page } from '@playwright/test';
import PlaywrightConfig from '../../playwright.config';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, ContextType, Risk } from '../../utils/ZapClient';
import { ECCUBE_ADMIN_ROUTE } from '../../config/default.config';

const zapClient = new ZapClient();

const url = `${PlaywrightConfig.use.baseURL}/${ECCUBE_ADMIN_ROUTE}/content/layout`;

test.describe.serial('レイアウト管理>表示テスト', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.startSession(ContextType.Admin, 'admin_content_layout')
      .then(async () => expect(await zapClient.isForcedUserModeEnabled()).toBeTruthy());

    const browser = await chromium.launch();
    page = await browser.newPage();
    await page.goto(url);
  });

  test('レイアウト管理ページを表示します', async () => {
    await expect(page).toHaveTitle(/レイアウト管理/);
  });

  test('タイトルを確認します', async () => {
    await page.textContent('.c-pageTitle__title')
      .then(title => expect(title).toContain('レイアウト管理'));
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
});
