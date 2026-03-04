import { test, expect, chromium, Page } from '@playwright/test';
import PlaywrightConfig from '../../playwright.config';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, ContextType, Risk, HttpMessage } from '../../utils/ZapClient';
const zapClient = new ZapClient();

const url = `${PlaywrightConfig.use.baseURL}/contact`;

test.describe.serial('お問い合わせフォームのテストをします', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.startSession(ContextType.FrontGuest, 'front_guest_contact');

    const browser = await chromium.launch();
    page = await browser.newPage();
    await page.goto(url);
  });

  test('お問い合わせページを表示します', async () => {
    await expect(page).toHaveTitle(/お問い合わせ/);
  });

  test('タイトルを確認します', async () => {
    await page.textContent('.ec-pageHeader')
      .then(title => expect(title).toContain('お問い合わせ'));
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

  test('お問い合わせ内容を入力します', async () => {
    await page.fill('#contact_name_name01', '石');
    await page.fill('#contact_name_name02', '球部');
    await page.fill('#contact_kana_kana01', 'イシ');
    await page.fill('#contact_kana_kana02', 'キュウブ');
    await page.fill('#contact_postal_code', '5300001');
    await page.selectOption('#contact_address_pref', { label: '大阪府' });
    await page.fill('#contact_address_addr01', '大阪市北区梅田');
    await page.fill('#contact_address_addr02', '2-4-9');
    await page.fill('#contact_phone_number', '9999999999');
    await page.fill('#contact_email', 'zap_user@example.com');
    await page.fill('#contact_contents', 'お問い合わせ入力');
    await page.click('button >> text=確認ページへ');
  });

  test('入力内容を確認します', async () => {
    await expect(page.locator('#contact_email')).toBeHidden();
    await expect(page.locator('#contact_email')).toHaveValue('zap_user@example.com');
  });

  let message: HttpMessage;
  test('HttpMessage を取得します', async () => {
    message = await zapClient.getLastMessage(url);
  });

  let completeRequestBody: string;
  test('確認→完了画面の requestBody に書き換えます', async () => {
    completeRequestBody = message.requestBody.replace(/mode=confirm/, 'mode=complete&mode_complete=dummy');
    expect(completeRequestBody).toContain('mode=complete');
  });

  test('CSRFトークン を取得し直します', async () => {
    await page.goto(url);
    const token = await page.inputValue('#contact__token');
    completeRequestBody = completeRequestBody.replace(/contact%5B_token%5D=[a-zA-Z0-9\-_]+/, `contact%5B_token%5D=${token}`);
    expect(completeRequestBody).toMatch(token);
  });

  let completeMessage: HttpMessage;
  test('content-length を書き換えて手動リクエストを送信します', async () => {
    const requestHeader = message.requestHeader.replace(
      'Content-Length: ' + message.requestBody.length,
      'Content-Length: ' + completeRequestBody.length
    );
    await zapClient.sendRequest(requestHeader + completeRequestBody);
    completeMessage = await zapClient.getLastMessage(url);
  });

  test.describe('テストを実行します[POST][入力→確認] @attack', () => {
    let scanId: number;
    test('アクティブスキャンを実行します', async () => {
      scanId = await zapClient.activeScan(url, false, true, null, 'POST', message.requestBody);
      await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000, page);
    });

    test('結果を確認します', async () => {
      await zapClient.getAlerts(url, 0, 1, Risk.High)
        .then(alerts => expect(alerts).toEqual([]));
    });
  });

  test.describe('テストを実行します[POST][確認→完了] @attack', () => {
    let scanId: number;
    test('アクティブスキャンを実行します', async () => {
      expect(completeMessage.responseHeader).toContain('HTTP/1.1 302 Found');
      expect(completeMessage.responseHeader).toContain('Location: /contact/complete');

      scanId = await zapClient.activeScan(url, false, true, null, 'POST', completeMessage.requestBody);
      await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000, page);
    });

    test('結果を確認します', async () => {
      await zapClient.getAlerts(url, 0, 1, Risk.High)
        .then(alerts => expect(alerts).toEqual([]));
    });
  });
});
