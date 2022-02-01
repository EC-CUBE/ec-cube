import { test, expect, chromium, Page } from '@playwright/test';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, Mode, ContextType, Risk, HttpMessage } from '../../utils/ZapClient';
const zapClient = new ZapClient('http://127.0.0.1:8090');

const baseURL = 'https://ec-cube';
const url = baseURL + '/products/detail/2';

test.describe.serial('商品詳細画面のテストをします', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.setMode(Mode.Protect);
    await zapClient.newSession('/zap/wrk/sessions/front_guest_contact', true);
    await zapClient.importContext(ContextType.FrontGuest);
    const browser = await chromium.launch();
    page = await browser.newPage();
    await page.goto(url);
  });

  test('商品詳細画面を表示します', async () => {
    await expect(page).toHaveTitle(/チェリーアイスサンド/);
  });

  test('タイトルを確認します', async () => {
    await page.textContent('.ec-headingTitle')
      .then(title => expect(title).toContain('チェリーアイスサンド'));
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
