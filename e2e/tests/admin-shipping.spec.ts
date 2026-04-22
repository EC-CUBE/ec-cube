import { test, expect } from '@playwright/test';
import path from 'path';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';

test.describe('Admin Shipping (EA09)', () => {

  test('shipping_csv_template_download - EA0903-UC04-T03', async ({ page }) => {
    // Navigate to shipping CSV upload page
    await page.goto(`/${adminRoute}/order/shipping_csv_upload`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('出荷CSV登録');

    // Download template
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.locator('#download-button').click(),
    ]);

    expect(download.suggestedFilename()).toMatch(/^shipping\.csv$/);

    // Save and verify content
    const filePath = await download.path();
    expect(filePath).toBeTruthy();
  });

  test('shipping_edit - EA0901-UC03-T01', async ({ page }) => {
    // Navigate to order list and search for all orders
    await page.goto(`/${adminRoute}/order`);
    await page.waitForLoadState('load');
    await page.locator('#search_form .c-outsideBlock__contents button').first().click();
    await page.waitForLoadState('load');
    await page.waitForTimeout(1000);

    // Get the first order's edit link
    const firstOrderLink = page.locator('table tbody td a[href*="/order/"]').first();
    await expect(firstOrderLink).toBeVisible();
    const orderEditHref = await firstOrderLink.getAttribute('href');

    // Navigate to order edit page
    await page.goto(orderEditHref!);
    await page.waitForLoadState('load');

    // Find the shipping edit link
    const shippingEditLink = page.locator('a[href*="/shipping/"][href*="/edit"]');
    await expect(shippingEditLink).toBeVisible();
    const shippingEditHref = await shippingEditLink.getAttribute('href');

    // Navigate to shipping edit page
    await page.goto(shippingEditHref!);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('出荷登録');

    // Edit tracking number
    const trackingNumber = 'TEST-TRACK-' + Date.now();
    await page.locator('#form_shippings_0_tracking_number').fill(trackingNumber);

    // Submit
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました', { timeout: 30_000 });

    // Verify the tracking number was saved
    await expect(page.locator('#form_shippings_0_tracking_number')).toHaveValue(trackingNumber);
  });

  test('shipping_csv_upload_page_display - EA0903-UC04', async ({ page }) => {
    // Verify the shipping CSV upload page is accessible and has correct elements
    await page.goto(`/${adminRoute}/order/shipping_csv_upload`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('出荷CSV登録');

    // Verify upload form exists
    await expect(page.locator('#upload-form')).toBeVisible();
    // File input may be visually hidden by CSS styling, check it's attached
    await expect(page.locator('#admin_csv_import_import_file')).toBeAttached();
    await expect(page.locator('#upload-button')).toBeVisible();
    await expect(page.locator('#download-button')).toBeVisible();
  });

  test('shippingお届け先追加 - EA0901-UC03-T03', async ({ page }) => {
    test.setTimeout(180_000);

    // Navigate to order list and find the first order
    await page.goto(`/${adminRoute}/order`);
    await page.waitForLoadState('load');
    await page.locator('#search_form .c-outsideBlock__contents button').first().click();
    await page.waitForLoadState('load');

    // Get the first order's edit link
    const firstOrderLink = page.locator('table tbody td a[href*="/order/"]').first();
    await expect(firstOrderLink).toBeVisible();
    const orderEditHref = await firstOrderLink.getAttribute('href');

    // Navigate to order edit page
    await page.goto(orderEditHref!);
    await page.waitForLoadState('load');

    // Find and navigate to the shipping edit page
    const shippingEditLink = page.locator('a[href*="/shipping/"][href*="/edit"]');
    await expect(shippingEditLink).toBeVisible();
    const shippingEditHref = await shippingEditLink.getAttribute('href');

    await page.goto(shippingEditHref!);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('出荷登録');

    // Count the current number of shipping sections to determine the new index
    const currentShippingCount = await page.locator('[id^="shipmentOverview_"]').count();
    const newIndex = currentShippingCount;

    // Click "出荷先を追加" button to add a new shipping destination
    await page.locator('#addShipping').click();
    await page.waitForTimeout(1000);

    // Add a product to the new shipping destination
    // Click the product search button in the new shipping section
    const newShippingProductBtn = page.locator('[id^="shipping-product_"] > div > button').last();
    await newShippingProductBtn.scrollIntoViewIfNeeded();
    await newShippingProductBtn.click();
    await page.waitForSelector('#searchProductModalButton', { state: 'visible' });

    // Search for a product
    await page.locator('#admin_search_product_id').fill('チェリーアイスサンド');
    await page.locator('#searchProductModalButton').click();
    await page.waitForSelector('#searchProductModalList table', { state: 'visible' });

    // Select the first product (row 2 since each product has 2 rows: info + class select)
    await page.locator('#searchProductModalList > table > tbody > tr:nth-child(2) > td.align-middle.pe-3.text-end > button').click();
    await page.waitForTimeout(2000);

    // Fill in shipping address for the new destination using dynamic index
    await page.locator(`#form_shippings_${newIndex}_name_name01`).fill('テスト');
    await page.locator(`#form_shippings_${newIndex}_name_name02`).fill('太郎');
    await page.locator(`#form_shippings_${newIndex}_kana_kana01`).fill('テスト');
    await page.locator(`#form_shippings_${newIndex}_kana_kana02`).fill('タロウ');
    await page.locator(`#form_shippings_${newIndex}_postal_code`).fill('0600000');
    await page.locator(`#form_shippings_${newIndex}_address_pref`).selectOption({ value: '1' });
    await page.locator(`#form_shippings_${newIndex}_address_addr01`).fill('テスト市');
    await page.locator(`#form_shippings_${newIndex}_address_addr02`).fill('テスト町1-1');
    await page.locator(`#form_shippings_${newIndex}_phone_number`).fill('111111111');

    // Submit the shipping form
    await page.locator('#btn_save').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました', { timeout: 30_000 });
  });

  test('shipping_出荷CSV登録失敗 - EA0903-UC04-T02', async ({ page }) => {
    // Navigate to shipping CSV upload page
    await page.goto(`/${adminRoute}/order/shipping_csv_upload`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('出荷CSV登録');

    // Upload an invalid shipping CSV with non-existent shipping IDs
    const csvPath = path.join(__dirname, '..', 'fixtures', 'shipping_invalid.csv');
    await page.locator('#admin_csv_import_import_file').setInputFiles(csvPath);
    await page.locator('#upload-button').click();
    await page.waitForLoadState('load');

    // Should show an error (the shipping ID 99999999 does not exist or the status transition is invalid)
    // The error is displayed in the upload form area as text-danger div
    // Error message is "2行目の出荷IDが存在しません" or "ステータス変更できません"
    await expect(page.locator('#upload-form')).toContainText(/存在しません|ステータス変更できません/);
  });
});
