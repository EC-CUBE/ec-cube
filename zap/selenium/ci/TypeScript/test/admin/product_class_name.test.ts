import { test, expect, chromium, Page } from '@playwright/test';
import PlaywrightConfig from '../../playwright.config';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, Mode, ContextType, Risk, HttpMessage } from '../../utils/ZapClient';
import { ECCUBE_ADMIN_ROUTE } from '../../config/default.config';

const zapClient = new ZapClient();

const url = `${PlaywrightConfig.use.baseURL}/${ECCUBE_ADMIN_ROUTE}/product/class_name`;

test.describe.serial('規格管理>商品管理のテストを行います', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.setMode(Mode.Protect);
    await zapClient.newSession('/zap/wrk/sessions/admin_product_class_name', true);
    await zapClient.importContext(ContextType.Admin);

    if (!await zapClient.isForcedUserModeEnabled()) {
      await zapClient.setForcedUserModeEnabled();
      expect(await zapClient.isForcedUserModeEnabled()).toBeTruthy();
    }
    const browser = await chromium.launch();
    page = await browser.newPage();
    await page.goto(url);
  });

  test('規格管理>商品管理ページを表示します', async () => {
    await expect(page).toHaveTitle(/規格管理/);
  });

  test('タイトルを確認します', async () => {
    await page.textContent('.c-pageTitle__subTitle')
      .then(title => expect(title).toContain('商品管理'));
  });

  test.describe('テストを実行します[GET] @attack', () => {
    let scanId: number;
    test('アクティブスキャンを実行します[GET]', async () => {
      scanId = await zapClient.activeScanAsUser(url, 2, 55, false, null, 'GET');
      await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000, page);
    });

    test('結果を確認します[GET]', async () => {
      await zapClient.getAlerts(url, 0, 1, Risk.High)
        .then(alerts => expect(alerts).toEqual([]));
    });
  });

  test('規格を新規作成します', async () => {
    await page.fill('input[name="admin_class_name\\[name\\]"]', '規格名');
    await page.fill('input[name="admin_class_name\\[backend_name\\]"]', '管理名');
    await page.click('button:has-text("新規作成")');
  });

  let message: HttpMessage;
  test('HttpMessage を取得します', async () => {
    const messages = await zapClient.getMessages(url, await zapClient.getNumberOfMessages(url) - 1, 1);
    message = messages.pop();
    expect(message.requestHeader).toContain(`POST ${url}`)
    expect(message.responseHeader).toContain('HTTP/1.1 302 Found');
  });

  let scanId: number;
  test('アクティブスキャンを実行します[POST]', async () => {
    scanId = await zapClient.activeScanAsUser(url, 2, 55, false, null, 'POST', message.requestBody);
    await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000, page);
  });

  test('結果を確認します[POST]', async () => {
    await zapClient.getAlerts(url, 0, 1, Risk.High)
      .then(alerts => expect(alerts).toEqual([]));
  });
});
