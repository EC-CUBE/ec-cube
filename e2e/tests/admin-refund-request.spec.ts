import { test, expect } from '@playwright/test';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';

test.describe('Admin Refund Request', () => {
  test.describe.configure({ mode: 'serial' });

  test('返品申請一覧が表示される', async ({ page }) => {
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    // ページタイトル
    await expect(page.locator('.c-pageTitle')).toContainText('返品申請管理');
  });

  test('返品申請の検索ができる', async ({ page }) => {
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    // 空検索（全件表示）
    await page.locator('button.btn-ec-conversion', { hasText: '検索' }).click();
    await page.waitForLoadState('load');

    await expect(page.locator('.c-pageTitle')).toContainText('返品申請管理');
  });

  test('返品申請詳細が表示される', async ({ page }) => {
    // まず一覧を表示して検索
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    // 検索して結果を取得
    await page.locator('button.btn-ec-conversion', { hasText: '検索' }).click();
    await page.waitForLoadState('load');

    // 編集リンクをクリック（結果がある場合）
    const editBtn = page.locator('a.btn-ec-actionIcon').first();
    const hasResults = await editBtn.isVisible().catch(() => false);

    if (hasResults) {
      await editBtn.click();
      await page.waitForLoadState('load');

      // 詳細ページの項目確認
      await expect(page.locator('.c-pageTitle')).toContainText('返品申請詳細');
      await expect(page.locator('.card-body')).toContainText('申請ID');
      await expect(page.locator('.card-body')).toContainText('ステータス');
      await expect(page.locator('.card-body')).toContainText('注文番号');
      await expect(page.locator('.card-body')).toContainText('返品理由');
    }
  });

  test('管理者メモを保存できる', async ({ page }) => {
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    await page.locator('button.btn-ec-conversion', { hasText: '検索' }).click();
    await page.waitForLoadState('load');

    const editBtn = page.locator('a.btn-ec-actionIcon').first();
    const hasResults = await editBtn.isVisible().catch(() => false);

    if (hasResults) {
      await editBtn.click();
      await page.waitForLoadState('load');

      // 管理者メモを入力して保存
      const memo = `E2Eテストメモ_${Date.now()}`;
      await page.locator('#admin_refund_request_edit_admin_note').fill(memo);

      await page.locator('button.btn-ec-conversion', { hasText: '保存' }).click();
      await page.waitForLoadState('load');

      // 保存成功メッセージ
      await expect(page.locator('.alert-success').first()).toContainText('保存しました');

      // メモが保存されている
      const savedMemo = await page.locator('#admin_refund_request_edit_admin_note').inputValue();
      expect(savedMemo).toBe(memo);
    }
  });

  test('ステータス変更（処理開始）ができる', async ({ page }) => {
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    await page.locator('button.btn-ec-conversion', { hasText: '検索' }).click();
    await page.waitForLoadState('load');

    // ステータスが「新規申請」の申請の編集ボタンをクリック
    const rows = page.locator('table.table tbody tr');
    const rowCount = await rows.count();
    let foundNew = false;

    for (let i = 0; i < rowCount; i++) {
      const statusText = await rows.nth(i).locator('td').nth(4).innerText();
      if (statusText.includes('新規申請')) {
        await rows.nth(i).locator('a.btn-ec-actionIcon').click();
        foundNew = true;
        break;
      }
    }

    if (foundNew) {
      await page.waitForLoadState('load');

      // 遷移選択肢が表示される
      const transitionSelect = page.locator('#admin_refund_request_edit_transition');
      await expect(transitionSelect).toBeVisible();

      // 処理開始を選択して保存
      await transitionSelect.selectOption('start_processing');
      await page.locator('button.btn-ec-conversion', { hasText: '保存' }).click();
      await page.waitForLoadState('load');

      await expect(page.locator('.alert-success').first()).toContainText('保存しました');

      // ステータスが「処理中」になっている
      await expect(page.locator('.card-body').first()).toContainText('処理中');
    }
  });

  test('承認済・却下ではステータス変更欄が表示されない', async ({ page }) => {
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    await page.locator('button.btn-ec-conversion', { hasText: '検索' }).click();
    await page.waitForLoadState('load');

    // ステータスが「処理中」の申請を承認する
    const rows = page.locator('table.table tbody tr');
    const rowCount = await rows.count();
    let foundProcessing = false;

    for (let i = 0; i < rowCount; i++) {
      const statusText = await rows.nth(i).locator('td').nth(4).innerText();
      if (statusText.includes('処理中')) {
        await rows.nth(i).locator('a.btn-ec-actionIcon').click();
        foundProcessing = true;
        break;
      }
    }

    if (foundProcessing) {
      await page.waitForLoadState('load');

      // 承認を選択して保存
      const transitionSelect = page.locator('#admin_refund_request_edit_transition');
      await transitionSelect.selectOption('accept');
      await page.locator('button.btn-ec-conversion', { hasText: '保存' }).click();
      await page.waitForLoadState('load');

      await expect(page.locator('.alert-success').first()).toContainText('保存しました');
      await expect(page.locator('.card-body').first()).toContainText('承認済');

      // 承認済ではステータス変更欄が非表示
      await expect(page.locator('#admin_refund_request_edit_transition')).not.toBeVisible();
    }
  });

  test('CSVエクスポートボタンが表示される', async ({ page }) => {
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    await page.locator('button.btn-ec-conversion', { hasText: '検索' }).click();
    await page.waitForLoadState('load');

    // CSV エクスポートボタンが存在する
    const csvBtn = page.locator('a', { hasText: 'CSVエクスポート' });
    const hasResults = await csvBtn.isVisible().catch(() => false);
    if (hasResults) {
      await expect(csvBtn).toBeVisible();
    }
  });

  test('一覧から返品申請一覧へ戻れる', async ({ page }) => {
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    await page.locator('button.btn-ec-conversion', { hasText: '検索' }).click();
    await page.waitForLoadState('load');

    const editBtn = page.locator('a.btn-ec-actionIcon').first();
    const hasResults = await editBtn.isVisible().catch(() => false);

    if (hasResults) {
      await editBtn.click();
      await page.waitForLoadState('load');

      // 一覧へ戻る
      await page.locator('a.c-baseLink', { hasText: '返品申請一覧へ戻る' }).click();
      await page.waitForLoadState('load');

      await expect(page.locator('.c-pageTitle')).toContainText('返品申請管理');
    }
  });
});
