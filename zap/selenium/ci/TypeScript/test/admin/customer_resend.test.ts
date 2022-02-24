import { test, expect, chromium, Page } from '@playwright/test';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, Mode, ContextType, Risk, HttpMessage } from '../../utils/ZapClient';
const zapClient = new ZapClient('http://127.0.0.1:8090');

const baseURL = 'https://ec-cube/admin';
const url = baseURL + '/customer/1/resend';

test.describe.serial('管理画面トップページのテストをします', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.setMode(Mode.Protect);
    await zapClient.newSession('/zap/wrk/sessions/admin_customer_resend', true);
    await zapClient.importContext(ContextType.Admin);

    if (!await zapClient.isForcedUserModeEnabled()) {
      await zapClient.setForcedUserModeEnabled();
      expect(await zapClient.isForcedUserModeEnabled()).toBeTruthy();
    }

    const browser = await chromium.launch();
    page = await browser.newPage();
  });

  test('仮会員メールを再送します。', async () =>
  {
    // 会員編集画面から、仮会員へ更新する
    await page.goto(baseURL + '/customer/1/edit');
    await page.locator('//*[@id="admin_customer_status"]').selectOption('1');
    await page.locator('//*[@id="ex-conversion-action"]/div[2]/button').click();
    await expect(page.locator('.alert')).toContainText('保存しました');

    // 会員一覧から、仮会員メールを再送する
    await page.goto(baseURL + '/customer');
    // アイコンをクリック
    await page.click('//*[@id="ex-customer-1"]/td[6]/div/div[1]/a');
    // 送信を実行
    await page.click('//*[@id="discontinuance_cus_1"]/div/div/div[3]/a');
    // 送信完了メッセージを確認
    await expect(page.locator('.alert')).toContainText('メールを送信しました');
  });

  test.describe('テストを実行します[POST] @attack', () => {
    let message: HttpMessage;
    test('HttpMessage を取得します', async () => {
      const messages = await zapClient.getMessages(url, await zapClient.getNumberOfMessages(url) - 1, 1);
      message = messages.pop();
      expect(message.requestHeader).toContain('POST ' + url);
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
