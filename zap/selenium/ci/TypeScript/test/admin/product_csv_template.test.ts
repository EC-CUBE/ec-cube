import { test, expect, chromium, Page } from '@playwright/test';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, Mode, ContextType, Risk, HttpMessage } from '../../utils/ZapClient';
const zapClient = new ZapClient('http://127.0.0.1:8090');

const baseURL = 'https://ec-cube/admin';
const url = baseURL + '/product/product_csv_upload';

test.describe.serial('雛形ダウンロードのテストをします', () => {



  let page: Page;
  test.beforeAll(async () => {
    await zapClient.setMode(Mode.Protect);
    await zapClient.newSession('/zap/wrk/sessions/admin_product_csv_upload', true);
    await zapClient.importContext(ContextType.Admin);

    if (!await zapClient.isForcedUserModeEnabled()) {
      await zapClient.setForcedUserModeEnabled();
      expect(await zapClient.isForcedUserModeEnabled()).toBeTruthy();
    }
    const browser = await chromium.launch();
    page = await browser.newPage();
    await page.goto(url);
  });

  test('商品CSV登録を表示します', async () => {
    await expect(page).toHaveTitle(/商品CSV登録/);
  });

  test('商品CSV登録ページのタイトルを確認します', async () => {
    await page.textContent('.c-pageTitle__title')
      .then(title => expect(title).toContain('商品CSV登録'));
  });

  test.describe('商品CSV登録ページのテストを実行します[GET] @attack', () => {
    let scanId: number;
    test('商品CSV登録ページのアクティブスキャンを実行します', async () => {
      scanId = await zapClient.activeScanAsUser(url, 2, 55, false, null, 'GET');
      await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000, page);
    });

    test('商品CSV登録ページの結果を確認します', async () => {
      await zapClient.getAlerts(url, 0, 1, Risk.High)
        .then(alerts => expect(alerts).toEqual([]));
    });
  });

  test('プロダクト用雛形テンプレートをダウンロードします。', async () => {
    const productURL = baseURL + '/product/csv_template/product';
    await page.goto(url);
  });

  test.describe('プロダクト用雛形テンプレートのダウンロード時のテストを実行します[GET] @attack', () => {
    let scanId: number;
    test('プロダクト用雛形テンプレートのダウンロード時のアクティブスキャンを実行します', async () => {
      scanId = await zapClient.activeScanAsUser(url, 2, 55, false, null, 'GET');
      await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000, page);
    });

    test('プロダクト用雛形テンプレートのダウンロード時の結果を確認します', async () => {
      await zapClient.getAlerts(url, 0, 1, Risk.High)
        .then(alerts => expect(alerts).toEqual([]));
    });
  });
});
