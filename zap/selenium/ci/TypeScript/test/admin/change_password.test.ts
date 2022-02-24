import { test, expect, chromium, Page } from '@playwright/test';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, Mode, ContextType, Risk, HttpMessage } from '../../utils/ZapClient';
const zapClient = new ZapClient('http://127.0.0.1:8090');

const baseURL = 'https://ec-cube/admin';
const url = baseURL + '/change_password';

// path/to/ec-cube/zap/selenium/ci/TypeScript/patches/0001-Member.patch を当てる必要がある
test.describe.serial('パスワード変更のテストをします', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.setMode(Mode.Protect);
    await zapClient.newSession('/zap/wrk/sessions/admin_change_password', true);
    await zapClient.importContext(ContextType.Admin);

    if (!await zapClient.isForcedUserModeEnabled()) {
      await zapClient.setForcedUserModeEnabled();
      expect(await zapClient.isForcedUserModeEnabled()).toBeTruthy();
    }
    const browser = await chromium.launch();
    page = await browser.newPage();
    await page.goto(url);
  });

  test('パスワード変更ページを表示します', async () => {
    await expect(page).toHaveTitle(/パスワード変更/);
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

  const changedPassword = 'zHXFl*85.jFib';
  test('パスワードを変更します', async () => {
    await page.reload();
    await page.fill('input[name="admin_change_password[current_password]"]', 'password');
    await page.fill('input[name="admin_change_password[change_password][first]"]', changedPassword);
    await page.fill('input[name="admin_change_password[change_password][second]"]', changedPassword);
    await page.click('#ex-conversion-action >> button >> text=登録');

    await expect(page.locator('.alert-success')).toContainText('パスワードを更新しました');
  });

  test.describe('テストを実行します[POST] @attack', () => {
    let message: HttpMessage;
    test('HttpMessage を取得します', async () => {
      const messages = await zapClient.getMessages(url, await zapClient.getNumberOfMessages(url) - 1, 1);
      message = messages.pop();
      expect(message.requestHeader).toContain('POST https://ec-cube/admin/change_password');
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
