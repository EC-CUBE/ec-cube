import { test, expect } from '@playwright/test';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
const pageTitle = '.c-pageTitle';
const searchResultMsg = '#search_form #search_total_count';
const searchErrorMsg = '.c-contentsArea__primaryCol .text-center';

async function goOrderList(page: import('@playwright/test').Page) {
  await page.goto(`/${adminRoute}/order`);
  await page.waitForLoadState('load');
}

async function searchOrder(page: import('@playwright/test').Page, keyword: string = '') {
  await page.locator('#admin_search_order_multi').fill(keyword);
  await page.locator('#search_form #search_submit').click();
  await page.waitForLoadState('load');
}

async function createOrderViaUI(page: import('@playwright/test').Page, name01: string, name02: string) {
  await page.goto(`/${adminRoute}/order/new`);
  await page.waitForLoadState('load');

  // Fill orderer information
  await page.locator('#order_Payment').selectOption({ index: 1 });
  await page.locator('#order_name_name01').fill(name01);
  await page.locator('#order_name_name02').fill(name02);
  await page.locator('#order_kana_kana01').fill('テスト');
  await page.locator('#order_kana_kana02').fill('タロウ');
  await page.locator('#order_postal_code').fill('060-0000');
  await page.locator('#order_address_pref').selectOption({ label: '北海道' });
  await page.locator('#order_address_addr01').fill('札幌市');
  await page.locator('#order_address_addr02').fill('1-1-1');
  await page.locator('#order_email').fill('test@test.com');
  await page.locator('#order_phone_number').fill('111-111-111');

  // Copy orderer info to shipping
  await page.locator('button.copy-customer').click();
  await page.waitForTimeout(500);

  // Select delivery company
  await page.locator('#order_Shipping_Delivery').selectOption({ index: 1 });
  await page.waitForTimeout(500);

  // Add a product (search for a specific product without class categories)
  await page.locator('#orderItem a.add').click();
  await page.waitForSelector('#addProduct', { state: 'visible' });
  await page.locator('#admin_search_product_id').fill('チェリーアイスサンド');
  await page.locator('#searchProductModalButton').click();
  await page.waitForSelector('#searchProductModalList table', { state: 'visible' });
  await page.locator('#searchProductModalList table button').first().click();
  await expect(page.locator('#addProduct')).not.toBeVisible({ timeout: 10000 });
  await page.waitForTimeout(500);

  // Submit the form (register button)
  await page.locator('#form1 button[value="register"]').click();
  await page.waitForLoadState('load');
  await page.waitForTimeout(3000);

  await expect(page.locator('.alert-success').first()).toContainText('保存しました');
}

test.describe('Admin Order (EA04)', () => {
  test.describe.configure({ mode: 'serial' });

  test('order_受注登録 (EA0405-UC01-T01)', async ({ page }) => {
    // Create an order via the new order form
    await page.goto(`/${adminRoute}/order/new`);
    await page.waitForLoadState('load');

    // Submit empty form -- should not show success (validation error expected)
    await page.locator('#form1 button[value="register"]').click();
    await page.waitForLoadState('load');
    await page.waitForTimeout(2000);
    // The success message should not appear in the content area
    await expect(page.locator('div.c-contentsArea > div.alert-success')).not.toBeVisible();

    // Fill correct data
    await page.locator('#order_Payment').selectOption({ index: 1 });
    await page.locator('#order_name_name01').fill('order1');
    await page.locator('#order_name_name02').fill('order1');
    await page.locator('#order_kana_kana01').fill('アアア');
    await page.locator('#order_kana_kana02').fill('アアア');
    await page.locator('#order_postal_code').fill('060-0000');
    await page.locator('#order_address_pref').selectOption({ label: '北海道' });
    await page.locator('#order_address_addr01').fill('bbb');
    await page.locator('#order_address_addr02').fill('bbb');
    await page.locator('#order_email').fill('test@test.com');
    await page.locator('#order_phone_number').fill('111-111-111');

    // Copy orderer info to shipping
    await page.locator('button.copy-customer').click();
    await page.waitForTimeout(500);

    // Select delivery company
    await page.locator('#order_Shipping_Delivery').selectOption({ index: 1 });
    await page.waitForTimeout(500);

    // Add a product: search for チェリーアイスサンド
    await page.locator('#orderItem a.add').click();
    await page.waitForSelector('#addProduct', { state: 'visible' });
    await page.locator('#admin_search_product_id').fill('チェリーアイスサンド');
    await page.locator('#searchProductModalButton').click();
    await page.waitForSelector('#searchProductModalList table', { state: 'visible' });
    // Select first product (no class categories, modal auto-closes)
    await page.locator('#searchProductModalList table button').first().click();
    await expect(page.locator('#addProduct')).not.toBeVisible({ timeout: 10000 });
    await page.waitForTimeout(500);

    // Submit the form
    await page.locator('#form1 button[value="register"]').click();
    await page.waitForLoadState('load');
    await page.waitForTimeout(3000);

    await expect(page.locator('.alert-success').first()).toContainText('保存しました');
  });

  test('order_受注編集 (EA0401-UC05)', async ({ page }) => {
    // Search for the order we just created
    await goOrderList(page);
    await searchOrder(page, 'order1');
    await expect(page.locator(searchResultMsg)).not.toContainText('検索結果：0件が該当しました');

    // Click edit on first row
    await page.locator('#search_result tbody tr:first-child a.action-edit').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('受注登録');

    // Open orderer panel (collapsed by default on edit page)
    await page.locator('a[href="#ordererInfo"]').click();
    await page.waitForTimeout(500);
    await expect(page.locator('#ordererInfo')).toBeVisible();

    // Clear name and submit -- should show validation error
    await page.locator('#order_name_name01').fill('');
    await page.locator('#form1 button[value="register"]').click();
    await page.waitForLoadState('load');
    await page.waitForTimeout(2000);

    // Error should be visible (入力されていません)
    await expect(page.locator('#ordererInfo')).toContainText('入力されていません');

    // Fill correct data and save
    await page.locator('#order_name_name01').fill('aaa');
    await page.locator('#order_kana_kana01').fill('アアア');
    await page.locator('#order_kana_kana02').fill('アアア');
    await page.locator('#order_postal_code').fill('060-0000');
    await page.locator('#order_address_pref').selectOption({ label: '北海道' });
    await page.locator('#order_address_addr01').fill('bbb');
    await page.locator('#order_address_addr02').fill('address 2');
    await page.locator('#order_phone_number').fill('111-111-111');
    await page.locator('#order_Payment').selectOption({ label: '郵便振替' });

    await page.locator('#form1 button[value="register"]').click();
    await page.waitForLoadState('load');
    await page.waitForTimeout(3000);

    await expect(page.locator('.alert-success').first()).toContainText('保存しました');

    // Change order status to 入金済み
    await page.locator('#order_OrderStatus').selectOption({ label: '入金済み' });
    await page.locator('#form1 button[value="register"]').click();
    await page.waitForLoadState('load');
    await page.waitForTimeout(3000);

    await expect(page.locator('.alert-success').first()).toContainText('保存しました');
  });

  test('order_受注検索 (EA0401-UC01-T01)', async ({ page }) => {
    // Search all orders
    await goOrderList(page);
    await searchOrder(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);
    await expect(page.locator(searchResultMsg)).not.toContainText('検索結果：0件が該当しました');

    // Search by name
    await goOrderList(page);
    await searchOrder(page, 'order1');
    await expect(page.locator(searchResultMsg)).not.toContainText('検索結果：0件が該当しました');

    // Search with no results
    await goOrderList(page);
    await searchOrder(page, 'gege@gege.com');
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：0件が該当しました');

    // Search with invalid phone number (detail search)
    await goOrderList(page);
    // Open detail search
    await page.locator('#search_form a:has(i)').first().click();
    await page.waitForTimeout(500);
    await expect(page.locator('#searchDetail')).toBeVisible();
    await page.locator('#admin_search_order_phone_number').fill('あああ');
    await page.locator('#search_form #search_submit').click();
    await page.waitForLoadState('load');
    await expect(page.locator(searchErrorMsg).first()).toContainText('検索条件に誤りがあります');
  });

  test('order_受注CSVダウンロード (EA0401-UC02-T01)', async ({ page }) => {
    await goOrderList(page);
    await searchOrder(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);
    await expect(page.locator(searchResultMsg)).not.toContainText('検索結果：0件が該当しました');

    // Click CSV download dropdown and download order CSV
    await page.locator('#csvDownloadDropDown').click();
    await page.waitForTimeout(500);
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.locator('#orderCsvDownload').click(),
    ]);
    expect(download.suggestedFilename()).toMatch(/^order_\d{14}\.csv$/);
  });

  test('order_受注情報のCSV出力項目変更設定 (EA0401-UC02-T02)', async ({ page }) => {
    await goOrderList(page);
    await searchOrder(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Click CSV setting dropdown and navigate to order CSV settings
    await page.locator('#csvSettingDropDown').click();
    await page.waitForTimeout(500);
    await page.locator('#orderCsvSetting').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('CSV出力項目設定');

    // Verify CSV type is 3 (order CSV)
    const csvTypeValue = await page.locator('#csv-type').inputValue();
    expect(csvTypeValue).toBe('3');
  });

  test('order_配送CSVダウンロード (EA0401-UC03-T01)', async ({ page }) => {
    await goOrderList(page);
    await searchOrder(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);
    await expect(page.locator(searchResultMsg)).not.toContainText('検索結果：0件が該当しました');

    // Click CSV download dropdown and download shipping CSV
    await page.locator('#csvDownloadDropDown').click();
    await page.waitForTimeout(500);
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.locator('#shippingCsvDownload').click(),
    ]);
    expect(download.suggestedFilename()).toMatch(/^shipping_\d{14}\.csv$/);
  });

  test('order_配送情報のCSV出力項目変更設定 (EA0401-UC03-T02)', async ({ page }) => {
    await goOrderList(page);
    await searchOrder(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Click CSV setting dropdown and navigate to shipping CSV settings
    await page.locator('#csvSettingDropDown').click();
    await page.waitForTimeout(500);
    await page.locator('#shippingCsvSetting').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('CSV出力項目設定');

    // Verify CSV type is 4 (shipping CSV)
    const csvTypeValue = await page.locator('#csv-type').inputValue();
    expect(csvTypeValue).toBe('4');
  });

  test('order_一覧でのソート (EA0401-UC09-T01)', async ({ page }) => {
    // Create a second order to have something to sort
    await createOrderViaUI(page, 'ソートテスト', '二郎');

    await goOrderList(page);
    await searchOrder(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);
    await expect(page.locator(searchResultMsg)).not.toContainText('検索結果：0件が該当しました');

    // Sort by order_status ascending
    await page.locator('a[data-sortkey="order_status"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.listSort-current[data-sortkey="order_status"] .fa-arrow-up')).toBeVisible();

    // Sort by order_status descending
    await page.locator('a[data-sortkey="order_status"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.listSort-current[data-sortkey="order_status"] .fa-arrow-down')).toBeVisible();

    // Sort by purchase_price ascending
    await page.locator('[data-sortkey="purchase_price"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.listSort-current[data-sortkey="purchase_price"] .fa-arrow-up')).toBeVisible();

    // Sort by purchase_price descending
    await page.locator('a[data-sortkey="purchase_price"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.listSort-current[data-sortkey="purchase_price"] .fa-arrow-down')).toBeVisible();
  });

  test('order_受注編集ページへの遷移', async ({ page }) => {
    await goOrderList(page);
    await searchOrder(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Click edit link on first row
    await page.locator('#search_result tbody tr:first-child a.action-edit').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('受注登録');
    expect(page.url()).toMatch(/\/admin\/order\/\d+\/edit$/);
  });

  test('order_受注削除 (EA0401-UC08-T01)', async ({ page }) => {
    // First create an order to delete, so we don't affect fixture data
    await createOrderViaUI(page, '削除テスト', '太郎');

    // Go to order list and search for it
    await goOrderList(page);
    await searchOrder(page, '削除テスト');
    await expect(page.locator(searchResultMsg)).not.toContainText('検索結果：0件が該当しました');

    // Get the order number for the first row
    const orderNum = await page.locator('#search_result tbody tr:first-child a.action-edit').textContent();

    // Select the first order
    await page.locator('#search_result > tbody > tr:nth-child(1) > td > input[type=checkbox]').check();
    await page.waitForTimeout(500);

    // The bulk wrapper should become visible -- but the delete button is in bulkDeleteModal
    // Open the delete modal via JS (there's no visible delete button in the bulk wrapper for orders,
    // but the modal exists)
    // Actually let's find the delete opener
    const deleteOpener = page.locator('[data-bs-target="#bulkDeleteModal"]');
    const openerCount = await deleteOpener.count();

    if (openerCount > 0) {
      await deleteOpener.click();
    } else {
      // If there's no opener button, trigger the modal directly
      await page.evaluate(() => {
        const modal = document.querySelector('#bulkDeleteModal') as HTMLElement;
        if (modal) {
          // @ts-ignore
          const bsModal = new bootstrap.Modal(modal);
          bsModal.show();
        }
      });
    }
    await page.waitForTimeout(500);

    // Click confirm delete
    await page.locator('#btn_bulk_delete').click();
    await page.waitForLoadState('load');
    await page.waitForTimeout(2000);

    // Verify the deleted order is no longer at the top
    await goOrderList(page);
    await searchOrder(page, '削除テスト');
    // After deletion, we expect 0 results for this search
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：0件が該当しました');
  });

  test('order_受注メール通知 (EA0402-UC01-T01)', async ({ page }) => {
    // Go to order list and search
    await goOrderList(page);
    await searchOrder(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Click the mail notification icon on the first row
    await page.locator('#search_result > tbody > tr:nth-child(1) > td.align-middle.pe-3 a.confirmationModal[data-type="mail"]').click();

    // Wait for the confirmation modal to appear
    await page.waitForSelector('#sentUpdateModal', { state: 'visible' });

    // Scroll to and click the bulk change button to send
    await page.locator('#bulkChange').scrollIntoViewIfNeeded();
    await page.locator('#bulkChange').click();

    // Wait for completion
    await page.waitForSelector('#bulkChangeComplete', { state: 'visible', timeout: 30_000 });

    // Verify the completion button is visible (mail was sent)
    await expect(page.locator('#bulkChangeComplete')).toBeVisible();
  });

  test('order_一括受注のステータス変更 (EA0405-UC06-T01)', async ({ page }) => {
    // First, create 2 orders with 新規受付 status
    await createOrderViaUI(page, 'ステータス変更テスト', '一郎');
    await createOrderViaUI(page, 'ステータス変更テスト', '二郎');

    // Search for these orders
    await goOrderList(page);
    await searchOrder(page, 'ステータス変更テスト');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Get initial count
    const initialCountText = await page.locator(searchResultMsg).textContent() || '';
    const initialMatch = initialCountText.match(/(\d+)件/);
    const initialCount = initialMatch ? parseInt(initialMatch[1]) : 0;
    expect(initialCount).toBeGreaterThanOrEqual(2);

    // Select all orders
    await page.locator('#toggle_check_all').check();
    await page.waitForTimeout(500);

    // Change status to 発送済み
    await page.locator('#option_bulk_status').selectOption({ label: '発送済み' });
    await page.locator('#btn_bulk_status').click();

    // The status change uses the sentUpdateModal but runs automatically (no #bulkChange click needed).
    // Wait for #bulkChangeComplete (the close/done button) to appear.
    await page.waitForSelector('#bulkChangeComplete', { state: 'visible', timeout: 30_000 });
    await expect(page.locator('#sentUpdateModal')).toContainText('完了しました');
  });

  test('order_個別出荷済みステータス変更 (EA0405-UC07-T01)', async ({ page }) => {
    // Create a new order to use for this test
    await createOrderViaUI(page, '出荷テスト', '太郎');

    // Search for the order
    await goOrderList(page);
    await searchOrder(page, '出荷テスト');
    await expect(page.locator(searchResultMsg)).not.toContainText('検索結果：0件が該当しました');

    // Click the shipped status button on the first row (data-type="status")
    await page.locator('#search_result > tbody > tr:nth-child(1) a[data-type="status"]').click();

    // Wait for the sentUpdateModal to appear
    await page.waitForSelector('#sentUpdateModal', { state: 'visible' });

    // Check the notification mail checkbox to send mail
    const notifMailCheckbox = page.locator('#notificationMail');
    if (await notifMailCheckbox.count() > 0) {
      await notifMailCheckbox.click();
    }

    // Click the bulk change button to execute
    await page.locator('#bulkChange').scrollIntoViewIfNeeded();
    await page.locator('#bulkChange').click();

    // Wait for completion
    await page.waitForSelector('#bulkChangeComplete', { state: 'visible', timeout: 30_000 });
    await expect(page.locator('#bulkChangeComplete')).toBeVisible();
  });

  test('order_一括メール通知 (EA0402-UC02-T01)', async ({ page }) => {
    // Go to order list and search all
    await goOrderList(page);
    await searchOrder(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);
    await expect(page.locator(searchResultMsg)).not.toContainText('検索結果：0件が該当しました');

    // Select all orders
    await page.locator('#toggle_check_all').check();
    await page.waitForTimeout(500);

    // Click bulk mail send button
    await page.locator('#bulkSendMail').click();

    // Wait for the sentUpdateModal to appear
    await page.waitForSelector('#sentUpdateModal', { state: 'visible' });

    // Click the send button
    await page.locator('#bulkChange').click();

    // Wait for completion
    await page.waitForSelector('#bulkChangeComplete', { state: 'visible', timeout: 30_000 });
    await expect(page.locator('#bulkChangeComplete')).toBeVisible();
  });

  test('order_一括メール通知_キャンセル (EA0402-UC02-T02)', async ({ page }) => {
    // Go to order list and search all
    await goOrderList(page);
    await searchOrder(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);
    await expect(page.locator(searchResultMsg)).not.toContainText('検索結果：0件が該当しました');

    // Select all orders
    await page.locator('#toggle_check_all').check();
    await page.waitForTimeout(500);

    // Click bulk mail send button
    await page.locator('#bulkSendMail').click();

    // Wait for the sentUpdateModal to appear
    await page.waitForSelector('#sentUpdateModal', { state: 'visible' });
    await page.waitForTimeout(500);

    // Click cancel button instead of send
    await page.locator('.modal.show .btn-ec-sub').click();

    // Verify the modal is closed
    await expect(page.locator('#sentUpdateModal')).not.toBeVisible({ timeout: 10_000 });

    // Verify we are still on the order list page (no mail was sent)
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);
  });

  test('order_納品書の出力 (EA0405-UC06-T02)', async ({ page }) => {
    // Go to order list and search all
    await goOrderList(page);
    await searchOrder(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);
    await expect(page.locator(searchResultMsg)).not.toContainText('検索結果：0件が該当しました');

    // Select all orders via the bulk form checkbox
    await page.locator('#form_bulk #toggle_check_all').check();
    await page.waitForTimeout(500);

    // Click the PDF export button - opens in a new window
    const [popup] = await Promise.all([
      page.waitForEvent('popup'),
      page.locator('#form_bulk #bulkExportPdf').click(),
    ]);

    await popup.waitForLoadState('load');

    // Verify we are on the PDF form page
    await expect(popup.locator('.c-pageTitle')).toContainText('納品書出力');

    // Click the download/export button
    const [download] = await Promise.all([
      popup.waitForEvent('download'),
      popup.locator('.btn-ec-conversion').click(),
    ]);

    expect(download.suggestedFilename()).toMatch(/nouhinsyo\.pdf$/);

    await popup.close();
  });

  test('order_納品書の一括出力 (EA0405-UC06-T03)', async ({ page }) => {
    // Go to order list and search all
    await goOrderList(page);
    await searchOrder(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);
    await expect(page.locator(searchResultMsg)).not.toContainText('検索結果：0件が該当しました');

    // Select all orders via the bulk form checkbox
    await page.locator('#form_bulk #toggle_check_all').check();
    await page.waitForTimeout(500);

    // Scroll to top to ensure the button is visible
    await page.evaluate(() => window.scrollTo(0, 0));
    await page.waitForTimeout(500);

    // Click the PDF export button - opens in a new window
    const [popup] = await Promise.all([
      page.waitForEvent('popup'),
      page.locator('#form_bulk #bulkExportPdf').click(),
    ]);

    await popup.waitForLoadState('load');

    // Verify we are on the PDF form page
    await expect(popup.locator('.c-pageTitle')).toContainText('納品書出力');

    // Fill in notes
    await popup.locator('#order_pdf_note1').fill('Test note first');
    await popup.locator('#order_pdf_note2').fill('Test note second');
    await popup.locator('#order_pdf_note3').fill('Test note third');

    // Check the default checkbox
    await popup.locator('#order_pdf_default').scrollIntoViewIfNeeded();
    await popup.waitForTimeout(500);
    await popup.locator('#order_pdf_default').click();

    // Click the download/export button
    const [download] = await Promise.all([
      popup.waitForEvent('download'),
      popup.locator('#order_pdf_form .c-conversionArea .justify-content-end button.btn-ec-conversion').click(),
    ]);

    expect(download.suggestedFilename()).toMatch(/nouhinsyo\.pdf$/);

    await popup.close();
  });
});
