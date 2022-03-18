import { test, expect, chromium, Page } from '@playwright/test';
import PlaywrightConfig from '../../playwright.config';
import { intervalRepeater } from '../../utils/Progress';
import { ZapClient, Mode, ContextType, Risk, HttpMessage } from '../../utils/ZapClient';
import { ECCUBE_ADMIN_ROUTE } from '../../config/default.config';

const zapClient = new ZapClient();

const url = `${PlaywrightConfig.use.baseURL}/${ECCUBE_ADMIN_ROUTE}/customer/new`;

test.describe.serial('会員管理 会員登録のテストを行います', () => {
  let page: Page;
  test.beforeAll(async () => {
    await zapClient.setMode(Mode.Protect);
    await zapClient.newSession('/zap/wrk/sessions/admin_customer_new', true);
    await zapClient.importContext(ContextType.Admin);

    if (!await zapClient.isForcedUserModeEnabled()) {
      await zapClient.setForcedUserModeEnabled();
      expect(await zapClient.isForcedUserModeEnabled()).toBeTruthy();
    }
    const browser = await chromium.launch();
    page = await browser.newPage();
    await page.goto(url);
  });

  test('会員管理 会員登録のページを表示します', async () => {
    await expect(page).toHaveTitle(/会員登録/);
  });

  test('タイトルを確認します', async () => {
    await page.textContent('.c-pageTitle__subTitle')
      .then(title => expect(title).toContain('会員管理'));
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

  test('ユーザ情報を入力します', async () => {
    await page.fill('[placeholder="姓"]', '山田');
    await page.fill('[placeholder="名"]', '太郎');
    await page.fill('[placeholder="セイ"]', 'ヤマダ');
    await page.fill('[placeholder="メイ"]', 'タロウ');
    await page.fill('input[name="admin_customer\\[company_name\\]"]', 'イーシーキューブ');
    await page.fill('[placeholder="例：5300001"]', '5300001');
    await page.click('select[name="admin_customer\\[address\\]\\[pref\\]"]');
    await page.selectOption('select[name="admin_customer\\[address\\]\\[pref\\]"]', '1');
    await page.fill('[placeholder="市区町村名\\(例：大阪市北区\\)"]', '大阪市北区梅田');
    await page.fill('[placeholder="番地・ビル名\\(例：西梅田1丁目6-8\\)"]', '2-4-9');
    await page.fill('[placeholder="例：ec-cube\\@example\\.com"]', 'taro_yamada@test.local');
    await page.fill('[placeholder="例：11122223333"]', '0001112222');
    await page.fill('input[name="admin_customer\\[password\\]\\[first\\]"]', 'password123');
    await page.fill('input[name="admin_customer\\[password\\]\\[second\\]"]', 'password123');
    await page.click('input[name="admin_customer\\[sex\\]"]');
    await page.selectOption('select[name="admin_customer\\[job\\]"]', '3');
    await page.fill('input[name="admin_customer\\[birth\\]"]', '1980-04-01');
    await page.fill('input[name="admin_customer\\[point\\]"]', '10');
    await page.fill('textarea[name="admin_customer\\[note\\]"]', '国語国語国語国語国語国語国語国語国語国語国語国語国語国語国語国語国語国語国語国語国語国語');
    await page.click('button:has-text("登録")');
  });

  let message: HttpMessage;
  test('HttpMessage を取得します', async () => {
    message = await zapClient.getLastMessage(url);
  });

  test.describe('テストを実行します[POST][入力→登録] @attack', () => {
    let scanId: number;
    test('アクティブスキャンを実行します', async () => {
      scanId = await zapClient.activeScan(url, false, true, null, 'POST', message.requestBody);
      await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000, page);
    });

    test('ユーザ登録の結果を確認します', async () => {
      await zapClient.getAlerts(url, 0, 1, Risk.High)
        .then(alerts => expect(alerts).toEqual([]));
    });
  });
});
