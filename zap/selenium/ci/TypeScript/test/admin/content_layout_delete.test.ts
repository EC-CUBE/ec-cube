import { test, expect, chromium, Page } from '@playwright/test';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, Mode, ContextType, Risk, HttpMessage } from '../../utils/ZapClient';
const zapClient = new ZapClient('http://127.0.0.1:8090');

const baseURL = 'https://ec-cube/admin';
const url = baseURL + '/content/layout';

test.describe.serial('レイアウト管理>削除のテスト', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.setMode(Mode.Protect);
    await zapClient.newSession('/zap/wrk/sessions/admin_content_layout', true);
    await zapClient.importContext(ContextType.Admin);

    if (!await zapClient.isForcedUserModeEnabled()) {
      await zapClient.setForcedUserModeEnabled();
      expect(await zapClient.isForcedUserModeEnabled()).toBeTruthy();
    }
    const browser = await chromium.launch();
    page = await browser.newPage();
    await page.goto(url);
  });

  test('レイアウト管理ページを表示します', async () => {
    await expect(page).toHaveTitle(/レイアウト管理/);
  });

  test('レイアウト新規作成ページに遷移します', async () => {
    await Promise.all([
      page.waitForNavigation(),
      page.click('a:has-text("新規作成")')
    ])
    await expect(page).toHaveURL(/content\/layout\/new$/)
  });

  test('レイアウトを新規作成します', async () => {
    await page.fill('#admin_layout_name', 'dummy-layout')
    await Promise.all([
      page.waitForNavigation(),
      page.click('.c-conversionArea .btn-ec-conversion')
    ])
    await expect(page.locator('.c-contentsArea .alert')).toContainText('保存しました')
  });

  test('レイアウト管理ページに戻ります', async () => {
    await page.goto(url);
    await expect(page).toHaveTitle(/レイアウト管理/);
  });

  test('作成したレイアウトを削除します', async () => {
    await page.locator('.c-contentsArea .card:has-text("dummy-layout")').locator('button').click()
    await Promise.all([
      page.waitForNavigation(),
      page.click('.btn-ec-delete')
    ])
    await expect(page.locator('.c-contentsArea .alert')).toContainText('削除しました')
});

  test.describe('テストを実行します[DELETE] @attack', () => {
    let scanId: number;
    test('アクティブスキャンを実行します', async () => {
      scanId = await zapClient.activeScanAsUser(url, 2, 55, false, null, 'DELETE');
      await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000, page);
    });

    test('結果を確認します', async () => {
      await zapClient.getAlerts(url, 0, 1, Risk.High)
        .then(alerts => expect(alerts).toEqual([]));
    });
  });
});
