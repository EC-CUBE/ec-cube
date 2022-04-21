import {chromium, expect, Page, test} from "@playwright/test";
import {ContextType, Mode, Risk, ZapClient} from "../../utils/ZapClient";
import {intervalRepeater} from "../../utils/Progress";
const zapClient = new ZapClient('http://127.0.0.1:8090')

const baseURL = 'https://ec-cube/admin';
const url = baseURL + '/product';

test.describe.serial('商品管理＞商品一覧のテストをします', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.setMode(Mode.Protect);
    await zapClient.newSession('/zap/wrk/sessions/admin_product', true);
    await zapClient.importContext(ContextType.Admin);

    if(!await zapClient.isForcedUserModeEnabled()) {
      await zapClient.setForcedUserModeEnabled();
      expect(await zapClient.isForcedUserModeEnabled()).toBeTruthy();
    }

    const browser = await chromium.launch();
    page = await browser.newPage();
    await page.goto(url);
  });

  test('商品管理＞商品一覧を表示します', async () => {
    await expect(page).toHaveTitle(/商品管理 商品一覧/);
  });

  test('タイトルを確認します', async () => {
    await expect(page.locator('.c-pageTitle__titles')).toContainText('商品管理')
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
    })
  });
});
