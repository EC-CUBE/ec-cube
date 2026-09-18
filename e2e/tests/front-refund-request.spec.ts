import { test, expect, Page } from '@playwright/test';
import * as path from 'path';

const refundTestEmail = 'refund-test@test.test';
const refundTestPassword = 'password';

async function loginAsRefundTestCustomer(page: Page) {
  await page.goto('/mypage/login');
  await page.waitForLoadState('load');
  await page.locator('input[name="login_email"]').fill(refundTestEmail);
  await page.locator('input[name="login_pass"]').fill(refundTestPassword);
  await page.getByRole('button', { name: 'ログイン' }).click();
  await page.waitForLoadState('load');
}

/**
 * 発送済み注文の注文番号と商品明細IDを取得する.
 * マイページ購入履歴詳細から返品申請リンクのURLを解析して取得.
 */
async function getDeliveredOrderInfo(page: Page): Promise<{ orderNo: string; orderItemId: string }> {
  await page.goto('/mypage/');
  await page.waitForLoadState('load');

  // 詳細を見るリンクをクリック
  const detailLink = page.locator('p.ec-historyListHeader__action a').first();
  await expect(detailLink).toBeVisible({ timeout: 10_000 });
  await detailLink.click();
  await page.waitForLoadState('load');

  // 返品申請リンクからURLパラメータを取得
  const refundLink = page.locator('a.ec-inlineBtn--action', { hasText: '返品申請' }).first();
  await expect(refundLink).toBeVisible({ timeout: 10_000 });

  const href = await refundLink.getAttribute('href');
  if (!href) throw new Error('返品申請リンクが見つかりません');

  // /mypage/refund_request/{order_no}/{order_item_id} からパラメータ抽出
  const match = href.match(/\/mypage\/refund_request\/([^/]+)\/(\d+)/);
  if (!match) throw new Error(`返品申請リンクのURLパースに失敗: ${href}`);

  return { orderNo: match[1], orderItemId: match[2] };
}

test.describe('Front Refund Request', () => {
  test.describe.configure({ mode: 'serial' });

  test('発送済み注文の詳細に返品申請リンクが表示される', async ({ page }) => {
    await loginAsRefundTestCustomer(page);

    await page.goto('/mypage/');
    await page.waitForLoadState('load');

    // 詳細を見る
    await page.locator('p.ec-historyListHeader__action a').first().click();
    await page.waitForLoadState('load');

    // 返品申請リンクが存在する
    await expect(page.locator('a.ec-inlineBtn--action', { hasText: '返品申請' }).first()).toBeVisible();
    // 返品申請履歴リンクが存在する
    await expect(page.locator('a.ec-inlineBtn', { hasText: '返品申請履歴' }).first()).toBeVisible();
  });

  test('返品申請フォームが表示される', async ({ page }) => {
    await loginAsRefundTestCustomer(page);
    const { orderNo, orderItemId } = await getDeliveredOrderInfo(page);

    await page.goto(`/mypage/refund_request/${orderNo}/${orderItemId}`);
    await page.waitForLoadState('load');

    // タイトル
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('返品申請');

    // フォームフィールドが存在する
    await expect(page.locator('#refund_request_quantity')).toBeVisible();
    await expect(page.locator('#refund_request_reason')).toBeVisible();
    await expect(page.locator('#refund_request_files')).toBeVisible();

    // 確認画面へボタン
    await expect(page.locator('button.ec-blockBtn--action', { hasText: '確認画面へ' })).toBeVisible();
  });

  test('返品申請（ファイルなし）：入力→確認→完了', async ({ page }) => {
    await loginAsRefundTestCustomer(page);
    const { orderNo, orderItemId } = await getDeliveredOrderInfo(page);

    // 入力画面
    await page.goto(`/mypage/refund_request/${orderNo}/${orderItemId}`);
    await page.waitForLoadState('load');

    await page.locator('#refund_request_quantity').fill('1');
    await page.locator('#refund_request_reason').fill('商品に不具合がありました。E2Eテスト用の返品申請です。');

    // 確認画面へ
    await page.locator('button.ec-blockBtn--action', { hasText: '確認画面へ' }).click();
    await page.waitForLoadState('load');

    // 確認画面の表示確認
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('返品申請内容確認');
    await expect(page.locator('.ec-borderedDefs')).toContainText('1');
    await expect(page.locator('.ec-borderedDefs')).toContainText('商品に不具合がありました');

    // 申請する
    await page.locator('button.ec-blockBtn--action', { hasText: '申請する' }).click();
    await page.waitForLoadState('load');

    // 完了画面
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('返品申請完了');
    await expect(page.locator('.ec-reportHeading h2')).toContainText('返品申請を受け付けました');
  });

  test('返品申請履歴が表示される', async ({ page }) => {
    await loginAsRefundTestCustomer(page);
    const { orderNo, orderItemId } = await getDeliveredOrderInfo(page);

    await page.goto(`/mypage/refund_request/${orderNo}/${orderItemId}/history`);
    await page.waitForLoadState('load');

    await expect(page.locator('div.ec-pageHeader h1')).toContainText('返品申請履歴');
    // 先ほど申請した内容が表示される
    await expect(page.locator('main')).toContainText('新規申請');
  });

  test('数量0でバリデーションエラー', async ({ page }) => {
    await loginAsRefundTestCustomer(page);
    const { orderNo, orderItemId } = await getDeliveredOrderInfo(page);

    await page.goto(`/mypage/refund_request/${orderNo}/${orderItemId}`);
    await page.waitForLoadState('load');

    await page.locator('#refund_request_quantity').fill('0');
    await page.locator('#refund_request_reason').fill('テスト理由');

    await page.locator('button.ec-blockBtn--action', { hasText: '確認画面へ' }).click();
    await page.waitForLoadState('load');

    // 入力画面に留まる（確認画面に遷移しない）
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('返品申請');
    await expect(page.locator('div.ec-pageHeader h1')).not.toContainText('確認');
  });

  test('確認画面にはサマリー表示のみで編集可能な入力欄が残らない（H-2 回帰防止）', async ({ page }) => {
    await loginAsRefundTestCustomer(page);
    const { orderNo, orderItemId } = await getDeliveredOrderInfo(page);

    await page.goto(`/mypage/refund_request/${orderNo}/${orderItemId}`);
    await page.waitForLoadState('load');

    await page.locator('#refund_request_quantity').fill('1');
    await page.locator('#refund_request_reason').fill('H-2 回帰防止：確認画面に編集欄が残らないこと');
    await page.locator('button.ec-blockBtn--action', { hasText: '確認画面へ' }).click();
    await page.waitForLoadState('load');

    // 確認画面に遷移している
    await expect(page).toHaveURL(/\/confirm$/);

    // 確認画面のフォーム内に編集可能な input/textarea/file が残っていないこと
    const confirmForm = page.locator('form[action$="/confirm"]');
    await expect(confirmForm.locator('input[name="refund_request[quantity]"]')).toHaveCount(0);
    await expect(confirmForm.locator('textarea[name="refund_request[reason]"]')).toHaveCount(0);
    await expect(confirmForm.locator('input[type="file"]')).toHaveCount(0);
    // hidden の _token は存在する
    await expect(confirmForm.locator('input[name="_token"]')).toHaveCount(1);
  });

  test('返品申請（ファイル添付）：入力→確認→完了で DB にファイルが保存される（H-1 回帰防止）', async ({ page }) => {
    await loginAsRefundTestCustomer(page);
    const { orderNo, orderItemId } = await getDeliveredOrderInfo(page);

    // 入力画面
    await page.goto(`/mypage/refund_request/${orderNo}/${orderItemId}`);
    await page.waitForLoadState('load');

    await page.locator('#refund_request_quantity').fill('1');
    await page.locator('#refund_request_reason').fill('H-1 回帰防止：エビデンスファイルが本当に保存されること');

    // 同梱フィクスチャ画像を添付
    const fixture = path.resolve(__dirname, '../fixtures/evidence.png');
    await page.locator('#refund_request_files').setInputFiles(fixture);

    await page.locator('button.ec-blockBtn--action', { hasText: '確認画面へ' }).click();
    await page.waitForLoadState('load');

    // 確認画面でファイル名が見える
    await expect(page).toHaveURL(/\/confirm$/);
    await expect(page.locator('main')).toContainText('evidence.png');

    // 申請する
    await page.locator('button.ec-blockBtn--action', { hasText: '申請する' }).click();
    await page.waitForLoadState('load');

    // 完了画面に遷移
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('返品申請完了');

    // 履歴ページで「画像/動画ファイルあり」が見えること（保存された証拠）
    await page.goto(`/mypage/refund_request/${orderNo}/${orderItemId}/history`);
    await page.waitForLoadState('load');
    // 履歴に保存ファイルのリンクが含まれていること
    await expect(page.locator('a[href*="/mypage/refund_request/file/"]').first()).toBeVisible();
  });

  test('確認画面はセッション喪失時に入力画面へ戻る', async ({ page }) => {
    await loginAsRefundTestCustomer(page);
    const { orderNo, orderItemId } = await getDeliveredOrderInfo(page);

    // セッション未投入のままで confirm に直アクセス
    await page.goto(`/mypage/refund_request/${orderNo}/${orderItemId}/confirm`);
    await page.waitForLoadState('load');

    // 入力画面（confirm が付かない URL）に戻されている
    await expect(page).toHaveURL(new RegExp(`/mypage/refund_request/${orderNo}/${orderItemId}$`));
  });

  test('理由空欄でバリデーションエラー', async ({ page }) => {
    await loginAsRefundTestCustomer(page);
    const { orderNo, orderItemId } = await getDeliveredOrderInfo(page);

    await page.goto(`/mypage/refund_request/${orderNo}/${orderItemId}`);
    await page.waitForLoadState('load');

    await page.locator('#refund_request_quantity').fill('1');
    await page.locator('#refund_request_reason').fill('');

    await page.locator('button.ec-blockBtn--action', { hasText: '確認画面へ' }).click();
    await page.waitForLoadState('load');

    // 入力画面に留まる
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('返品申請');
    await expect(page.locator('div.ec-pageHeader h1')).not.toContainText('確認');
  });
});
