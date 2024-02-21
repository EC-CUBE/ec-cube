import { test, expect, chromium, Page } from '@playwright/test';
import PlaywrightConfig from '../../playwright.config';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, ContextType, Risk, HttpMessage } from '../../utils/ZapClient';
import { ECCUBE_ADMIN_ROUTE } from '../../config/default.config';

const zapClient = new ZapClient();

const url = `${PlaywrightConfig.use.baseURL}/${ECCUBE_ADMIN_ROUTE}/order/4/mail`;

test.describe.serial('受注管理>メール通知のテストをします', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.startSession(ContextType.Admin, 'admin_contact')
      .then(async () => expect(await zapClient.isForcedUserModeEnabled()).toBeTruthy());

    const browser = await chromium.launch();
    page = await browser.newPage();
    await page.goto(url);
  });

  test('メール通知ページを表示します', async () => {
    await expect(page).toHaveTitle(/メール通知/);
  });

  test('タイトルを確認します', async () => {
    await page.textContent('.c-pageTitle__title')
      .then(title => expect(title).toContain('メール通知'));
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

  test('メールテンプレートを選択します', async () => {
    await page.selectOption('#template-change', { label: '注文受付メール' });
    await expect(page.locator('#admin_order_mail_mail_subject')).toHaveValue('ご注文ありがとうございます');
  });

  test('確認ページへ遷移します', async () => {
    await page.click('button >> text=送信内容を確認');
  });

  let message: HttpMessage;
  test('HttpMessage を取得します', async () => {
    message = await zapClient.getLastMessage(url);
  });

  test.describe('テストを実行します[POST][入力→確認] @attack', () => {
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
