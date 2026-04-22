import { test, expect, Page } from '@playwright/test';

/**
 * Helper: Login as the test customer via /mypage/login.
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
 * Helper: Create a new active customer via admin and return their email/password.
 * Uses admin to create the customer directly, avoiding the front-end registration flow
 * which may require email confirmation.
 */
async function createCustomerViaAdmin(page: Page): Promise<{ email: string; password: string }> {
  const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
  const email = `test_withdraw_${Date.now()}@example.com`;
  const password = 'password1234';

  const adminPage = await page.context().newPage();
  await adminPage.goto(`/${adminRoute}/`);
  await adminPage.waitForLoadState('load');
  await adminPage.locator('#login_id').fill(process.env.ADMIN_USER || 'admin');
  await adminPage.locator('#password').fill(process.env.ADMIN_PASSWORD || 'password');
  await adminPage.getByRole('button', { name: 'ログイン' }).click();
  await adminPage.waitForLoadState('load');

  // Go to new customer registration page
  await adminPage.goto(`/${adminRoute}/customer/new`);
  await adminPage.waitForLoadState('load');

  // Fill the form
  await adminPage.locator('#admin_customer_name_name01').fill('退会');
  await adminPage.locator('#admin_customer_name_name02').fill('テスト');
  await adminPage.locator('#admin_customer_kana_kana01').fill('タイカイ');
  await adminPage.locator('#admin_customer_kana_kana02').fill('テスト');
  await adminPage.locator('#admin_customer_postal_code').fill('530-0001');
  await adminPage.locator('#admin_customer_address_pref').selectOption({ value: '27' });
  await adminPage.locator('#admin_customer_address_addr01').fill('大阪市北区');
  await adminPage.locator('#admin_customer_address_addr02').fill('梅田2-4-9');
  await adminPage.locator('#admin_customer_phone_number').fill('111111111');
  await adminPage.locator('#admin_customer_email').fill(email);
  await adminPage.locator('#admin_customer_plain_password_first').fill(password);
  await adminPage.locator('#admin_customer_plain_password_second').fill(password);
  await adminPage.locator('#admin_customer_status').selectOption({ value: '2' }); // 本会員
  await adminPage.locator('#admin_customer_point').fill('0');

  // Submit
  await adminPage.locator('.c-conversionArea button[type="submit"]').first().click();
  await adminPage.waitForLoadState('load');
  await adminPage.close();

  return { email, password };
}

/**
 * Helper: Login with a specific email and password.
 */
async function loginAs(page: Page, email: string, password: string) {
  await page.goto('/mypage/login');
  await page.waitForLoadState('load');
  await page.locator('input[name="login_email"]').fill(email);
  await page.locator('input[name="login_pass"]').fill(password);
  await page.getByRole('button', { name: 'ログイン' }).click();
  await page.waitForLoadState('load');
}

/**
 * Helper: Add product to cart via AJAX and complete purchase.
 * Returns after completing the purchase.
 */
async function createOrder(page: Page) {
  await page.goto('/products/detail/2');
  await page.waitForLoadState('load');

  await page.locator('.add-cart').click();
  await expect(page.locator('.ec-modal')).toBeVisible({ timeout: 10_000 });

  // Go to cart
  await page.locator('.ec-modal-box a', { hasText: 'カートへ進む' }).click();
  await page.waitForLoadState('load');

  // Go to checkout
  await page.getByRole('link', { name: 'レジに進む' }).click();
  await page.waitForLoadState('load');

  // Confirm
  await page.locator('.ec-blockBtn--action', { hasText: '確認する' }).click();
  await page.waitForLoadState('load');

  // Order
  await page.locator('.ec-blockBtn--action', { hasText: '注文する' }).click();
  await page.waitForLoadState('load');
  await expect(page).toHaveURL(/\/shopping\/complete/);
}

/**
 * Helper: Clean up cart by deleting all items.
 */
async function clearCart(page: Page) {
  await page.goto('/cart');
  await page.waitForLoadState('load');
  page.on('dialog', dialog => dialog.accept());
  const deleteLinks = page.locator('.ec-cartRow__delColumn a');
  while (await deleteLinks.count() > 0) {
    await deleteLinks.first().click();
    await page.waitForLoadState('load');
  }
}

/**
 * Helper: Add a delivery address and return to the address list page.
 */
async function addDeliveryAddress(page: Page, addr01: string) {
  await page.goto('/mypage/delivery');
  await page.waitForLoadState('load');

  // Click add button
  await page.locator('div.ec-addressRole div.ec-addressRole__actions a').click();
  await page.waitForLoadState('load');

  // Fill form
  await page.locator('#customer_address_name_name01').fill('姓05');
  await page.locator('#customer_address_name_name02').fill('名05');
  await page.locator('#customer_address_kana_kana01').fill('セイ');
  await page.locator('#customer_address_kana_kana02').fill('メイ');
  await page.locator('#customer_address_postal_code').fill('530-0001');
  await page.locator('#customer_address_address_pref').selectOption({ value: '27' });
  await page.waitForTimeout(1000);
  await page.locator('#customer_address_address_addr01').fill(addr01);
  await page.locator('#customer_address_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
  await page.locator('#customer_address_phone_number').fill('111-111-111');

  // Submit
  await page.locator('div.ec-RegisterRole__actions button').click();
  await page.waitForLoadState('load');

  // Verify back on address list page
  await expect(page.locator('div.ec-pageHeader h1')).toContainText('お届け先一覧');
}

test.describe('Front Mypage (EF05)', () => {

  test('EF0501-UC01-T01 Mypage 初期表示', async ({ page }) => {
    await loginAsTestCustomer(page);

    await page.goto('/mypage/');
    await page.waitForLoadState('load');

    // Verify all mypage navigation items are displayed
    const navList = page.locator('ul.ec-navlistRole__navlist');
    await expect(navList.locator('li:nth-child(1) a')).toContainText('ご注文履歴');
    await expect(navList.locator('li:nth-child(2) a')).toContainText('お気に入り一覧');
    await expect(navList.locator('li:nth-child(3) a')).toContainText('会員情報編集');
    await expect(navList.locator('li:nth-child(4) a')).toContainText('お届け先一覧');
    await expect(navList.locator('li:nth-child(5) a')).toContainText('退会手続き');
  });

  test('EF0502-UC01-T01 Mypage ご注文履歴', async ({ page }) => {
    await loginAsTestCustomer(page);

    // Create an order so order history is not empty
    await createOrder(page);

    // Go to mypage order history
    await page.goto('/mypage/');
    await page.waitForLoadState('load');

    // Verify order history page
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('ご注文履歴');

    // Verify order number is displayed
    const mainContent = page.locator('main.ec-layoutRole__main');
    await expect(mainContent).toContainText('ご注文番号');

    // Verify "詳細を見る" button is displayed
    await expect(page.locator('p.ec-historyListHeader__action a').first()).toContainText('詳細を見る');
  });

  test('EF0503-UC01-T01 Mypage ご注文履歴詳細', async ({ page }) => {
    await loginAsTestCustomer(page);

    // Create an order if not already created
    await createOrder(page);

    // Go to mypage and click first order detail
    await page.goto('/mypage/');
    await page.waitForLoadState('load');
    await page.locator('p.ec-historyListHeader__action a').first().click();
    await page.waitForLoadState('load');

    // Verify history detail page
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('ご注文履歴詳細');

    // Verify sections exist
    await expect(page.locator('div.ec-orderOrder')).toContainText('ご注文状況');
    await expect(page.locator('div.ec-orderDelivery div.ec-rectHeading h2')).toContainText('配送情報');
    await expect(page.locator('div.ec-orderDelivery__title').first()).toContainText('お届け先');
    await expect(page.locator('div.ec-orderPayment div.ec-rectHeading h2')).toContainText('お支払い情報');
    await expect(page.locator('div.ec-orderConfirm div.ec-rectHeading h2')).toContainText('お問い合わせ');
    await expect(page.locator('div.ec-orderMails div.ec-rectHeading h2')).toContainText('メール配信履歴一覧');

    // Verify total box
    const totalBox = page.locator('div.ec-orderRole__summary div.ec-totalBox');
    await expect(totalBox).toContainText('小計');
    await expect(totalBox).toContainText('手数料');
    await expect(totalBox).toContainText('送料');
    await expect(totalBox).toContainText('合計');
  });

  test('EF0503-UC01-T02 Mypage お気に入り一覧', async ({ page }) => {
    await loginAsTestCustomer(page);

    // Go to favorites page - initially empty
    await page.goto('/mypage/favorite');
    await page.waitForLoadState('load');
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お気に入り一覧');
    await expect(page.locator('div.ec-favoriteRole')).toContainText('お気に入りは登録されていません');

    // Add product to favorites
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');
    await page.locator('button#favorite').click();
    await page.waitForLoadState('load');

    // Go to favorites and verify
    await page.goto('/mypage/favorite');
    await page.waitForLoadState('load');
    await expect(page.locator('ul.ec-favoriteRole__itemList li:nth-child(1) p.ec-favoriteRole__itemTitle')).toContainText('チェリーアイスサンド');

    // Delete the favorite
    page.on('dialog', dialog => dialog.accept());
    await page.locator('ul.ec-favoriteRole__itemList li:nth-child(1) a.ec-closeBtn--circle').click();
    await page.waitForLoadState('load');

    // Verify removed
    await expect(page.locator('div.ec-favoriteRole')).toContainText('お気に入りは登録されていません');
  });

  test('EF0504-UC01-T01 Mypage 会員情報編集', async ({ page }) => {
    await loginAsTestCustomer(page);

    // Go to member info edit page
    await page.goto('/mypage/change');
    await page.waitForLoadState('load');

    await expect(page.locator('div.ec-pageHeader h1')).toContainText('会員情報編集');

    // Verify existing info is displayed
    const currentName01 = await page.locator('#entry_name_name01').inputValue();
    expect(currentName01.length).toBeGreaterThan(0);

    // Save original values for restoration
    const origName01 = currentName01;
    const origName02 = await page.locator('#entry_name_name02').inputValue();

    // Fill the form with new values (keep same email to not break other tests)
    await page.locator('#entry_name_name01').fill('姓05');
    await page.locator('#entry_name_name02').fill('名05');
    await page.locator('#entry_kana_kana01').fill('セイ');
    await page.locator('#entry_kana_kana02').fill('メイ');
    await page.locator('#entry_postal_code').fill('5300001');
    await page.locator('#entry_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(1000);
    await page.locator('#entry_address_addr01').fill('大阪市北区');
    await page.locator('#entry_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
    await page.locator('#entry_phone_number').fill('111111111');
    await page.locator('#entry_email_first').fill('playwright@test.test');
    await page.locator('#entry_email_second').fill('playwright@test.test');
    // Leave password fields empty to keep the current password

    // Submit
    await page.locator('div.ec-editRole form button.ec-blockBtn--cancel', { hasText: '登録する' }).click();
    await page.waitForLoadState('load');

    // Verify completion page
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('会員情報編集(完了)');

    // Click "トップページへ" button
    await page.locator('div.ec-registerCompleteRole a.ec-blockBtn--cancel').click();
    await page.waitForLoadState('load');

    // Verify top page
    await expect(page.locator('.ec-newsRole')).toBeVisible();
  });

  test('EF0506-UC01-T01 Mypage お届け先編集表示', async ({ page }) => {
    await loginAsTestCustomer(page);

    // Go to delivery address list
    await page.goto('/mypage/delivery');
    await page.waitForLoadState('load');

    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お届け先一覧');
  });

  test('EF0506-UC01-T02 Mypage お届け先編集作成変更', async ({ page }) => {
    await loginAsTestCustomer(page);

    // Create a new delivery address
    await addDeliveryAddress(page, '大阪市北区');

    // Verify the address appears in the list
    await expect(page.locator('div.ec-addressList div:nth-child(1) div.ec-addressList__address')).toContainText('大阪市北区');

    // Edit the address
    await page.locator('div.ec-addressList div:nth-child(1) div.ec-addressList__action a').click();
    await page.waitForLoadState('load');

    // Change city
    await page.locator('#customer_address_address_addr01').fill('大阪市南区');

    // Submit
    await page.locator('div.ec-RegisterRole__actions button').click();
    await page.waitForLoadState('load');

    // Verify updated
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お届け先一覧');
    await expect(page.locator('div.ec-addressList div:nth-child(1) div.ec-addressList__address')).toContainText('大阪市南区');

    // Cleanup: delete the address
    page.on('dialog', dialog => dialog.accept());
    await page.locator('div.ec-addressList div:nth-child(1) a.ec-addressList__remove').click();
    await page.waitForLoadState('load');
  });

  test('EF0506-UC03-T01 Mypage お届け先編集削除', async ({ page }) => {
    await loginAsTestCustomer(page);

    // Create a delivery address to delete
    await addDeliveryAddress(page, '大阪市西区');

    // Verify the address appears
    await expect(page.locator('div.ec-addressList div:nth-child(1) div.ec-addressList__address')).toContainText('大阪市西区');

    // Delete the address
    page.on('dialog', dialog => dialog.accept());
    await page.locator('div.ec-addressList div:nth-child(1) a.ec-addressList__remove').click();
    await page.waitForLoadState('load');

    // Verify deleted - "お届け先は登録されていません。" should appear
    await expect(page.locator('#page_mypage_delivery')).toContainText('お届け先は登録されていません');
  });

  test.skip('EF0506-UC03-T02 Mypage お届け先上限確認', async ({ page }) => {
    // This test requires creating 20 addresses programmatically.
    // Skipped as per instructions (requires 20+ addresses).
  });

  test('EF0507-UC03-T01 Mypage 退会手続き 未実施', async ({ page }) => {
    await loginAsTestCustomer(page);

    // Go to withdrawal page
    await page.goto('/mypage/withdraw');
    await page.waitForLoadState('load');

    // Click "退会手続きへ" button
    await page.locator('div.ec-withdrawRole form button').click();
    await page.waitForLoadState('load');

    // Verify confirm page is displayed
    await expect(page.locator('div.ec-withdrawConfirmRole')).toBeVisible();

    // Click "いいえ、退会しません" (cancel)
    await page.locator('div.ec-withdrawConfirmRole form a.ec-withdrawConfirmRole__cancel').click();
    await page.waitForLoadState('load');

    // Verify back on mypage
    const navList = page.locator('ul.ec-navlistRole__navlist');
    await expect(navList.locator('li:nth-child(1) a')).toContainText('ご注文履歴');
    await expect(navList.locator('li:nth-child(5) a')).toContainText('退会手続き');
  });

  test('EF0507-UC03-T02 Mypage 退会手続き', async ({ page }) => {
    // Create a new customer specifically for withdrawal via admin
    const { email, password } = await createCustomerViaAdmin(page);

    // Login as the new customer
    await loginAs(page, email, password);

    // Go to withdrawal page
    await page.goto('/mypage/withdraw');
    await page.waitForLoadState('load');

    // Click "退会手続きへ" button
    await page.locator('div.ec-withdrawRole form button').click();
    await page.waitForLoadState('load');

    // Click "はい、退会します" (confirm withdrawal)
    await page.locator('div.ec-withdrawConfirmRole form button').click();
    await page.waitForLoadState('load');

    // Verify withdrawal complete
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('退会手続き');
    await expect(page.locator('div.ec-withdrawCompleteRole div.ec-reportHeading')).toContainText('退会が完了いたしました');

    // Click "トップページへ"
    await page.locator('div.ec-withdrawCompleteRole a.ec-blockBtn--cancel').click();
    await page.waitForLoadState('load');

    // Verify top page
    await expect(page.locator('.ec-newsRole')).toBeVisible();
  });
});
