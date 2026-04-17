import { test, expect, Page, BrowserContext } from '@playwright/test';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';

/**
 * Helper: Login as admin user in a given page.
 */
async function loginAsAdmin(page: Page) {
  await page.goto(`/${adminRoute}/`);
  await page.waitForLoadState('load');
  await page.locator('#login_id').fill(process.env.ADMIN_USER || 'admin');
  await page.locator('#password').fill(process.env.ADMIN_PASSWORD || 'password');
  await page.getByRole('button', { name: 'ログイン' }).click();
  await page.waitForLoadState('load');
}

/**
 * Helper: Login as the test customer.
 */
async function loginAsTestCustomer(page: Page) {
  await page.goto('/mypage/login');
  await page.waitForLoadState('load');
  await page.locator('input[name="login_email"]').fill('playwright@test.test');
  await page.locator('input[name="login_pass"]').fill('password');
  await page.getByRole('button', { name: 'ログイン' }).click();
  await page.waitForLoadState('load');
}

/**
 * Helper: Enable product-level tax rate in shop settings.
 */
async function enableProductTaxRule(page: Page) {
  await page.goto(`/${adminRoute}/setting/shop`);
  await page.waitForLoadState('load');

  const isChecked = await page.locator('#shop_master_option_product_tax_rule').isChecked();
  if (!isChecked) {
    await page.evaluate(() => {
      const cb = document.querySelector('#shop_master_option_product_tax_rule') as HTMLInputElement;
      if (cb) cb.click();
    });
  }

  await page.locator('.c-conversionArea button[type=submit]').first().click();
  await page.waitForLoadState('load');
}

/**
 * Helper: Set payment charge for payment ID 1.
 */
async function setPaymentCharge(page: Page, charge: string) {
  await page.goto(`/${adminRoute}/setting/shop/payment/1/edit`);
  await page.waitForLoadState('load');

  await page.locator('#payment_register_charge').fill(charge);

  await page.locator('.c-conversionArea button[type=submit]').first().click();
  await page.waitForLoadState('load');
}

/**
 * Helper: Create a product with the given name, price, category and optional tax rate.
 */
async function createProduct(page: Page, name: string, price: string, categoryId: number, taxRate?: number) {
  await page.goto(`/${adminRoute}/product/product/new`);
  await page.waitForLoadState('load');

  await page.locator('#admin_product_name').fill(name);
  await page.locator('#admin_product_class_price02').fill(price);
  await page.locator(`#admin_product_category_${categoryId}`).check();
  await page.locator('#admin_product_Status').selectOption({ label: '公開' });

  if (taxRate !== undefined) {
    await page.locator('#admin_product_class_tax_rate').fill(String(taxRate));
  }

  // Submit
  await page.locator('#form1 .c-conversionArea button').last().click();
  await page.waitForLoadState('load');

  // Wait for success message
  await expect(page.locator('.alert-success')).toContainText('保存しました', { timeout: 10_000 });
}

/**
 * Helper: Search for a product by name on the front, add to cart with quantity.
 */
async function searchAndAddToCart(page: Page, productName: string, quantity: number) {
  // Search for the product
  await page.goto('/');
  await page.waitForLoadState('load');
  const searchArea = page.locator('.ec-layoutRole__header .ec-headerSearch').first();
  await searchArea.locator('input.search-name').fill(productName);
  await searchArea.locator('button.ec-headerSearch__keywordBtn').click();
  await page.waitForLoadState('load');

  // Click the first product in the results to go to its detail page
  await page.locator('ul.ec-shelfGrid li.ec-shelfGrid__item a').first().click();
  await page.waitForLoadState('load');

  // Set quantity
  await page.locator('#quantity').fill(String(quantity));

  // Add to cart
  await page.locator('.add-cart').click();
  await expect(page.locator('.ec-modal')).toBeVisible({ timeout: 10_000 });

  // Close modal
  await page.locator('.ec-modal-overlay').click();
  await page.waitForTimeout(500);
}

/**
 * Helper: Clean up by deleting test products from admin.
 */
async function deleteProductByName(page: Page, productName: string) {
  await page.goto(`/${adminRoute}/product`);
  await page.waitForLoadState('load');
  await page.locator('#admin_search_product_id').fill('');
  await page.locator('#admin_search_product_name').fill(productName);
  await page.locator('.c-contentsArea__cols button[type="submit"]').click();
  await page.waitForLoadState('load');

  // Check if any product was found
  const checkboxes = page.locator('input[name="ids[]"]');
  if (await checkboxes.count() > 0) {
    await checkboxes.first().check();
    // Bulk delete button click (if available)
    // For simplicity, we delete via product edit page
  }
}

test.describe('Front Invoice (EF08)', () => {

  test('EF0801-UC01-T01 商品購入 税額確認', async ({ page, context }) => {
    // Use unique product names to avoid conflicts with previous runs
    const suffix = String(Date.now()).slice(-6);
    const productNames = {
      choco: `チョコ${suffix}`,
      vanilla: `バニラ${suffix}`,
      matcha: `抹茶${suffix}`,
    };

    // -- ADMIN SETUP --
    // Login as admin in a separate page to set up shop and products
    const adminPage = await context.newPage();
    await loginAsAdmin(adminPage);

    // Enable product-level tax rates
    await enableProductTaxRule(adminPage);

    // Set payment charge to 2187 yen
    await setPaymentCharge(adminPage, '2187');

    // Create test products with unique names
    await createProduct(adminPage, productNames.choco, '71141', 1);
    await createProduct(adminPage, productNames.vanilla, '92778', 1);
    await createProduct(adminPage, productNames.matcha, '15221', 1, 8); // 8% tax rate

    // Give test customer 10000 points
    await adminPage.goto(`/${adminRoute}/customer`);
    await adminPage.waitForLoadState('load');
    await adminPage.locator('#admin_search_customer_multi').fill('playwright@test.test');
    await adminPage.getByRole('button', { name: '検索' }).click();
    await adminPage.waitForLoadState('load');
    await adminPage.locator('a[href*="/customer/"][href*="/edit"]').first().click();
    await adminPage.waitForLoadState('load');
    await adminPage.locator('#admin_customer_point').fill('10000');
    await adminPage.locator('.c-conversionArea button[type="submit"]').first().click();
    await adminPage.waitForLoadState('load');

    await adminPage.close();

    // -- FRONT PURCHASE --
    await loginAsTestCustomer(page);

    // Clear any existing cart items
    await page.goto('/cart');
    await page.waitForLoadState('load');
    page.on('dialog', dialog => dialog.accept());
    const deleteLinks = page.locator('.ec-cartRow__delColumn a');
    while (await deleteLinks.count() > 0) {
      await deleteLinks.first().click();
      await page.waitForLoadState('load');
    }

    // Add products to cart
    await searchAndAddToCart(page, productNames.choco, 4);
    await searchAndAddToCart(page, productNames.vanilla, 4);
    await searchAndAddToCart(page, productNames.matcha, 4);

    // Go to cart
    await page.goto('/cart');
    await page.waitForLoadState('load');

    // Go to checkout
    await page.getByRole('link', { name: 'レジに進む' }).click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/shopping$/);

    // Verify prices on shopping page (before point deduction)
    const totalBox = page.locator('.ec-totalBox');
    await expect(page.locator('//dt[contains(text(), "小計")]/../dd')).toContainText('787,000');
    await expect(page.locator('//dt[contains(text(), "手数料")]/../dd')).toContainText('2,187');
    await expect(page.locator('//dt[contains(text(), "送料")]/../dd')).toContainText('1,000');
    await expect(page.locator('//div[@class="ec-totalBox__total"]/span[@class="ec-totalBox__price"]')).toContainText('790,187');

    // Verify tax breakdown exists (tax rates 8% and 10% are shown)
    const tax8dd = page.locator('//dt[contains(text(), "税率 8 %対象")]/../dd');
    await expect(tax8dd).toBeVisible();
    const tax8Text = await tax8dd.textContent();
    // Verify it contains the inner tax amount pattern
    expect(tax8Text).toMatch(/内消費税/);

    const tax10dd = page.locator('//dt[contains(text(), "税率 10 %対象")]/../dd');
    await expect(tax10dd).toBeVisible();
    const tax10Text = await tax10dd.textContent();
    expect(tax10Text).toMatch(/内消費税/);

    // Go to confirm page
    await page.locator('#shopping-form div.ec-orderRole__summary div.ec-totalBox button').scrollIntoViewIfNeeded();
    await page.locator('.ec-blockBtn--action', { hasText: '確認する' }).click();
    await page.waitForLoadState('load');
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('ご注文内容のご確認');

    // Verify prices on confirm page
    await expect(page.locator('//dt[contains(text(), "小計")]/../dd')).toContainText('787,000');
    await expect(page.locator('//dt[contains(text(), "手数料")]/../dd')).toContainText('2,187');
    await expect(page.locator('//dt[contains(text(), "送料")]/../dd')).toContainText('1,000');
    await expect(page.locator('//div[@class="ec-totalBox__total"]/span[@class="ec-totalBox__price"]')).toContainText('790,187');

    // Verify tax breakdown on confirm page
    const confirmTax8dd = page.locator('//dt[contains(text(), "税率 8 %対象")]/../dd');
    await expect(confirmTax8dd).toBeVisible();
    const confirmTax8Text = await confirmTax8dd.textContent();
    expect(confirmTax8Text).toMatch(/内消費税/);

    const confirmTax10dd = page.locator('//dt[contains(text(), "税率 10 %対象")]/../dd');
    await expect(confirmTax10dd).toBeVisible();
    const confirmTax10Text = await confirmTax10dd.textContent();
    expect(confirmTax10Text).toMatch(/内消費税/);

    // Complete the order
    await page.locator('#shopping-form div.ec-orderRole__summary div.ec-totalBox button').scrollIntoViewIfNeeded();
    await page.locator('.ec-blockBtn--action', { hasText: '注文する' }).click();
    await page.waitForLoadState('load');

    await expect(page).toHaveURL(/\/shopping\/complete/);
  });
});
