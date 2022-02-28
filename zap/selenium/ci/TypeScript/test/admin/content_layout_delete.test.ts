import { test, expect, chromium, Page, Locator } from '@playwright/test';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, Mode, ContextType, Risk, HttpMessage } from '../../utils/ZapClient';
const zapClient = new ZapClient('http://127.0.0.1:8090');

const baseURL = 'https://ec-cube/admin';
let url = null

test.describe.serial('レイアウト管理>削除のテスト', () => {
  let page: Page;
  let deleteButton: Locator;

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

    // レイアウトの新規作成
    const layoutNmae = Math.random().toString(36).slice(2)
    await page.goto(`${baseURL}/content/layout/new`)
    await page.fill('#admin_layout_name', layoutNmae)
    await Promise.all([
      page.waitForNavigation(),
      page.click('.c-conversionArea >> text=登録')
    ])
    await expect(page.locator('.c-contentsArea .alert')).toContainText('保存しました')

    // 削除ボタンと削除URLの取得
    await page.goto(`${baseURL}/content/layout`)
    deleteButton = page.locator(`.c-contentsArea .card:has-text("${layoutNmae}")`).locator('button')
    const layoutId = (await deleteButton.getAttribute('data-url')).replace(/^.*\/(\d+)\/.*$/, '$1')
    url = baseURL + `/content/layout/${layoutId}/delete`;
  });

  test('レイアウトを削除します', async () => {
    await deleteButton.click()
    await Promise.all([
      page.waitForNavigation(),
      page.click('.btn-ec-delete')
    ])
    await expect(page.locator('.c-contentsArea .alert')).toContainText('削除しました')
  });

  test.describe('テストを実行します[POST] @attack', () => {
    let message: HttpMessage;

    test('HttpMessage を取得します', async () => {
      const messages = await zapClient.getMessages(url, await zapClient.getNumberOfMessages(url), 1);
      message = messages.pop();
      expect(message.requestHeader).toContain(`POST ${url}`)
      expect(message.responseHeader).toContain('HTTP/1.1 302 Found');
    });

    let scanId: number;
    test('アクティブスキャンを実行します', async () => {
      scanId = await zapClient.activeScanAsUser(url, 2, 55, false, null, 'POST', message.requestBody);
      await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000, page);
    });

    test('結果を確認します', async () => {
      await zapClient.getAlerts(url, 0, 1, Risk.High)
        .then(alerts => expect(alerts).toEqual([]));
    });
  });
});
