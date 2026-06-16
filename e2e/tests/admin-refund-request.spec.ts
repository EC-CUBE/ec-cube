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

    // 前提: fixture により返品申請が少なくとも1件存在する
    const editBtn = page.locator('a.btn-ec-actionIcon').first();
    await expect(editBtn).toBeVisible();
    await editBtn.click();
    await page.waitForLoadState('load');

    // 詳細ページの項目確認（申請内容カードに各項目が並ぶ）
    await expect(page.locator('.c-pageTitle')).toContainText('返品申請詳細');
    const detailCard = page.locator('.card-body').first();
    await expect(detailCard).toContainText('申請ID');
    await expect(detailCard).toContainText('ステータス');
    await expect(detailCard).toContainText('注文番号');
    await expect(detailCard).toContainText('返品理由');
  });

  test('管理者メモを保存できる', async ({ page }) => {
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    await page.locator('button.btn-ec-conversion', { hasText: '検索' }).click();
    await page.waitForLoadState('load');

    const editBtn = page.locator('a.btn-ec-actionIcon').first();
    await expect(editBtn).toBeVisible();
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

    // 前提: fixture により「新規申請」状態の返品申請が必ず存在する
    expect(foundNew, '「新規申請」ステータスの返品申請が一覧に存在しません').toBe(true);
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
  });

  test('承認済・却下ではステータス変更欄が表示されない', async ({ page }) => {
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    await page.locator('button.btn-ec-conversion', { hasText: '検索' }).click();
    await page.waitForLoadState('load');

    // ステータスが「処理中」の申請を承認する（前のテストで「処理中」に遷移済み）
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

    expect(foundProcessing, '「処理中」ステータスの返品申請が一覧に存在しません').toBe(true);
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
  });

  test('CSVエクスポートボタンが表示される', async ({ page }) => {
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    await page.locator('button.btn-ec-conversion', { hasText: '検索' }).click();
    await page.waitForLoadState('load');

    // CSV エクスポートボタンが存在する
    const csvBtn = page.locator('a', { hasText: 'CSVエクスポート' });
    await expect(csvBtn).toBeVisible();
  });

  test('一覧の件数セレクタが表示され、切替で URL が変わる', async ({ page }) => {
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    const select = page.locator('select.form-select').first();
    await expect(select).toBeVisible();

    // option に 10件/20件/.../100件 が並ぶ
    await expect(select.locator('option', { hasText: '10件' })).toHaveCount(1);
    await expect(select.locator('option', { hasText: '100件' })).toHaveCount(1);

    // 20件に切り替え（onchange="location = this.value;"）
    await select.selectOption({ label: '20件' });
    await page.waitForLoadState('load');

    // URL に page_count=20 が反映されている
    await expect(page).toHaveURL(/page_count=20/);
    // 切替後も「20件」が selected
    await expect(page.locator('select.form-select option[selected]').first()).toHaveText('20件');
  });

  test('検索結果初期表示で結果ゼロにならない（status IN (NULL) 回帰防止）', async ({ page }) => {
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    // 初期表示でデータがある環境では「検索結果に合致するデータが見つかりませんでした」が出ないこと
    await expect(page.locator('body')).not.toContainText('検索条件に合致するデータが見つかりませんでした');
  });

  test('一覧から返品申請一覧へ戻れる', async ({ page }) => {
    await page.goto(`/${adminRoute}/order/refund_request`);
    await page.waitForLoadState('load');

    await page.locator('button.btn-ec-conversion', { hasText: '検索' }).click();
    await page.waitForLoadState('load');

    const editBtn = page.locator('a.btn-ec-actionIcon').first();
    await expect(editBtn).toBeVisible();
    await editBtn.click();
    await page.waitForLoadState('load');

    // 一覧へ戻る
    await page.locator('a.c-baseLink', { hasText: '返品申請一覧へ戻る' }).click();
    await page.waitForLoadState('load');

    await expect(page.locator('.c-pageTitle')).toContainText('返品申請管理');
  });
});
