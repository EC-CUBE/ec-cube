import { test, expect, chromium, Page } from '@playwright/test';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, Mode, ContextType, Risk, HttpMessage } from '../../utils/ZapClient';
const zapClient = new ZapClient('http://127.0.0.1:8090');

const baseURL = 'https://ec-cube/admin';
const url = baseURL + '/content/block';

test.describe.serial('コンテンツ管理>ブロック管理のテストをします', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.setMode(Mode.Protect);
    await zapClient.newSession('/zap/wrk/sessions/admin_index', true);
    await zapClient.importContext(ContextType.Admin);

    // 管理画面ログイン
    if (!await zapClient.isForcedUserModeEnabled()) {
      await zapClient.setForcedUserModeEnabled();
      expect(await zapClient.isForcedUserModeEnabled()).toBeTruthy();
    }
    // chroniumを立ち上げ、URLを開く
    const browser = await chromium.launch();
    page = await browser.newPage();
    await page.goto(url);
  });

  test('コンテンツ管理>ブロック管理を表示します', async () => {
    await expect(page).toHaveTitle(/ブロック管理/);
  });

  test('タイトルを確認します', async () => {
    await page.textContent('.c-pageTitle__title')
      .then(title => expect(title).toContain('ブロック管理'));
  });

  // アクティブスキャンを実行します
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
