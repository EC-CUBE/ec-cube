import { test, expect } from '@playwright/test';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';

/**
 * Helper: Re-login to admin if session expired.
 * When tests create new browser contexts for front operations,
 * the admin session may be invalidated on the server side.
 */
async function ensureAdminLoggedIn(page: import('@playwright/test').Page) {
  if (await page.locator('#login_id').count() > 0) {
    await page.locator('#login_id').fill(process.env.ADMIN_USER || 'admin');
    await page.locator('#password').fill(process.env.ADMIN_PASSWORD || 'password');
    await page.getByRole('button', { name: 'ログイン' }).click();
    await page.waitForLoadState('load');
  }
}

test.describe('Admin Basic Info (EA07)', () => {
  test.describe.configure({ mode: 'serial' });

  test('basicinfo_shop_settings - EA0701-UC01-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/setting/shop')) {
      await page.goto(`/${adminRoute}/setting/shop`);
      await page.waitForLoadState('load');
    }
    await expect(page.locator('.c-pageTitle')).toContainText('基本設定');

    await page.locator('#shop_master_company_name').fill('サンプル会社名');
    await page.locator('#shop_master_shop_name').fill('サンプルショップ');
    await page.locator('#shop_master_postal_code').fill('100-0001');
    await page.locator('#shop_master_phone_number').fill('050-5555-5555');

    await page.waitForTimeout(2000);

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました', { timeout: 30_000 });

    // Verify on the about page
    await page.goto('/help/about');
    await page.waitForLoadState('load');
    await expect(page.locator('#help_about_box__company_name dd')).toContainText('サンプル会社名');
    await expect(page.locator('#help_about_box__shop_name dd')).toContainText('サンプルショップ');
    await expect(page.locator('#help_about_box__address dd')).toContainText('1000001');
    await expect(page.locator('#help_about_box__phone_number dd')).toContainText('05055555555');
  });

  test('basicinfo_payment_list - EA0704-UC01-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/shop/payment`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/payment')) {
      await page.goto(`/${adminRoute}/setting/shop/payment`);
      await page.waitForLoadState('load');
    }
    await expect(page.locator('.c-pageTitle')).toContainText('支払方法一覧');

    // Verify that payment methods are listed
    const count = await page.locator('.c-contentsArea__primaryCol li.sortable-item').count();
    expect(count).toBeGreaterThanOrEqual(1);
  });

  test('basicinfo_payment_crud - EA0705-UC01/UC02/EA0704-UC03', async ({ page }) => {
    const paymentName = 'test_payment_' + Date.now();
    const paymentNameEdited = paymentName + '_edited';

    // --- CREATE ---
    await page.goto(`/${adminRoute}/setting/shop/payment`);
    await page.waitForLoadState('load');
    const beforeCount = await page.locator('.c-contentsArea__primaryCol li.sortable-item').count();

    await page.locator('.c-contentsArea__primaryCol .btn-ec-regular').click();
    await page.waitForLoadState('load');

    await page.locator('#payment_register_method').fill(paymentName);
    await page.locator('#payment_register_charge').fill('100');
    await page.locator('#payment_register_rule_min').fill('1');

    await page.locator('#form1 > .c-conversionArea > .c-conversionArea__container button.btn-ec-conversion').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-container .c-contentsArea div.alert-success')).toContainText('保存しました');

    // Verify on list
    await page.goto(`/${adminRoute}/setting/shop/payment`);
    await page.waitForLoadState('load');
    const afterCreateCount = await page.locator('.c-contentsArea__primaryCol li.sortable-item').count();
    expect(afterCreateCount).toBe(beforeCount + 1);
    // New payment appears first (nth-child(2) because list has a header item)
    await expect(page.locator('.c-contentsArea__primaryCol .c-primaryCol .card-body ul li:nth-child(2)')).toContainText(paymentName);

    // --- EDIT ---
    await page.locator('.c-contentsArea__primaryCol .list-group-flush .list-group-item:nth-child(2) a[href*="edit"]').click();
    await page.waitForLoadState('load');

    await page.locator('#payment_register_method').fill(paymentNameEdited);
    await page.locator('#payment_register_charge').fill('1000');

    await page.locator('#form1 > .c-conversionArea > .c-conversionArea__container button.btn-ec-conversion').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-container .c-contentsArea div.alert-success')).toContainText('保存しました');

    await page.goto(`/${adminRoute}/setting/shop/payment`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea__primaryCol .c-primaryCol .card-body ul li:nth-child(2)')).toContainText(paymentNameEdited);

    // --- DELETE ---
    // Click delete on first item (the one we just edited)
    await page.locator('.c-contentsArea__primaryCol .list-group-flush .list-group-item:nth-child(2) a[data-bs-target="#DeleteModal"]').click();
    await page.waitForTimeout(500);
    await page.locator('#DeleteModal').waitFor({ state: 'visible' });
    await page.locator('#DeleteModal > div > div > div.modal-footer > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-container .c-contentsArea div.alert-success')).toContainText('削除しました');

    // Verify count is back to original
    await page.goto(`/${adminRoute}/setting/shop/payment`);
    await page.waitForLoadState('load');
    const afterDeleteCount = await page.locator('.c-contentsArea__primaryCol li.sortable-item').count();
    expect(afterDeleteCount).toBe(beforeCount);
  });

  test('basicinfo_delivery_list - EA0706-UC01-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/shop/delivery`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('配送方法一覧');

    // Verify that delivery methods are listed
    const count = await page.locator('.c-contentsArea__primaryCol li.sortable-item').count();
    expect(count).toBeGreaterThanOrEqual(1);
  });

  test('basicinfo_支払方法入れ替え - EA0704-UC02-T01', async ({ page }) => {
    // Navigate to payment list
    await page.goto(`/${adminRoute}/setting/shop/payment`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('支払方法一覧');

    // Helper: click sort button and wait for AJAX to complete.
    async function clickSortButton(selector: string) {
      await page.evaluate(() => {
        document.querySelectorAll('.tooltip').forEach(el => el.remove());
      });
      await page.locator(selector).click({ force: true });
      await page.waitForTimeout(200);
      await page.waitForFunction(() => !document.querySelector('.modal-backdrop'), {}, { timeout: 10_000 });
    }

    // Payment list: li:nth-child(1) = header, li:nth-child(2+) = data
    // Verify 郵便振替 is at position 1 (nth-child(2))
    const paymentSelector = (n: number) =>
      `.c-contentsArea__primaryCol .c-primaryCol .card-body ul li:nth-child(${n + 1})`;
    await expect(page.locator(paymentSelector(1))).toContainText('郵便振替');

    // Move first payment down (nth-child(2) = row 1)
    await clickSortButton(`.c-contentsArea__primaryCol .list-group-flush .list-group-item:nth-child(2) a.action-down`);

    // Reload and verify 郵便振替 moved to position 2 (nth-child(3))
    await page.goto(`/${adminRoute}/setting/shop/payment`);
    await page.waitForLoadState('load');
    await expect(page.locator(paymentSelector(2))).toContainText('郵便振替');

    // Move it back up (nth-child(3) = row 2)
    await clickSortButton(`.c-contentsArea__primaryCol .list-group-flush .list-group-item:nth-child(3) a.action-up`);

    // Reload and verify 郵便振替 is back at position 1
    await page.goto(`/${adminRoute}/setting/shop/payment`);
    await page.waitForLoadState('load');
    await expect(page.locator(paymentSelector(1))).toContainText('郵便振替');
  });

  test('basicinfo_delivery_crud - EA0707-UC01/UC02/EA0706-UC03', async ({ page }) => {
    const deliveryName = 'test_delivery_' + Date.now();
    const deliveryNameEdited = deliveryName + '_edited';

    // --- CREATE ---
    await page.goto(`/${adminRoute}/setting/shop/delivery`);
    await page.waitForLoadState('load');
    const beforeCount = await page.locator('.c-contentsArea__primaryCol li.sortable-item').count();

    // Click new registration link
    await page.locator('a[href*="delivery/new"]').click();
    await page.waitForLoadState('load');

    await page.locator('#delivery_name').fill(deliveryName);
    await page.locator('#delivery_service_name').fill('名称');

    // Select payment methods
    await page.locator('#delivery_payments_1').check();
    await page.locator('#delivery_payments_4').check();

    // Add delivery time
    await page.locator('#add-delivery-time-value').fill('<AM>');
    await page.locator('#add-delivery-time-button').click();

    // Set flat rate shipping
    await page.locator('#delivery_free_all').fill('100');
    await page.locator('#set_fee_all').click();

    // Submit
    await page.getByRole('button', { name: '登録' }).click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-container div.c-contentsArea > div.alert-success')).toContainText('保存しました');

    // Verify on list
    await page.goto(`/${adminRoute}/setting/shop/delivery`);
    await page.waitForLoadState('load');
    const afterCreateCount = await page.locator('.c-contentsArea__primaryCol li.sortable-item').count();
    expect(afterCreateCount).toBe(beforeCount + 1);

    // Find the row with our delivery name
    await expect(page.locator(`div.c-primaryCol ul li:has-text("${deliveryName}")`)).toBeVisible();

    // --- EDIT ---
    // Click edit on the new delivery (it should be at position 2, right after the header)
    await page.locator(`div.c-primaryCol ul li:has-text("${deliveryName}") a[href*="edit"]`).click();
    await page.waitForLoadState('load');

    await page.locator('#delivery_name').fill(deliveryNameEdited);

    await page.getByRole('button', { name: '登録' }).click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-container div.c-contentsArea > div.alert-success')).toContainText('保存しました');

    // Verify
    await page.goto(`/${adminRoute}/setting/shop/delivery`);
    await page.waitForLoadState('load');
    await expect(page.locator(`div.c-primaryCol ul li:has-text("${deliveryNameEdited}")`)).toBeVisible();

    // --- DELETE ---
    await page.locator(`div.c-primaryCol ul li:has-text("${deliveryNameEdited}") a[data-bs-target="#DeleteModal"]`).click();
    await page.locator('#DeleteModal').waitFor({ state: 'visible' });
    await page.waitForTimeout(500);
    await page.locator('#DeleteModal > div > div > div.modal-footer > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-container div.c-contentsArea > div.alert-success')).toContainText('削除しました');

    // Verify count is back to original
    await page.goto(`/${adminRoute}/setting/shop/delivery`);
    await page.waitForLoadState('load');
    const afterDeleteCount = await page.locator('.c-contentsArea__primaryCol li.sortable-item').count();
    expect(afterDeleteCount).toBe(beforeCount);
  });

  test('basicinfo_配送方法一覧順序変更 - EA0706-UC02-T01', async ({ page }) => {
    // Navigate to delivery list
    await page.goto(`/${adminRoute}/setting/shop/delivery`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('配送方法一覧');

    // Helper: click sort button and wait for AJAX to complete.
    async function clickSortButton(selector: string) {
      await page.evaluate(() => {
        document.querySelectorAll('.tooltip').forEach(el => el.remove());
      });
      await page.locator(selector).click({ force: true });
      await page.waitForTimeout(200);
      await page.waitForFunction(() => !document.querySelector('.modal-backdrop'), {}, { timeout: 10_000 });
    }

    // Delivery list: li:nth-child(1) = header, li:nth-child(2+) = data
    const nameSelector = (n: number) => `div.c-primaryCol ul > li:nth-child(${n}) > div > div.col.d-flex.align-items-center > a`;

    // Verify initial order
    await expect(page.locator(nameSelector(2))).toContainText('サンプル宅配');
    await expect(page.locator(nameSelector(3))).toContainText('サンプル業者');

    // Move row 2 down
    await clickSortButton(`div.c-primaryCol ul > li:nth-child(2) a.action-down`);

    // Verify swapped
    await expect(page.locator(nameSelector(2))).toContainText('サンプル業者');
    await expect(page.locator(nameSelector(3))).toContainText('サンプル宅配');

    // Move row 3 up to restore
    await clickSortButton(`div.c-primaryCol ul > li:nth-child(3) a.action-up`);

    // Verify restored
    await expect(page.locator(nameSelector(2))).toContainText('サンプル宅配');
    await expect(page.locator(nameSelector(3))).toContainText('サンプル業者');
  });

  test('basicinfo_tax_rate - EA0708-UC01-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/shop/tax`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('税率設定');

    // Verify 10% is displayed
    await expect(page.locator('#ex-tax_rule-1 > td.align-middle.text-end')).toContainText('10%');

    // Register a new tax rate
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');

    await page.locator('table tbody tr:nth-child(1) input[type=number]').fill('8');
    await page.evaluate(({ y, m, d }) => {
      const el = document.querySelector('#tax_rule_apply_date') as HTMLInputElement;
      if (el) el.value = `${y}-${m}-${d}T00:00:00`;
    }, { y: year, m: month, d: day });

    await page.locator('table tbody tr:nth-child(1) button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('table > tbody > tr:nth-child(2) > td.align-middle.text-end .list')).toContainText('8%');

    // Edit the new tax rate
    await page.locator('table tbody tr:nth-child(2) .edit-button').click();
    await page.locator('table tbody tr:nth-child(2) input[type=number]').fill('12');
    await page.locator('table tbody tr:nth-child(2) > td > div.edit > button.btn.btn-ec-conversion').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-container .c-contentsArea .alert-success')).toContainText('保存しました');
    await expect(page.locator('table > tbody > tr:nth-child(2) > td.align-middle.text-end .list')).toContainText('12%');

    // Delete the new tax rate
    await page.locator('table tbody tr:nth-child(2) > td.align-middle.action > div > div > div:nth-child(2) > div.d-inline-block.me-3 > a').click();
    await page.locator('table tbody tr:nth-child(2) > td.align-middle.action > div > div > div:nth-child(2) > div.modal').waitFor({ state: 'visible' });
    await page.locator('table tbody tr:nth-child(2) > td.align-middle.action > div > div > div:nth-child(2) > div.modal.fade.show > div > div > div.modal-footer > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-container .c-contentsArea .alert-success')).toContainText('削除しました');
  });

  test('basicinfo_mail_settings - EA0709-UC02-T01', async ({ page }) => {
    const title = '商品出荷のお知らせ ' + Date.now();

    await page.goto(`/${adminRoute}/setting/shop/mail`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('メール設定');

    // Select template - this triggers an AJAX load of the template content
    await page.locator('#mail_template').selectOption({ label: '出荷通知メール' });
    // Wait for the subject field to be populated by the template load
    await page.waitForTimeout(2000);
    await expect(page.locator('#mail_mail_subject')).toBeVisible();

    // Edit subject
    await page.locator('#mail_mail_subject').fill(title);

    // Save
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました', { timeout: 30_000 });
  });

  test('basicinfo_csv_settings - EA0710-UC01-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/shop/csv`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('CSV出力項目設定');

    // Select CSV type
    await page.locator('#csv-type').selectOption({ label: '受注CSV' });
    await page.waitForTimeout(2000);

    // First, ensure "誕生日" is in the output list (it may have been removed by a prior run)
    const notOutputOptions = await page.locator('#csv-not-output option').allTextContents();
    if (notOutputOptions.some(o => o.includes('誕生日'))) {
      // Add it back to the output list first
      await page.locator('#csv-not-output').selectOption({ label: '誕生日' });
      await page.locator('#add').click();
      await page.locator('#csv-form > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button').click();
      await page.waitForLoadState('load');
      await expect(page.locator('.alert-success')).toContainText('保存しました');

      // Reload and re-select CSV type
      await page.goto(`/${adminRoute}/setting/shop/csv`);
      await page.waitForLoadState('load');
      await page.locator('#csv-type').selectOption({ label: '受注CSV' });
      await page.waitForTimeout(2000);
    }

    // Now select "誕生日" from output list and remove it
    await page.locator('#csv-output').selectOption({ label: '誕生日' });
    await page.locator('#remove').click();

    // Save settings
    await page.locator('#csv-form > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('basicinfo_order_status_settings - EA0711-UC01-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/shop/order_status`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('受注対応状況設定');

    // Edit first order status
    await page.locator('#form_OrderStatuses_0_name').fill('新規受付');
    await page.locator('#form_OrderStatuses_0_customer_order_status_name').fill('注文受付');
    await page.locator('#form_OrderStatuses_0_color').fill('#19406C');

    // Submit
    await page.locator('#ex-conversion-action > div > button').click();
    await page.waitForLoadState('load');
    await page.waitForTimeout(1000);
    await expect(page.locator('#page_admin_setting_shop_order_status .alert-success')).toContainText('保存しました', { timeout: 30_000 });

    // Verify
    await page.goto(`/${adminRoute}/setting/shop/order_status`);
    await page.waitForLoadState('load');
    await expect(page.locator('#form_OrderStatuses_0_customer_order_status_name')).toHaveValue('注文受付');
    await expect(page.locator('#form_OrderStatuses_0_name')).toHaveValue('新規受付');
  });

  test('basicinfo_tradelaw_settings - EA0702-UC01-T01', async ({ page }) => {
    // Navigate to trade law settings page
    await page.goto(`/${adminRoute}/setting/shop/tradelaw`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('特定商取引法設定');

    // Fill in trade law entries (index 0 = 販売業者, 1 = 代表責任者, etc.)
    const entries = [
      { index: 0, name: '販売業者名称', desc: '販売業者説明' },
      { index: 1, name: '代表責任者名称', desc: '代表責任者説明' },
      { index: 2, name: '所在地名称', desc: '所在地説明' },
      { index: 3, name: '電話番号名称', desc: '電話番号説明' },
      { index: 4, name: 'メールアドレス名称', desc: 'メールアドレス説明' },
      { index: 5, name: 'URL名称', desc: 'URL説明' },
    ];
    for (const e of entries) {
      await page.locator(`#form_TradeLaws_${e.index}_name`).fill(e.name);
      await page.locator(`#form_TradeLaws_${e.index}_description`).fill(e.desc);
    }

    // Save
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました', { timeout: 30_000 });

    // Verify on the front tradelaw page
    await page.goto('/help/tradelaw');
    await page.waitForLoadState('load');
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('特定商取引法に基づく表記');

    // Check that entries appear in dl elements
    const defs = page.locator('.ec-borderedDefs dl');
    await expect(defs.first()).toBeVisible();
    await expect(page.locator('.ec-borderedDefs')).toContainText('販売業者名称');
    await expect(page.locator('.ec-borderedDefs')).toContainText('販売業者説明');
  });

  test('basicinfo_agreement_settings - EA0703-UC01-T01', async ({ page }) => {
    // Navigate directly to the agreement page edit
    // Page ID 19 is the standard 利用規約 page
    await page.goto(`/${adminRoute}/content/page/19/edit`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('ページ管理');

    // Get current content from the hidden textarea
    const currentContent = await page.locator('#main_edit_tpl_data').inputValue();

    // Inject test text into the template
    const testText = 'テストテキスト_' + Date.now();
    const newContent = currentContent.replace(/<\/h1>/, `</h1>\n${testText}`);

    // Set value via Ace editor's underlying textarea and sync with Ace
    await page.evaluate(({ content }) => {
      const textarea = document.querySelector('#main_edit_tpl_data') as HTMLTextAreaElement;
      if (textarea) textarea.value = content;
      const editorEl = document.querySelector('.ace_editor') as any;
      if (editorEl && editorEl.env?.editor) {
        editorEl.env.editor.setValue(content);
      }
    }, { content: newContent });

    // Save
    await page.locator('.c-conversionArea button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました', { timeout: 30_000 });

    // Verify on the front agreement page
    await page.goto('/help/agreement');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-layoutRole__main')).toContainText(testText);

    // Restore original content
    await page.goto(`/${adminRoute}/content/page/19/edit`);
    await page.waitForLoadState('load');

    await page.evaluate(({ content }) => {
      const textarea = document.querySelector('#main_edit_tpl_data') as HTMLTextAreaElement;
      if (textarea) textarea.value = content;
      const editorEl = document.querySelector('.ace_editor') as any;
      if (editorEl && editorEl.env?.editor) {
        editorEl.env.editor.setValue(content);
      }
    }, { content: currentContent });

    await page.locator('.c-conversionArea button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました', { timeout: 30_000 });
  });

  test('basicinfo_authentication_key - EA1101-UC01-T01', async ({ page }) => {
    // Navigate to authentication key settings
    await page.goto(`/${adminRoute}/store/plugin/authentication_setting`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('認証キー設定');

    // Input an authentication key
    await page.locator('#admin_authentication_authentication_key').fill('1111111111111111111111111111111111111111');

    // Submit
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました', { timeout: 30_000 });
  });

  test('basicinfo_ポイント設定_有効 - EA0701-UC01-T16', async ({ page }) => {
    test.setTimeout(300_000);

    const price = 2800;
    const pointRate = 2;
    const pointConversionRate = 5;
    const expectedPoint = Math.floor(price * pointRate / 100);
    const expectedPointText = `${expectedPoint.toLocaleString()} pt`;

    // Prepare product stock
    await page.goto(`/${adminRoute}/product/product/2/edit`);
    await page.waitForLoadState('load');

    await page.locator('#admin_product_class_price02').fill(String(price));
    await page.locator('#admin_product_class_stock').fill('1000');

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Enable point feature with specified rates
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');

    // Enable point feature checkbox
    const pointCheckbox = page.locator('#shop_master_option_point');
    if (!await pointCheckbox.isChecked()) {
      await page.locator('label[for="shop_master_option_point"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('#shop_master_basic_point_rate').fill(String(pointRate));
    await page.locator('#shop_master_point_conversion_rate').fill(String(pointConversionRate));

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Get customer email via admin
    await page.goto(`/${adminRoute}/customer/1/edit`);
    await page.waitForLoadState('load');
    const email = await page.locator('#admin_customer_email').inputValue();

    // We need to login as a customer -- use a new context for front
    const browser = page.context().browser()!;
    const frontContext = await browser.newContext();
    const frontPage = await frontContext.newPage();

    // Login as this customer on front
    await frontPage.goto('/mypage/login');
    await frontPage.waitForLoadState('load');
    await frontPage.locator('input[name="login_email"]').fill(email);
    await frontPage.locator('input[name="login_pass"]').fill('password');
    await frontPage.locator('#login_mypage button[type="submit"]').click();
    await frontPage.waitForLoadState('load');

    // Add product to cart
    await frontPage.goto('/products/detail/2');
    await frontPage.waitForLoadState('load');

    // Select quantity if needed, then add to cart
    await frontPage.locator('.ec-productRole__btn button[type="submit"]').first().click();
    await frontPage.waitForLoadState('load');

    // Go to cart and proceed to checkout
    await frontPage.goto('/cart');
    await frontPage.waitForLoadState('load');
    await frontPage.getByRole('link', { name: 'レジに進む' }).click();
    await frontPage.waitForLoadState('load');

    // Verify point on shopping page
    await expect(frontPage.locator('body')).toContainText('加算ポイント');

    // Proceed to confirm
    await frontPage.locator('#shopping-form button.ec-blockBtn--action').click();
    await frontPage.waitForLoadState('load');

    await expect(frontPage.locator('body')).toContainText('加算ポイント');

    // Complete order
    await frontPage.locator('#shopping-form button.ec-blockBtn--action').click();
    await frontPage.waitForLoadState('load');
    await expect(frontPage.locator('body')).toContainText('ご注文ありがとうございました');

    // Verify on mypage order history
    await frontPage.goto('/mypage');
    await frontPage.waitForLoadState('load');
    await frontPage.locator('.ec-historyRole .ec-historyListHeader__action a').first().click();
    await frontPage.waitForLoadState('load');
    await expect(frontPage.locator('body')).toContainText('加算ポイント');

    await frontContext.close();
  });

  test('basicinfo_ポイント設定_無効 - EA0701-UC01-T17', async ({ page }) => {
    test.setTimeout(180_000);

    // Disable point feature
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');

    // Re-login if session expired
    if (await page.locator('#login_id').count() > 0) {
      await page.locator('#login_id').fill(process.env.ADMIN_USER || 'admin');
      await page.locator('#password').fill(process.env.ADMIN_PASSWORD || 'password');
      await page.getByRole('button', { name: 'ログイン' }).click();
      await page.waitForLoadState('load');
      await page.goto(`/${adminRoute}/setting/shop`);
      await page.waitForLoadState('load');
    }

    const pointCheckbox = page.locator('#shop_master_option_point');
    if (await pointCheckbox.isChecked()) {
      await page.locator('label[for="shop_master_option_point"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Get customer email via admin
    await page.goto(`/${adminRoute}/customer/1/edit`);
    await page.waitForLoadState('load');
    const email = await page.locator('#admin_customer_email').inputValue();

    // Login as customer on front
    const browser = page.context().browser()!;
    const frontContext = await browser.newContext();
    const frontPage = await frontContext.newPage();

    await frontPage.goto('/mypage/login');
    await frontPage.waitForLoadState('load');
    await frontPage.locator('input[name="login_email"]').fill(email);
    await frontPage.locator('input[name="login_pass"]').fill('password');
    await frontPage.locator('#login_mypage button[type="submit"]').click();
    await frontPage.waitForLoadState('load');

    // Add product to cart
    await frontPage.goto('/products/detail/2');
    await frontPage.waitForLoadState('load');
    await frontPage.locator('.ec-productRole__btn button[type="submit"]').first().click();
    await frontPage.waitForLoadState('load');

    // Go to cart -> checkout
    await frontPage.goto('/cart');
    await frontPage.waitForLoadState('load');
    await frontPage.getByRole('link', { name: 'レジに進む' }).click();
    await frontPage.waitForLoadState('load');

    // Verify no point display on shopping page
    const shoppingBody = await frontPage.locator('body').textContent();
    expect(shoppingBody).not.toContain('加算ポイント');

    // Proceed to confirm
    await frontPage.locator('#shopping-form button.ec-blockBtn--action').click();
    await frontPage.waitForLoadState('load');
    const confirmBody = await frontPage.locator('body').textContent();
    expect(confirmBody).not.toContain('加算ポイント');

    // Complete order
    await frontPage.locator('#shopping-form button.ec-blockBtn--action').click();
    await frontPage.waitForLoadState('load');
    await expect(frontPage.locator('body')).toContainText('ご注文ありがとうございました');

    await frontContext.close();
  });

  test('basicinfo_会員設定_仮会員機能 - EA0701-UC01-T05/T06', async ({ page }) => {
    test.setTimeout(120_000);

    // Disable provisional member feature
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/setting/shop')) {
      await page.goto(`/${adminRoute}/setting/shop`);
      await page.waitForLoadState('load');
    }

    const activateCheckbox = page.locator('#shop_master_option_customer_activate');
    if (await activateCheckbox.isChecked()) {
      await page.locator('label[for="shop_master_option_customer_activate"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Register a new member on the front
    const email1 = `activate_off_${Date.now()}@example.com`;
    await page.goto('/entry');
    await page.waitForLoadState('load');
    await page.locator('#entry_name_name01').fill('姓');
    await page.locator('#entry_name_name02').fill('名');
    await page.locator('#entry_kana_kana01').fill('セイ');
    await page.locator('#entry_kana_kana02').fill('メイ');
    await page.locator('#entry_postal_code').fill('530-0001');
    await page.locator('#entry_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(500);
    await page.locator('#entry_address_addr01').fill('大阪市北区');
    await page.locator('#entry_address_addr02').fill('梅田');
    await page.locator('#entry_phone_number').fill('111-111-111');
    await page.locator('#entry_email_first').fill(email1);
    await page.locator('#entry_email_second').fill(email1);
    await page.locator('#entry_plain_password_first').fill('password1234');
    await page.locator('#entry_plain_password_second').fill('password1234');
    await page.locator('#entry_user_policy_check').check();
    await page.locator('button.ec-blockBtn--action[type="submit"]').click();
    await page.waitForLoadState('load');
    // Confirm page -> register
    await page.locator('button.ec-blockBtn--action[type="submit"]').click();
    await page.waitForLoadState('load');

    // Check in admin: should be 本会員 (because provisional is disabled)
    await page.goto(`/${adminRoute}/customer`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/customer')) {
      await page.goto(`/${adminRoute}/customer`);
      await page.waitForLoadState('load');
    }
    await page.locator('#admin_search_customer_multi').fill(email1);
    // Open detail search and filter by 本会員
    await page.locator('#search_form [data-bs-toggle="collapse"][href="#searchDetail"]').click();
    await page.locator('#searchDetail').waitFor({ state: 'visible' });
    // Uncheck 仮会員, keep 本会員 checked
    await page.locator('#admin_search_customer_customer_status_1').uncheck();
    await page.locator('#admin_search_customer_customer_status_2').check();
    await page.locator('#search_form .c-outsideBlock__contents button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#search_form > div.c-outsideBlock__contents.mb-5 > span')).toContainText('検索結果：1件が該当しました');

    // Re-enable provisional member feature
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');

    const activateCheckbox2 = page.locator('#shop_master_option_customer_activate');
    if (!await activateCheckbox2.isChecked()) {
      await page.locator('label[for="shop_master_option_customer_activate"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Register another member on the front
    const email2 = `activate_on_${Date.now()}@example.com`;
    // Logout front first
    await page.goto('/logout');
    await page.waitForLoadState('load');

    await page.goto('/entry');
    await page.waitForLoadState('load');
    await page.locator('#entry_name_name01').fill('姓');
    await page.locator('#entry_name_name02').fill('名');
    await page.locator('#entry_kana_kana01').fill('セイ');
    await page.locator('#entry_kana_kana02').fill('メイ');
    await page.locator('#entry_postal_code').fill('530-0001');
    await page.locator('#entry_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(500);
    await page.locator('#entry_address_addr01').fill('大阪市北区');
    await page.locator('#entry_address_addr02').fill('梅田');
    await page.locator('#entry_phone_number').fill('111-111-111');
    await page.locator('#entry_email_first').fill(email2);
    await page.locator('#entry_email_second').fill(email2);
    await page.locator('#entry_plain_password_first').fill('password1234');
    await page.locator('#entry_plain_password_second').fill('password1234');
    await page.locator('#entry_user_policy_check').check();
    await page.locator('button.ec-blockBtn--action[type="submit"]').click();
    await page.waitForLoadState('load');
    await page.locator('button.ec-blockBtn--action[type="submit"]').click();
    await page.waitForLoadState('load');

    // Check in admin: should be 仮会員 (because provisional is enabled)
    await page.goto(`/${adminRoute}/customer`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/customer')) {
      await page.goto(`/${adminRoute}/customer`);
      await page.waitForLoadState('load');
    }
    await page.locator('#admin_search_customer_multi').fill(email2);
    await page.locator('#search_form [data-bs-toggle="collapse"][href="#searchDetail"]').click();
    await page.locator('#searchDetail').waitFor({ state: 'visible' });
    // Uncheck 本会員, keep 仮会員 checked
    await page.locator('#admin_search_customer_customer_status_2').uncheck();
    await page.locator('#admin_search_customer_customer_status_1').check();
    await page.locator('#search_form .c-outsideBlock__contents button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#search_form > div.c-outsideBlock__contents.mb-5 > span')).toContainText('検索結果：1件が該当しました');
  });

  test('basicinfo_会員設定_マイページ注文状況 - EA0701-UC01-T07/T08', async ({ page }) => {
    test.setTimeout(180_000);

    // Get customer email
    await page.goto(`/${adminRoute}/customer/1/edit`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/customer/1/edit')) {
      await page.goto(`/${adminRoute}/customer/1/edit`);
      await page.waitForLoadState('load');
    }
    const email = await page.locator('#admin_customer_email').inputValue();

    // Disable mypage order status display
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');

    const orderStatusCheckbox = page.locator('#shop_master_option_mypage_order_status_display');
    if (await orderStatusCheckbox.isChecked()) {
      await page.locator('label[for="shop_master_option_mypage_order_status_display"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Login as member and check mypage
    const browser = page.context().browser()!;
    const frontContext = await browser.newContext();
    const frontPage = await frontContext.newPage();

    await frontPage.goto('/mypage/login');
    await frontPage.waitForLoadState('load');
    await frontPage.locator('input[name="login_email"]').fill(email);
    await frontPage.locator('input[name="login_pass"]').fill('password');
    await frontPage.locator('#login_mypage button[type="submit"]').click();
    await frontPage.waitForLoadState('load');

    // Go to order history
    await frontPage.goto('/mypage');
    await frontPage.waitForLoadState('load');
    // Click on the first order detail if available
    const orderLink = frontPage.locator('.ec-historyRole .ec-historyListHeader__action a').first();
    if (await orderLink.count() > 0) {
      await orderLink.click();
      await frontPage.waitForLoadState('load');
      // 'ご注文状況' should NOT be visible
      const bodyText = await frontPage.locator('.ec-orderRole').first().textContent() || '';
      expect(bodyText).not.toContain('ご注文状況');
    }

    await frontContext.close();

    // Re-enable mypage order status display
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/setting/shop')) {
      await page.goto(`/${adminRoute}/setting/shop`);
      await page.waitForLoadState('load');
    }

    const orderStatusCheckbox2 = page.locator('#shop_master_option_mypage_order_status_display');
    if (!await orderStatusCheckbox2.isChecked()) {
      await page.locator('label[for="shop_master_option_mypage_order_status_display"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Login as member and check again
    const frontContext2 = await browser.newContext();
    const frontPage2 = await frontContext2.newPage();

    await frontPage2.goto('/mypage/login');
    await frontPage2.waitForLoadState('load');
    await frontPage2.locator('input[name="login_email"]').fill(email);
    await frontPage2.locator('input[name="login_pass"]').fill('password');
    await frontPage2.locator('#login_mypage button[type="submit"]').click();
    await frontPage2.waitForLoadState('load');

    await frontPage2.goto('/mypage');
    await frontPage2.waitForLoadState('load');
    const orderLink2 = frontPage2.locator('.ec-historyRole .ec-historyListHeader__action a').first();
    if (await orderLink2.count() > 0) {
      await orderLink2.click();
      await frontPage2.waitForLoadState('load');
      await expect(frontPage2.locator('.ec-orderRole').first()).toContainText('ご注文状況');
    }

    await frontContext2.close();
  });

  test('basicinfo_会員設定_自動ログイン - EA0701-UC01-T11/T12', async ({ page }) => {
    test.setTimeout(120_000);

    // Disable auto-login feature
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/setting/shop')) {
      await page.goto(`/${adminRoute}/setting/shop`);
      await page.waitForLoadState('load');
    }

    const rememberMeCheckbox = page.locator('#shop_master_option_remember_me');
    if (await rememberMeCheckbox.isChecked()) {
      await page.locator('label[for="shop_master_option_remember_me"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Verify the auto-login checkbox is NOT visible on the front login page
    await page.goto('/mypage/login');
    await page.waitForLoadState('load');
    const loginFormText = await page.locator('#login_mypage').textContent() || '';
    expect(loginFormText).not.toContain('次回から自動的にログインする');

    // Re-enable auto-login feature
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/setting/shop')) {
      await page.goto(`/${adminRoute}/setting/shop`);
      await page.waitForLoadState('load');
    }

    const rememberMeCheckbox2 = page.locator('#shop_master_option_remember_me');
    if (!await rememberMeCheckbox2.isChecked()) {
      await page.locator('label[for="shop_master_option_remember_me"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Verify the auto-login checkbox IS visible on the front login page
    await page.goto('/mypage/login');
    await page.waitForLoadState('load');
    await expect(page.locator('#login_mypage')).toContainText('次回から自動的にログインする');
  });

  test('basicinfo_税設定_商品別税率 - EA0701-UC01-T15', async ({ page }) => {
    // Enable product-specific tax rate
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/setting/shop')) {
      await page.goto(`/${adminRoute}/setting/shop`);
      await page.waitForLoadState('load');
    }

    const taxRuleCheckbox = page.locator('#shop_master_option_product_tax_rule');
    if (!await taxRuleCheckbox.isChecked()) {
      await page.locator('label[for="shop_master_option_product_tax_rule"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Verify tax rate field is visible on product edit page
    await page.goto(`/${adminRoute}/product/product/new`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea')).toContainText('税率');
    await expect(page.locator('#admin_product_class_tax_rate')).toBeVisible();

    // Disable product-specific tax rate
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');

    const taxRuleCheckbox2 = page.locator('#shop_master_option_product_tax_rule');
    if (await taxRuleCheckbox2.isChecked()) {
      await page.locator('label[for="shop_master_option_product_tax_rule"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Verify tax rate field is NOT visible on product edit page
    await page.goto(`/${adminRoute}/product/product/new`);
    await page.waitForLoadState('load');
    const contentText = await page.locator('.c-contentsArea').textContent() || '';
    expect(contentText).not.toContain('税率');
    await expect(page.locator('#admin_product_class_tax_rate')).not.toBeVisible();
  });

  test('basicinfo_メール設定_テンプレート新規作成_削除 - EA0709-UC02-T02', async ({ page }) => {
    test.setTimeout(120_000);

    const templateId = Date.now().toString();
    const templateName = 'template_' + templateId;
    const fileName = 'filename_' + templateId;
    const mailSubject = 'subject_' + templateId;
    const mailText = 'テスト本文_' + templateId;
    const mailHtml = '<p>HTML本文</p>' + templateId;

    // Navigate to mail settings page
    await page.goto(`/${adminRoute}/setting/shop/mail`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/setting/shop/mail')) {
      await page.goto(`/${adminRoute}/setting/shop/mail`);
      await page.waitForLoadState('load');
    }
    await expect(page.locator('.c-pageTitle')).toContainText('メール設定');

    // Fill new template fields
    await page.locator('#mail_name').fill(templateName);
    await page.locator('#mail_file_name').fill(fileName);
    await page.locator('#mail_mail_subject').fill(mailSubject);

    // Fill text body (textarea in the ace editor wrapper)
    await page.evaluate((text) => {
      const textarea = document.querySelector('#editor textarea') as HTMLTextAreaElement;
      if (textarea) textarea.value = text;
      // Also try the ace editor
      const editorEl = document.querySelector('#editor .ace_editor') as any;
      if (editorEl && editorEl.env?.editor) {
        editorEl.env.editor.setValue(text);
      }
    }, mailText);

    // Fill HTML body
    await page.evaluate((html) => {
      const textarea = document.querySelector('#html_editor textarea') as HTMLTextAreaElement;
      if (textarea) textarea.value = html;
      const editorEl = document.querySelector('#html_editor .ace_editor') as any;
      if (editorEl && editorEl.env?.editor) {
        editorEl.env.editor.setValue(html);
      }
    }, mailHtml);

    // Save
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Verify the template was created by selecting it
    await page.goto(`/${adminRoute}/setting/shop/mail`);
    await page.waitForLoadState('load');

    // Select the newly created template
    await page.locator('#mail_template').selectOption({ label: templateName });
    await page.waitForTimeout(2000);

    // Verify subject
    await expect(page.locator('#mail_mail_subject')).toHaveValue(mailSubject);

    // Delete the template
    await page.locator('button[data-bs-target="#deleteModal"]').click();
    await page.waitForTimeout(500);
    await page.locator('#deleteModal').waitFor({ state: 'visible' });
    await page.locator('#deleteModal .modal-footer a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('削除しました');
  });

  test('basicinfo_お気に入り - EA0701-UC01-T09/T10', async ({ page }) => {
    // Disable favorite product feature
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/setting/shop')) {
      await page.goto(`/${adminRoute}/setting/shop`);
      await page.waitForLoadState('load');
    }

    const favCheckbox = page.locator('#shop_master_option_favorite_product');
    if (await favCheckbox.isChecked()) {
      await page.locator('label[for="shop_master_option_favorite_product"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Verify on front: no favorite in header
    await page.goto('/');
    await page.waitForLoadState('load');
    const headerText = await page.locator('.ec-headerNav').textContent();
    expect(headerText).not.toContain('お気に入り');

    // Verify on product detail: no favorite button
    await page.goto('/products/detail/1');
    await page.waitForLoadState('load');
    const btnArea = await page.locator('.ec-productRole__btn').textContent();
    expect(btnArea).not.toContain('お気に入りに追加');

    // Re-enable favorite product feature
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');

    const favCheckbox2 = page.locator('#shop_master_option_favorite_product');
    if (!await favCheckbox2.isChecked()) {
      await page.locator('label[for="shop_master_option_favorite_product"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Verify on front: favorite in header
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-headerNav')).toContainText('お気に入り');

    // Verify on product detail: favorite button present
    await page.goto('/products/detail/1');
    await page.waitForLoadState('load');
    await expect(page.locator('#favorite')).toContainText('お気に入りに追加');
  });

  test('basicinfo_税設定_適格請求書発行事業者登録番号 - EA0713-UC01-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/setting/shop')) {
      await page.goto(`/${adminRoute}/setting/shop`);
      await page.waitForLoadState('load');
    }

    await page.locator('#shop_master_company_name').fill('サンプル会社名');
    await page.locator('#shop_master_shop_name').fill('サンプルショップ');
    await page.locator('#shop_master_postal_code').fill('100-0001');
    await page.locator('#shop_master_phone_number').fill('050-5555-5555');
    await page.locator('#shop_master_invoice_registration_number').fill('T1234567890123');

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Verify the invoice registration number was saved
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');
    await expect(page.locator('#shop_master_invoice_registration_number')).toHaveValue('T1234567890123');
  });

  test('basicinfo_GAタグ設定 - EA0714-UC01-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/setting/shop')) {
      await page.goto(`/${adminRoute}/setting/shop`);
      await page.waitForLoadState('load');
    }

    await page.locator('#shop_master_ga_id').fill('UA-12345678-1');

    await page.waitForTimeout(1000);

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Verify GA tag is embedded in front page source
    await page.goto('/');
    await page.waitForLoadState('load');

    const pageContent = await page.content();
    expect(pageContent).toContain('https://www.googletagmanager.com/gtag/js?id=UA-12345678-1');

    // Clean up: remove GA tag
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');
    await page.locator('#shop_master_ga_id').fill('');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('basicinfo_商品設定_在庫切れ商品の非表示 - EA0701-UC01-T13/T14', async ({ page }) => {
    // First, set a product to have 0 stock
    // Navigate directly to the product edit page (product ID 2 = チェリーアイスサンド)
    await page.goto(`/${adminRoute}/product/product/2/edit`);
    await page.waitForLoadState('load');
    await ensureAdminLoggedIn(page);
    if (!page.url().includes('/product/product/2/edit')) {
      await page.goto(`/${adminRoute}/product/product/2/edit`);
      await page.waitForLoadState('load');
    }

    await page.locator('#admin_product_class_stock').fill('0');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Enable "hide out-of-stock products"
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');

    const nostockCheckbox = page.locator('#shop_master_option_nostock_hidden');
    if (!await nostockCheckbox.isChecked()) {
      await page.locator('label[for="shop_master_option_nostock_hidden"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Search on front for the product - should not be found
    await page.goto('/products/list?name=%E3%83%81%E3%82%A7%E3%83%AA%E3%83%BC%E3%82%A2%E3%82%A4%E3%82%B9%E3%82%B5%E3%83%B3%E3%83%89');
    await page.waitForLoadState('load');
    await expect(page.locator('body')).toContainText('お探しの商品は見つかりませんでした');

    // Disable "hide out-of-stock products"
    await page.goto(`/${adminRoute}/setting/shop`);
    await page.waitForLoadState('load');

    const nostockCheckbox2 = page.locator('#shop_master_option_nostock_hidden');
    if (await nostockCheckbox2.isChecked()) {
      await page.locator('label[for="shop_master_option_nostock_hidden"]').click();
      await page.waitForTimeout(500);
    }

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Search on front for the product - should be found
    await page.goto('/products/list?name=%E3%83%81%E3%82%A7%E3%83%AA%E3%83%BC%E3%82%A2%E3%82%A4%E3%82%B9%E3%82%B5%E3%83%B3%E3%83%89');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-shelfGrid')).toContainText('チェリーアイスサンド');

    // Restore product stock
    await page.goto(`/${adminRoute}/product/product/2/edit`);
    await page.waitForLoadState('load');
    await page.locator('#admin_product_class_stock').fill('100');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('basicinfo_calendar_settings - EA0712-UC01-T01/T02', async ({ page }) => {
    // 前のテストが別コンテキストでログインした場合、セッションが切れている可能性
    await page.goto(`/${adminRoute}/setting/shop/calendar`);
    await page.waitForLoadState('load');
    if (page.url().includes('/login')) {
      await page.locator('#login_id').fill(process.env.ADMIN_USER || 'admin');
      await page.locator('#password').fill(process.env.ADMIN_PASSWORD || 'password');
      await page.getByRole('button', { name: 'ログイン' }).click();
      await page.waitForLoadState('load');
      await page.goto(`/${adminRoute}/setting/shop/calendar`);
      await page.waitForLoadState('load');
    }
    await expect(page.locator('.c-pageTitle')).toContainText('定休日カレンダー');

    // Use dates far enough in the future to avoid conflicts with existing holidays
    const now = new Date();
    const year = now.getFullYear() + 1; // next year to avoid conflicts
    const date1 = `${year}-01-15`;
    const date2 = `${year}-02-15`;
    const testHolidayTitle1 = '定休日テスト1';
    const testHolidayTitle2 = '定休日テスト2';

    // Delete only holidays created by this test (from a previous run) to avoid conflicts
    for (const title of [testHolidayTitle1, testHolidayTitle2]) {
      const row = page.locator(`table tbody tr:has-text("${title}")`);
      if (await row.count() > 0) {
        await row.locator('a[data-bs-toggle="modal"]').click();
        await page.waitForTimeout(500);
        await page.locator('.modal.show .btn-ec-delete').click();
        await page.waitForLoadState('load');
        await page.waitForTimeout(500);
      }
    }

    // Set holiday 1
    await page.locator('#calendar_item_new #calendar_title').fill(testHolidayTitle1);
    await page.evaluate((date) => {
      const el = document.querySelector('#calendar_item_new #calendar_holiday') as HTMLInputElement;
      if (el) el.value = date;
    }, date1);
    await page.locator('#calendar_item_new button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#page_admin_setting_shop_calendar .alert-success')).toContainText('保存しました');

    // Set holiday 2
    await page.goto(`/${adminRoute}/setting/shop/calendar`);
    await page.waitForLoadState('load');

    await page.locator('#calendar_item_new #calendar_title').fill(testHolidayTitle2);
    await page.evaluate((date) => {
      const el = document.querySelector('#calendar_item_new #calendar_holiday') as HTMLInputElement;
      if (el) el.value = date;
    }, date2);
    await page.locator('#calendar_item_new button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#page_admin_setting_shop_calendar .alert-success')).toContainText('保存しました');
  });
});
