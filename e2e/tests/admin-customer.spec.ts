import { test, expect } from '@playwright/test';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
const pageTitle = '.c-pageTitle';
const searchBtn = '#search_form .c-outsideBlock__contents button';
const searchResultMsg = '#search_form > div.c-outsideBlock__contents.mb-5 > span';
const noResultMsg = '.c-contentsArea .c-contentsArea__cols div.text-center.h5';
const successAlert = '.alert-success';

/** Navigate to the customer list page */
async function goCustomerList(page: import('@playwright/test').Page) {
  await page.goto(`/${adminRoute}/customer`);
  await page.waitForLoadState('load');
}

/** Perform a customer search with optional keyword */
async function searchCustomer(page: import('@playwright/test').Page, keyword: string = '') {
  await page.locator('#admin_search_customer_multi').fill(keyword);
  await page.locator(searchBtn).click();
  await page.waitForLoadState('load');
}

/** Generate a unique email address */
function uniqueEmail(): string {
  return `test+${Date.now()}@example.com`;
}

test.describe('Admin Customer (EA05)', () => {
  test.describe.configure({ mode: 'serial' });

  // Shared state for customer created by the registration test
  let createdCustomerEmail: string;

  test('customer_会員登録 - create a new customer via admin form', async ({ page }) => {
    createdCustomerEmail = uniqueEmail();

    await page.goto(`/${adminRoute}/customer/new`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('会員登録');

    // Fill required fields
    await page.locator('#admin_customer_name_name01').fill('テスト');
    await page.locator('#admin_customer_name_name02').fill('ユーザー');
    await page.locator('#admin_customer_kana_kana01').fill('テスト');
    await page.locator('#admin_customer_kana_kana02').fill('ユーザー');
    await page.locator('#admin_customer_postal_code').fill('5300001');
    await page.locator('#admin_customer_address_pref').selectOption({ value: '27' });
    await page.locator('#admin_customer_address_addr01').fill('大阪市北区梅田2-4-9');
    await page.locator('#admin_customer_address_addr02').fill('ブリーゼタワー13F');
    await page.locator('#admin_customer_email').fill(createdCustomerEmail);
    await page.locator('#admin_customer_phone_number').fill('111111111');
    await page.locator('#admin_customer_plain_password_first').fill('password1234');
    await page.locator('#admin_customer_plain_password_second').fill('password1234');

    // Submit
    await page.locator('#customer_form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator(successAlert)).toContainText('保存しました');
  });

  test('customer_検索 - search for the created customer', async ({ page }) => {
    await goCustomerList(page);
    await searchCustomer(page, createdCustomerEmail);

    await expect(page.locator(searchResultMsg)).toContainText('検索結果：1件が該当しました');
  });

  test('customer_検索結果なし - search yields no results', async ({ page }) => {
    const bogusEmail = `notexist_${Date.now()}@example.com`;
    await goCustomerList(page);
    await searchCustomer(page, bogusEmail);

    await expect(page.locator(noResultMsg)).toContainText('検索条件に合致するデータが見つかりませんでした');
  });

  test('customer_検索エラー - search with invalid phone number', async ({ page }) => {
    await goCustomerList(page);

    // Open detailed search
    await page.locator('#search_form [data-bs-toggle="collapse"][href="#searchDetail"]').click();
    await page.locator('#searchDetail').waitFor({ state: 'visible' });

    // Fill invalid phone number
    await page.locator('#admin_search_customer_phone_number').fill('あああ');
    await page.locator(searchBtn).click();
    await page.waitForLoadState('load');

    await expect(page.locator(noResultMsg)).toContainText('検索条件に誤りがあります');
  });

  test('customer_CSV出力 - CSV download from customer list', async ({ page }) => {
    await goCustomerList(page);
    await searchCustomer(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Click CSV download link
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.locator(`a[href*="/customer/export"]`).click(),
    ]);
    expect(download.suggestedFilename()).toMatch(/^customer_\d{14}\.csv$/);
  });

  test('customer_一覧でのソート - sort the customer list', async ({ page }) => {
    await goCustomerList(page);
    await searchCustomer(page, '');

    // ID ascending
    await page.locator('[data-sortkey="customer_id"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.listSort-current[data-sortkey="customer_id"] .fa-arrow-up')).toBeVisible();

    // Verify ID column is sorted ascending
    const idsAsc = await page.locator('.c-contentsArea__primaryCol tr > td:nth-child(1)').allTextContents();
    const idsAscNum = idsAsc.map(s => parseInt(s.trim(), 10)).filter(n => !isNaN(n));
    const sortedAsc = [...idsAscNum].sort((a, b) => a - b);
    expect(idsAscNum).toEqual(sortedAsc);

    // ID descending
    await page.locator('[data-sortkey="customer_id"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.listSort-current[data-sortkey="customer_id"] .fa-arrow-down')).toBeVisible();

    const idsDesc = await page.locator('.c-contentsArea__primaryCol tr > td:nth-child(1)').allTextContents();
    const idsDescNum = idsDesc.map(s => parseInt(s.trim(), 10)).filter(n => !isNaN(n));
    const sortedDesc = [...idsDescNum].sort((a, b) => b - a);
    expect(idsDescNum).toEqual(sortedDesc);

    // Name ascending
    await page.locator('[data-sortkey="name"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.listSort-current[data-sortkey="name"] .fa-arrow-up')).toBeVisible();

    // Name descending
    await page.locator('[data-sortkey="name"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.listSort-current[data-sortkey="name"] .fa-arrow-down')).toBeVisible();
  });

  test('customer_会員編集 - edit the created customer', async ({ page }) => {
    await goCustomerList(page);
    await searchCustomer(page, createdCustomerEmail);
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：1件が該当しました');

    // Click on the first customer name link to edit
    await page.locator('#search_form table tbody tr:first-child td:nth-child(2) a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('会員登録');

    // Edit the last name
    await page.locator('#admin_customer_name_name01').fill('テスト編集済');

    // Submit
    await page.locator('#customer_form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator(successAlert)).toContainText('保存しました');
  });

  test('customer_会員削除 - delete the created customer', async ({ page }) => {
    await goCustomerList(page);
    await searchCustomer(page, createdCustomerEmail);
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：1件が該当しました');

    // Click the delete icon (X button) in the first row
    await page.locator('#search_form table tbody tr:first-child td.align-middle.pe-3 a[data-bs-toggle="modal"]').last().click();
    await page.waitForTimeout(500);

    // Confirm deletion in modal
    await page.locator('.modal.show a.btn-ec-delete').click();
    await page.waitForLoadState('load');

    // After deletion, the search should show no results
    await expect(page.locator(noResultMsg)).toContainText('検索条件に合致するデータが見つかりませんでした');
  });

  test('customer_会員編集_注文履歴あり - edit customer with order history', async ({ page }) => {
    // Go to customer list
    await goCustomerList(page);
    await searchCustomer(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Find a customer that has orders by checking each customer edit page
    const rows = await page.locator('#search_form table tbody tr').count();
    let foundCustomerWithOrders = false;

    for (let i = 1; i <= rows; i++) {
      const href = await page.locator(`#search_form table tbody tr:nth-child(${i}) td:nth-child(2) a`).getAttribute('href');
      if (!href) continue;

      // Navigate to the customer edit page
      await page.goto(href);
      await page.waitForLoadState('load');

      // Check if there is order history
      const historyBox = await page.locator('#orderHistory').innerHTML();
      if (!historyBox.includes('購入履歴がありません')) {
        foundCustomerWithOrders = true;

        // Verify the order history section is present and has links
        await expect(page.locator('#orderHistory')).toBeVisible();
        const orderLinks = await page.locator('#orderHistory a[href*="/admin/order/"]').count();
        expect(orderLinks).toBeGreaterThan(0);

        // Edit the customer name
        await page.locator('#admin_customer_name_name01').fill('注文履歴テスト');

        // Submit
        await page.locator('#customer_form .c-conversionArea button[type="submit"]').click();
        await page.waitForLoadState('load');
        await expect(page.locator(successAlert)).toContainText('保存しました');
        break;
      }

      // Go back to customer list
      await goCustomerList(page);
      await searchCustomer(page, '');
    }

    expect(foundCustomerWithOrders).toBeTruthy();
  });

  test('customer_会員削除キャンセル - cancel deletion in modal', async ({ page }) => {
    // First create a customer to test with
    const cancelTestEmail = `cancel_${Date.now()}@example.com`;
    await page.goto(`/${adminRoute}/customer/new`);
    await page.waitForLoadState('load');
    await page.locator('#admin_customer_name_name01').fill('削除キャンセル');
    await page.locator('#admin_customer_name_name02').fill('テスト');
    await page.locator('#admin_customer_kana_kana01').fill('サクジョキャンセル');
    await page.locator('#admin_customer_kana_kana02').fill('テスト');
    await page.locator('#admin_customer_postal_code').fill('5300001');
    await page.locator('#admin_customer_address_pref').selectOption({ value: '27' });
    await page.locator('#admin_customer_address_addr01').fill('大阪市北区');
    await page.locator('#admin_customer_address_addr02').fill('梅田');
    await page.locator('#admin_customer_email').fill(cancelTestEmail);
    await page.locator('#admin_customer_phone_number').fill('111111111');
    await page.locator('#admin_customer_plain_password_first').fill('password1234');
    await page.locator('#admin_customer_plain_password_second').fill('password1234');
    await page.locator('#customer_form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator(successAlert)).toContainText('保存しました');

    // Search for the customer
    await goCustomerList(page);
    await searchCustomer(page, cancelTestEmail);
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：1件が該当しました');

    // Get the customer ID before deletion attempt
    const customerIdBefore = await page.locator('#search_form table tbody tr:first-child td:nth-child(1)').textContent();

    // Click the delete icon to open modal
    await page.locator('#search_form table tbody tr:first-child td.align-middle.pe-3 a[data-bs-toggle="modal"]').last().click();
    await page.waitForTimeout(500);
    await expect(page.locator('.modal.show')).toBeVisible();

    // Click cancel button in modal
    await page.locator('.modal.show .modal-footer button[data-bs-dismiss="modal"]').click();
    await page.waitForTimeout(500);

    // Verify the customer is still in the list
    const customerIdAfter = await page.locator('#search_form table tbody tr:first-child td:nth-child(1)').textContent();
    expect(customerIdAfter).toBe(customerIdBefore);
  });

  test('customer_会員登録_必須項目未入力 - submit empty form shows validation', async ({ page }) => {
    await page.goto(`/${adminRoute}/customer/new`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('会員登録');

    // Submit empty form
    await page.locator('#customer_form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');
    await page.waitForTimeout(500);

    // The name01 field should be invalid (HTML5 validation)
    const isInvalid = await page.locator('#admin_customer_name_name01').evaluate(
      (el: HTMLInputElement) => !el.checkValidity()
    );
    expect(isInvalid).toBeTruthy();

    // Success message should not be shown
    await expect(page.locator(successAlert)).not.toBeVisible();
  });

  test('customer_会員編集_必須項目未入力 - edit with empty required fields', async ({ page }) => {
    // Create a customer to edit
    const editTestEmail = `editempty_${Date.now()}@example.com`;
    await page.goto(`/${adminRoute}/customer/new`);
    await page.waitForLoadState('load');
    await page.locator('#admin_customer_name_name01').fill('編集空テスト');
    await page.locator('#admin_customer_name_name02').fill('ユーザー');
    await page.locator('#admin_customer_kana_kana01').fill('ヘンシュウカラ');
    await page.locator('#admin_customer_kana_kana02').fill('テスト');
    await page.locator('#admin_customer_postal_code').fill('5300001');
    await page.locator('#admin_customer_address_pref').selectOption({ value: '27' });
    await page.locator('#admin_customer_address_addr01').fill('大阪市北区');
    await page.locator('#admin_customer_address_addr02').fill('梅田');
    await page.locator('#admin_customer_email').fill(editTestEmail);
    await page.locator('#admin_customer_phone_number').fill('111111111');
    await page.locator('#admin_customer_plain_password_first').fill('password1234');
    await page.locator('#admin_customer_plain_password_second').fill('password1234');
    await page.locator('#customer_form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator(successAlert)).toContainText('保存しました');

    // Search and edit
    await goCustomerList(page);
    await searchCustomer(page, editTestEmail);
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：1件が該当しました');
    await page.locator('#search_form table tbody tr:first-child td:nth-child(2) a').click();
    await page.waitForLoadState('load');

    // Clear the name field (required)
    await page.locator('#admin_customer_name_name01').fill('');

    // Submit
    await page.locator('#customer_form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');
    await page.waitForTimeout(500);

    // The name01 field should be invalid (HTML5 validation)
    const isInvalid = await page.locator('#admin_customer_name_name01').evaluate(
      (el: HTMLInputElement) => !el.checkValidity()
    );
    expect(isInvalid).toBeTruthy();

    // Success message should not be shown
    await expect(page.locator(successAlert)).not.toBeVisible();
  });

  test('customer_CSV出力項目設定 - navigate to CSV settings page', async ({ page }) => {
    await goCustomerList(page);
    await searchCustomer(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Click CSV出力項目設定 link (navigates to /admin/setting/shop/csv/2)
    await page.locator('a[href*="/setting/shop/csv/2"]').click();
    await page.waitForLoadState('load');

    // Verify we are on the CSV settings page
    await expect(page.locator(pageTitle)).toContainText('CSV出力項目設定');

    // Verify CSV type is 2 (customer CSV)
    const csvTypeValue = await page.locator('#csv-type').inputValue();
    expect(csvTypeValue).toBe('2');
  });

  test('customer_仮会員メール再送 - resend provisional member email', async ({ page }) => {
    // Search for non-active (仮会員) customers
    await goCustomerList(page);

    // Open detail search
    await page.locator('#search_form [data-bs-toggle="collapse"][href="#searchDetail"]').click();
    await page.locator('#searchDetail').waitFor({ state: 'visible' });
    await page.waitForTimeout(500);

    // Uncheck 本会員 (status 2), keep 仮会員 (status 1) checked
    await page.locator('#admin_search_customer_customer_status_2').uncheck();

    // Search
    await page.locator(searchBtn).click();
    await page.waitForLoadState('load');

    // Check if there are any 仮会員 results
    const resultText = await page.locator(searchResultMsg).textContent() || '';
    if (resultText.includes('0件') || resultText.includes('見つかりませんでした')) {
      test.skip(true, 'No non-active customers found, skipping resend mail test');
      return;
    }

    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Click the 仮会員メール再送 button on the first row
    const mailResendBtn = page.locator('#search_form table tbody tr:first-child td.align-middle.pe-3 a[data-bs-toggle="modal"]').first();
    await mailResendBtn.click();
    await page.waitForTimeout(500);

    // The modal should be visible
    await expect(page.locator('.modal.show')).toBeVisible();
    await expect(page.locator('.modal.show .modal-title')).toContainText('仮会員メールを再送します');

    // First cancel to test the cancel flow (use the キャンセル button in the footer)
    await page.locator('.modal.show .modal-footer button[data-bs-dismiss="modal"]').click();
    await page.waitForTimeout(500);
    await expect(page.locator('.modal.show')).not.toBeVisible();

    // Now actually send: click mail resend again
    await mailResendBtn.click();
    await page.waitForTimeout(500);
    await expect(page.locator('.modal.show')).toBeVisible();

    // Click send button
    await page.locator('.modal.show a.btn-ec-delete').click();
    await page.waitForLoadState('load');

    // Verify we stayed on or returned to the customer list page (no error)
    // The page should not show an error
    await expect(page.locator('.c-pageTitle')).toContainText('会員');
  });
});
