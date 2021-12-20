import { test, expect, chromium, Page } from '@playwright/test';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, Mode, ContextType, Risk, HttpMessage } from '../../utils/ZapClient';
const zapClient = new ZapClient('http://127.0.0.1:8090');

const baseURL = 'https://ec-cube';
const url = baseURL + '/contact';

test.describe('お問い合わせフォームのテストをします', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.setMode(Mode.Protect);
    await zapClient.newSession('/zap/wrk/sessions/front_guest_contact', true);
    await zapClient.importContext(ContextType.FrontGuest);
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
    await page.click('button.ec-blockBtn--action[type=submit][name=mode][value=confirm]');
  });

  test.describe('テストを実行します[POST][入力→g確認] @attack', () => {
    let message: HttpMessage;
    test('HttpMessage を取得します', async () => {
      message = await zapClient.getLastMessage(url);
    });
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
});
