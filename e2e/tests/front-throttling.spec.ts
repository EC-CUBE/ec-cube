import { test, expect } from '@playwright/test';
import { authenticator } from '@otplib/preset-default';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';

/**
 * EF09 Throttling Tests
 *
 * These tests verify that rate limiting works correctly for various forms.
 * They require APP_ENV=prod to be set (rate limiter is disabled in other envs).
 *
 * Each test is designed to run independently. On CI, rate limiter state is cleared
 * between matrix entries because each test method runs in a separate job.
 */

// Throttling tests do not use shared storageState
test.use({ storageState: { cookies: [], origins: [] } });

/**
 * Helper: Log in as a customer on the front site.
 */
async function loginAsMember(page: import('@playwright/test').Page, email: string, password = 'password') {
  await page.goto('/mypage/login');
  await page.waitForLoadState('load');
  await page.locator('input[name="login_email"]').fill(email);
  await page.locator('input[name="login_pass"]').fill(password);
  await page.locator('#login_mypage button[type="submit"]').click();
  await page.waitForLoadState('load');
}

/**
 * テスト用顧客のメールアドレス (globalSetup の setup-fixtures.php で作成)
 */
function getTestCustomerEmail(): string {
  return process.env.CUSTOMER_EMAIL || 'playwright@test.test';
}

function getTestCustomerPassword(): string {
  return process.env.CUSTOMER_PASSWORD || 'password';
}

/**
 * Helper: Fill and submit the contact form (input -> confirm -> complete).
 */
async function submitContactForm(page: import('@playwright/test').Page) {
  const email = `contact_${Date.now()}@example.com`;

  await page.goto('/contact');
  await page.waitForLoadState('load');

  // Fill form
  await page.locator('#contact_name_name01').fill('姓');
  await page.locator('#contact_name_name02').fill('名');
  await page.locator('#contact_kana_kana01').fill('セイ');
  await page.locator('#contact_kana_kana02').fill('メイ');
  await page.locator('#contact_postal_code').fill('530-0001');
  await page.locator('#contact_address_pref').selectOption({ value: '27' });
  await page.waitForTimeout(500);
  await page.locator('#contact_address_addr01').fill('大阪市北区');
  await page.locator('#contact_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
  await page.locator('#contact_phone_number').fill('111-111-111');
  await page.locator('#contact_email').fill(email);
  await page.locator('#contact_contents').fill('お問い合わせ内容の送信');

  // Go to confirm page
  await page.locator('div.ec-RegisterRole__actions button.ec-blockBtn--action').click();
  await page.waitForLoadState('load');

  // Submit (complete)
  await page.waitForTimeout(1000);
  await page.locator('div.ec-contactConfirmRole div.ec-RegisterRole__actions button.ec-blockBtn--action').click();
  await page.waitForLoadState('load');
}

/**
 * Helper: Fill the entry (registration) form and submit with confirmation (agree -> register).
 */
async function submitEntryFormWithConfirm(page: import('@playwright/test').Page) {
  const email = `entry_${Date.now()}_${Math.random().toString(36).slice(2)}@example.com`;

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
  await page.locator('#entry_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
  await page.locator('#entry_phone_number').fill('111-111-111');
  await page.locator('#entry_email_first').fill(email);
  await page.locator('#entry_email_second').fill(email);
  await page.locator('#entry_plain_password_first').fill('password1234');
  await page.locator('#entry_plain_password_second').fill('password1234');
  await page.locator('#entry_user_policy_check').check();

  // Click agree/submit button
  await page.locator('button.ec-blockBtn--action[type="submit"]').click();
  await page.waitForLoadState('load');

  // Confirm page -> click register
  await page.locator('button.ec-blockBtn--action[type="submit"]').click();
  await page.waitForLoadState('load');
}

/**
 * Helper: Fill the entry form and submit WITHOUT going through confirmation (no agree checkbox).
 * This tests the entry (input) rate limiter.
 */
async function submitEntryFormWithoutConfirm(page: import('@playwright/test').Page) {
  const email = `entry_${Date.now()}_${Math.random().toString(36).slice(2)}@example.com`;

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
  await page.locator('#entry_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
  await page.locator('#entry_phone_number').fill('111-111-111');
  await page.locator('#entry_email_first').fill(email);
  await page.locator('#entry_email_second').fill(email);
  await page.locator('#entry_plain_password_first').fill('password1234');
  await page.locator('#entry_plain_password_second').fill('password1234');

  // Submit without checking user_policy_check (goes to confirm without mode=complete)
  await page.locator('button.ec-blockBtn--action[type="submit"]').click();
  await page.waitForLoadState('load');
}

/**
 * Helper: Fill delivery address form fields (does NOT submit).
 */
async function fillDeliveryAddressForm(page: import('@playwright/test').Page, sei = '姓05', mei = '名05') {
  await page.locator('#customer_address_name_name01').fill(sei);
  await page.locator('#customer_address_name_name02').fill(mei);
  await page.locator('#customer_address_kana_kana01').fill('セイ');
  await page.locator('#customer_address_kana_kana02').fill('メイ');
  await page.locator('#customer_address_postal_code').fill('530-0001');
  await page.locator('#customer_address_address_pref').selectOption({ value: '27' });
  await page.waitForTimeout(500);
  await page.locator('#customer_address_address_addr01').fill('大阪市北区');
  await page.locator('#customer_address_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
  await page.locator('#customer_address_phone_number').fill('111-111-111');
}

/**
 * Helper: Submit delivery address form.
 * The delivery edit page uses ec-blockBtn--cancel class for the submit button.
 */
async function submitDeliveryAddressForm(page: import('@playwright/test').Page) {
  await page.locator('div.ec-RegisterRole__actions button[type="submit"]').click();
  await page.waitForLoadState('load');
}

test.describe('Throttling (EF09)', () => {

  // --- Front login: IP-based (25 attempts) ---
  test('フロント画面ログイン_IP', async ({ page }) => {
    test.setTimeout(120_000);

    for (let i = 0; i < 25; i++) {
      const email = `${Date.now()}.${i}@test.example.com`;
      await page.goto('/mypage/login');
      await page.waitForLoadState('load');
      await page.locator('input[name="login_email"]').fill(email);
      await page.locator('input[name="login_pass"]').fill('wrong_password');
      await page.locator('#login_mypage button[type="submit"]').click();
      await page.waitForLoadState('load');
      await expect(page.locator('p.ec-errorMessage')).toContainText('ログインできませんでした。');
      await expect(page.locator('p.ec-errorMessage')).toContainText('入力内容に誤りがないかご確認ください。');
    }

    // Exceeds IP limit
    const email = `${Date.now()}.exceed@test.example.com`;
    await page.goto('/mypage/login');
    await page.waitForLoadState('load');
    await page.locator('input[name="login_email"]').fill(email);
    await page.locator('input[name="login_pass"]').fill('wrong_password');
    await page.locator('#login_mypage button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('p.ec-errorMessage')).toContainText('ログイン試行回数が多すぎます。');
  });

  // --- Front login: Customer-based (5 attempts for same email) ---
  test('フロント画面ログイン_会員', async ({ page }) => {
    test.setTimeout(60_000);

    const email = `${Date.now()}.customer@test.example.com`;

    for (let i = 0; i < 5; i++) {
      await page.goto('/mypage/login');
      await page.waitForLoadState('load');
      await page.locator('input[name="login_email"]').fill(email);
      await page.locator('input[name="login_pass"]').fill('wrong_password');
      await page.locator('#login_mypage button[type="submit"]').click();
      await page.waitForLoadState('load');
      await expect(page.locator('p.ec-errorMessage')).toContainText('ログインできませんでした。');
      await expect(page.locator('p.ec-errorMessage')).toContainText('入力内容に誤りがないかご確認ください。');
    }

    // Exceeds customer limit
    await page.goto('/mypage/login');
    await page.waitForLoadState('load');
    await page.locator('input[name="login_email"]').fill(email);
    await page.locator('input[name="login_pass"]').fill('wrong_password');
    await page.locator('#login_mypage button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('p.ec-errorMessage')).toContainText('ログイン試行回数が多すぎます。');
  });

  // --- Admin login: IP-based (25 attempts) ---
  test('管理画面ログイン_IP', async ({ page }) => {
    test.setTimeout(120_000);

    for (let i = 0; i < 25; i++) {
      const loginId = `${Date.now()}_${i}`;
      await page.goto(`/${adminRoute}/`);
      await page.waitForLoadState('load');
      await page.locator('#login_id').fill(loginId);
      await page.locator('#password').fill('wrong_password');
      await page.getByRole('button', { name: 'ログイン' }).click();
      await page.waitForLoadState('load');
      await expect(page.locator('span.text-danger')).toContainText('ログインできませんでした。');
      await expect(page.locator('span.text-danger')).toContainText('入力内容に誤りがないかご確認ください。');
    }

    // Exceeds IP limit
    const loginId = `${Date.now()}_exceed`;
    await page.goto(`/${adminRoute}/`);
    await page.waitForLoadState('load');
    await page.locator('#login_id').fill(loginId);
    await page.locator('#password').fill('wrong_password');
    await page.getByRole('button', { name: 'ログイン' }).click();
    await page.waitForLoadState('load');
    await expect(page.locator('span.text-danger')).toContainText('ログイン試行回数が多すぎます。');
  });

  // --- Admin login: Member-based (5 attempts for same login_id) ---
  test('管理画面ログイン_会員', async ({ page }) => {
    test.setTimeout(60_000);

    const loginId = `${Date.now()}_member`;

    for (let i = 0; i < 5; i++) {
      await page.goto(`/${adminRoute}/`);
      await page.waitForLoadState('load');
      await page.locator('#login_id').fill(loginId);
      await page.locator('#password').fill('wrong_password');
      await page.getByRole('button', { name: 'ログイン' }).click();
      await page.waitForLoadState('load');
      await expect(page.locator('span.text-danger')).toContainText('ログインできませんでした。');
      await expect(page.locator('span.text-danger')).toContainText('入力内容に誤りがないかご確認ください。');
    }

    // Exceeds member limit
    await page.goto(`/${adminRoute}/`);
    await page.waitForLoadState('load');
    await page.locator('#login_id').fill(loginId);
    await page.locator('#password').fill('wrong_password');
    await page.getByRole('button', { name: 'ログイン' }).click();
    await page.waitForLoadState('load');
    await expect(page.locator('span.text-danger')).toContainText('ログイン試行回数が多すぎます。');
  });

  // --- Customer registration with confirmation (5 attempts, entry_complete limiter) ---
  test('会員登録', async ({ page }) => {
    test.setTimeout(180_000);

    for (let i = 0; i < 5; i++) {
      await submitEntryFormWithConfirm(page);
      await expect(page.locator('p.ec-reportDescription')).toContainText('現在、仮会員の状態です。');
    }

    // Exceeds limit
    await submitEntryFormWithConfirm(page);
    await expect(page.locator('p.ec-reportDescription')).toContainText(
      '試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。'
    );
  });

  // --- Contact form (5 complete submissions) ---
  test('問い合わせ', async ({ page }) => {
    test.setTimeout(180_000);

    for (let i = 0; i < 5; i++) {
      await submitContactForm(page);
      await expect(page.locator('div.ec-pageHeader h1')).toContainText('お問い合わせ(完了)');
    }

    // Exceeds limit
    await submitContactForm(page);
    await expect(page.locator('p.ec-reportDescription')).toContainText(
      '試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。'
    );
  });

  // --- Password reset (5 attempts) ---
  test('パスワード再発行', async ({ page }) => {
    test.setTimeout(60_000);

    for (let i = 0; i < 5; i++) {
      await page.goto('/forgot');
      await page.waitForLoadState('load');
      await page.locator('input[name="login_email"]').fill('test@example.com');
      await page.locator('button.ec-blockBtn--action[type="submit"]').click();
      await page.waitForLoadState('load');
      await expect(page.locator('div.ec-pageHeader h1')).toContainText('パスワードの再発行(メール送信)');
    }

    // Exceeds limit
    await page.goto('/forgot');
    await page.waitForLoadState('load');
    await page.locator('input[name="login_email"]').fill('test@example.com');
    await page.locator('button.ec-blockBtn--action[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('p.ec-reportDescription')).toContainText(
      '試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。'
    );
  });

  // --- Entry form input only (25 attempts, entry limiter without mode=complete) ---
  test('新規会員登録_入力', async ({ page }) => {
    test.setTimeout(180_000);

    for (let i = 0; i < 25; i++) {
      await submitEntryFormWithoutConfirm(page);
    }

    // Exceeds limit
    await submitEntryFormWithoutConfirm(page);
    await expect(page.locator('p.ec-reportDescription')).toContainText(
      '試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。'
    );
  });

  // --- Customer info edit (10 edits, mypage_change limiter) ---
  test('会員情報編集', async ({ page }) => {
    test.setTimeout(180_000);

    const email = getTestCustomerEmail();
    await loginAsMember(page, email);

    for (let i = 0; i < 10; i++) {
      await page.goto('/mypage/change');
      await page.waitForLoadState('load');

      // Submit the form as-is (no changes needed)
      await page.locator('div.ec-editRole form button[type="submit"]').click();
      await page.waitForLoadState('load');
      await expect(page.locator('div.ec-pageHeader h1')).toContainText('会員情報編集(完了)');
    }

    // Exceeds limit
    await page.goto('/mypage/change');
    await page.waitForLoadState('load');
    await page.locator('div.ec-editRole form button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('p.ec-reportDescription')).toContainText(
      '試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。'
    );
  });

  // --- Delivery address: add (10 additions, mypage_delivery_new limiter) ---
  test('配送先情報_追加', async ({ page }) => {
    test.setTimeout(300_000);

    const email = getTestCustomerEmail();
    await loginAsMember(page, email);

    for (let i = 0; i < 10; i++) {
      await page.goto('/mypage/delivery/new');
      await page.waitForLoadState('load');

      await fillDeliveryAddressForm(page);
      await submitDeliveryAddressForm(page);
    }

    // Exceeds limit
    await page.goto('/mypage/delivery/new');
    await page.waitForLoadState('load');

    await fillDeliveryAddressForm(page);
    await submitDeliveryAddressForm(page);
    await expect(page.locator('p.ec-reportDescription')).toContainText(
      '試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。'
    );
  });

  // --- Delivery address: edit (10 edits, mypage_delivery_edit limiter) ---
  test('配送先情報_編集', async ({ page }) => {
    test.setTimeout(300_000);

    const email = getTestCustomerEmail();
    await loginAsMember(page, email);

    // First, create a delivery address to edit
    await page.goto('/mypage/delivery/new');
    await page.waitForLoadState('load');
    await fillDeliveryAddressForm(page);
    await submitDeliveryAddressForm(page);

    // Get the edit link for the first delivery address
    await page.goto('/mypage/delivery');
    await page.waitForLoadState('load');
    const editLink = page.locator('.ec-addressList__action a.ec-inlineBtn').first();
    const editHref = await editLink.getAttribute('href');

    for (let i = 0; i < 10; i++) {
      await page.goto(editHref!);
      await page.waitForLoadState('load');

      await fillDeliveryAddressForm(page, '姓05', '名05');
      await submitDeliveryAddressForm(page);
    }

    // Exceeds limit
    await page.goto(editHref!);
    await page.waitForLoadState('load');

    await fillDeliveryAddressForm(page, '姓05', '名05');
    await submitDeliveryAddressForm(page);
    await expect(page.locator('p.ec-reportDescription')).toContainText(
      '試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。'
    );
  });

  // --- Delivery address: delete (10 deletions, mypage_delivery_delete limiter) ---
  // Note: This test requires the mypage_delivery_new limit to be raised in CI config
  test('配送先情報_削除', async ({ page }) => {
    test.setTimeout(300_000);

    const email = getTestCustomerEmail();
    await loginAsMember(page, email);

    // Register dialog handler once (auto-accept all confirm dialogs)
    page.on('dialog', dialog => dialog.accept());

    // Create 11 delivery addresses (need 10 to delete + 1 more to trigger throttle)
    for (let i = 0; i < 11; i++) {
      await page.goto('/mypage/delivery/new');
      await page.waitForLoadState('load');
      await fillDeliveryAddressForm(page, '姓0501', '名0501');
      await submitDeliveryAddressForm(page);
    }

    // Delete 10 delivery addresses
    for (let i = 0; i < 10; i++) {
      await page.goto('/mypage/delivery');
      await page.waitForLoadState('load');
      await page.locator('a.ec-addressList__remove').first().click();
      await page.waitForLoadState('load');
    }

    // Add one more (to have something to delete) and then try to delete it
    await page.goto('/mypage/delivery/new');
    await page.waitForLoadState('load');
    await fillDeliveryAddressForm(page, '姓0501', '名0501');
    await submitDeliveryAddressForm(page);

    await page.waitForTimeout(1000);

    // Try to delete - should be throttled
    await page.goto('/mypage/delivery');
    await page.waitForLoadState('load');
    await page.locator('a.ec-addressList__remove').first().click();
    await page.waitForLoadState('load');

    await expect(page.locator('p.ec-reportDescription')).toContainText(
      '試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。'
    );
  });

  // --- Order confirm: guest purchase (25 attempts, shopping_confirm limiter) ---
  // TODO: These throttling tests require full shopping flow (add to cart -> checkout -> confirm).
  // They are complex and slow due to the number of iterations required.
  test.fixme('注文確認_非会員購入', async ({ page }) => {
    // EF0901-UC01-T08: Guest purchase confirm throttling
    // Requires 25 iterations of: add to cart -> checkout -> guest info -> confirm -> complete
    // Then one more to trigger throttle on the confirm step
    test.setTimeout(600_000);

    for (let i = 0; i < 25; i++) {
      await page.goto('/products/detail/2');
      await page.waitForLoadState('load');
      await page.locator('#quantity').fill('1');
      await page.locator('.add-cart').click();
      await page.waitForTimeout(3000);
      await page.locator('div.ec-modal-box > div > a').click();
      await page.waitForLoadState('load');

      await page.goto('/cart');
      await page.waitForLoadState('load');
      await page.getByRole('link', { name: 'レジに進む' }).click();
      await page.waitForLoadState('load');

      // Guest purchase
      await page.locator('a[href*="shopping/nonmember"]').click();
      await page.waitForLoadState('load');

      // Fill guest info
      await page.locator('#nonmember_name_name01').fill('姓03');
      await page.locator('#nonmember_name_name02').fill('名03');
      await page.locator('#nonmember_kana_kana01').fill('セイ');
      await page.locator('#nonmember_kana_kana02').fill('メイ');
      await page.locator('#nonmember_postal_code').fill('530-0001');
      await page.locator('#nonmember_address_pref').selectOption({ value: '27' });
      await page.waitForTimeout(500);
      await page.locator('#nonmember_address_addr01').fill('大阪市北区');
      await page.locator('#nonmember_address_addr02').fill('梅田2-4-9');
      await page.locator('#nonmember_phone_number').fill('111-111-111');
      await page.locator('#nonmember_email_first').fill('test@example.com');
      await page.locator('#nonmember_email_second').fill('test@example.com');
      await page.locator('button[type="submit"]').click();
      await page.waitForLoadState('load');

      // Confirm
      await page.locator('#shopping-form button.ec-blockBtn--action').click();
      await page.waitForLoadState('load');

      // Complete
      await page.locator('#shopping-form button.ec-blockBtn--action').click();
      await page.waitForLoadState('load');
      await expect(page.locator('div.ec-pageHeader h1')).toContainText('ご注文完了');
    }

    // Trigger throttle on confirm step
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');
    await page.locator('#quantity').fill('1');
    await page.locator('.add-cart').click();
    await page.waitForTimeout(3000);
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    await page.goto('/cart');
    await page.waitForLoadState('load');
    await page.getByRole('link', { name: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    await page.locator('a[href*="shopping/nonmember"]').click();
    await page.waitForLoadState('load');
    await page.locator('#nonmember_name_name01').fill('姓03');
    await page.locator('#nonmember_name_name02').fill('名03');
    await page.locator('#nonmember_kana_kana01').fill('セイ');
    await page.locator('#nonmember_kana_kana02').fill('メイ');
    await page.locator('#nonmember_postal_code').fill('530-0001');
    await page.locator('#nonmember_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(500);
    await page.locator('#nonmember_address_addr01').fill('大阪市北区');
    await page.locator('#nonmember_address_addr02').fill('梅田2-4-9');
    await page.locator('#nonmember_phone_number').fill('111-111-111');
    await page.locator('#nonmember_email_first').fill('test@example.com');
    await page.locator('#nonmember_email_second').fill('test@example.com');
    await page.locator('button[type="submit"]').click();
    await page.waitForLoadState('load');

    await page.locator('#shopping-form button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');
    await expect(page.locator('p.ec-reportDescription')).toContainText(
      '試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。'
    );
  });

  // --- Order confirm: member purchase (10 attempts, shopping_confirm limiter) ---
  test.fixme('注文確認_会員購入', async ({ page }) => {
    // EF0901-UC01-T09: Member purchase confirm throttling
    // Requires 10 iterations of: add to cart -> checkout -> confirm -> complete
    // Then one more to trigger throttle on the confirm step
    test.setTimeout(300_000);

    const email = getTestCustomerEmail();
    await loginAsMember(page, email);

    for (let i = 0; i < 10; i++) {
      await page.goto('/products/detail/2');
      await page.waitForLoadState('load');
      await page.locator('#quantity').fill('1');
      await page.locator('.add-cart').click();
      await page.waitForTimeout(3000);
      await page.locator('div.ec-modal-box > div > a').click();
      await page.waitForLoadState('load');

      await page.goto('/cart');
      await page.waitForLoadState('load');
      await page.getByRole('link', { name: 'レジに進む' }).click();
      await page.waitForLoadState('load');

      await page.locator('#shopping-form button.ec-blockBtn--action').click();
      await page.waitForLoadState('load');

      await page.locator('#shopping-form button.ec-blockBtn--action').click();
      await page.waitForLoadState('load');
      await expect(page.locator('div.ec-pageHeader h1')).toContainText('ご注文完了');
    }

    // Trigger throttle
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');
    await page.locator('#quantity').fill('1');
    await page.locator('.add-cart').click();
    await page.waitForTimeout(3000);
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    await page.goto('/cart');
    await page.waitForLoadState('load');
    await page.getByRole('link', { name: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    await page.locator('#shopping-form button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');
    await expect(page.locator('p.ec-reportDescription')).toContainText(
      '試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。'
    );
  });

  // --- Order checkout: guest purchase (25 attempts, shopping_checkout limiter) ---
  // Note: confirmLimiter upper limit needs to be increased before running this test
  test.fixme('注文完了_非会員購入', async ({ page }) => {
    // EF0901-UC01-T10: Guest purchase checkout throttling
    test.setTimeout(600_000);

    for (let i = 0; i < 25; i++) {
      await page.goto('/products/detail/2');
      await page.waitForLoadState('load');
      await page.locator('#quantity').fill('1');
      await page.locator('.add-cart').click();
      await page.waitForTimeout(3000);
      await page.locator('div.ec-modal-box > div > a').click();
      await page.waitForLoadState('load');

      await page.goto('/cart');
      await page.waitForLoadState('load');
      await page.getByRole('link', { name: 'レジに進む' }).click();
      await page.waitForLoadState('load');

      await page.locator('a[href*="shopping/nonmember"]').click();
      await page.waitForLoadState('load');
      await page.locator('#nonmember_name_name01').fill('姓03');
      await page.locator('#nonmember_name_name02').fill('名03');
      await page.locator('#nonmember_kana_kana01').fill('セイ');
      await page.locator('#nonmember_kana_kana02').fill('メイ');
      await page.locator('#nonmember_postal_code').fill('530-0001');
      await page.locator('#nonmember_address_pref').selectOption({ value: '27' });
      await page.waitForTimeout(500);
      await page.locator('#nonmember_address_addr01').fill('大阪市北区');
      await page.locator('#nonmember_address_addr02').fill('梅田2-4-9');
      await page.locator('#nonmember_phone_number').fill('111-111-111');
      await page.locator('#nonmember_email_first').fill('test@example.com');
      await page.locator('#nonmember_email_second').fill('test@example.com');
      await page.locator('button[type="submit"]').click();
      await page.waitForLoadState('load');

      await page.locator('#shopping-form button.ec-blockBtn--action').click();
      await page.waitForLoadState('load');

      await page.locator('#shopping-form button.ec-blockBtn--action').click();
      await page.waitForLoadState('load');
      await expect(page.locator('div.ec-pageHeader h1')).toContainText('ご注文完了');
    }

    // Trigger throttle on checkout
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');
    await page.locator('#quantity').fill('1');
    await page.locator('.add-cart').click();
    await page.waitForTimeout(3000);
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    await page.goto('/cart');
    await page.waitForLoadState('load');
    await page.getByRole('link', { name: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    await page.locator('a[href*="shopping/nonmember"]').click();
    await page.waitForLoadState('load');
    await page.locator('#nonmember_name_name01').fill('姓03');
    await page.locator('#nonmember_name_name02').fill('名03');
    await page.locator('#nonmember_kana_kana01').fill('セイ');
    await page.locator('#nonmember_kana_kana02').fill('メイ');
    await page.locator('#nonmember_postal_code').fill('530-0001');
    await page.locator('#nonmember_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(500);
    await page.locator('#nonmember_address_addr01').fill('大阪市北区');
    await page.locator('#nonmember_address_addr02').fill('梅田2-4-9');
    await page.locator('#nonmember_phone_number').fill('111-111-111');
    await page.locator('#nonmember_email_first').fill('test@example.com');
    await page.locator('#nonmember_email_second').fill('test@example.com');
    await page.locator('button[type="submit"]').click();
    await page.waitForLoadState('load');

    await page.locator('#shopping-form button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');

    await page.locator('#shopping-form button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');
    await expect(page.locator('div.ec-cartRole__error')).toContainText(
      '購入処理で予期しないエラーが発生しました'
    );
  });

  // --- Order checkout: member purchase (10 attempts, shopping_checkout limiter) ---
  // Note: confirmLimiter upper limit needs to be increased before running this test
  test.fixme('注文完了_会員購入', async ({ page }) => {
    // EF0901-UC01-T11: Member purchase checkout throttling
    test.setTimeout(300_000);

    const email = getTestCustomerEmail();
    await loginAsMember(page, email);

    for (let i = 0; i < 10; i++) {
      await page.goto('/products/detail/2');
      await page.waitForLoadState('load');
      await page.locator('#quantity').fill('1');
      await page.locator('.add-cart').click();
      await page.waitForTimeout(3000);
      await page.locator('div.ec-modal-box > div > a').click();
      await page.waitForLoadState('load');

      await page.goto('/cart');
      await page.waitForLoadState('load');
      await page.getByRole('link', { name: 'レジに進む' }).click();
      await page.waitForLoadState('load');

      await page.locator('#shopping-form button.ec-blockBtn--action').click();
      await page.waitForLoadState('load');

      await page.locator('#shopping-form button.ec-blockBtn--action').click();
      await page.waitForLoadState('load');
      await expect(page.locator('div.ec-pageHeader h1')).toContainText('ご注文完了');
    }

    // Trigger throttle
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');
    await page.locator('#quantity').fill('1');
    await page.locator('.add-cart').click();
    await page.waitForTimeout(3000);
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    await page.goto('/cart');
    await page.waitForLoadState('load');
    await page.getByRole('link', { name: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    await page.locator('#shopping-form button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');

    await page.locator('#shopping-form button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');
    await expect(page.locator('div.ec-cartRole__error')).toContainText(
      '購入処理で予期しないエラーが発生しました'
    );
  });

  // --- Order flow: add delivery address (10 additions in order flow) ---
  test.fixme('order_お届け先追加', async ({ page }) => {
    // EF0901-UC01-T16: Add delivery address during order flow throttling
    // This requires login, adding to cart, going through checkout,
    // then repeatedly adding delivery addresses in the multiple shipping page
    test.setTimeout(300_000);

    const email = getTestCustomerEmail();
    await loginAsMember(page, email);

    // Add product to cart
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');
    await page.locator('#quantity').fill('1');
    await page.locator('.add-cart').click();
    await page.waitForTimeout(3000);
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    // Go to checkout
    await page.goto('/cart');
    await page.waitForLoadState('load');
    await page.getByRole('link', { name: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    // Go to multiple shipping / add delivery address page
    // Click 'お届け先を追加' link
    const addDeliveryLink = page.locator('a[href*="shopping/shipping_multiple"]');
    if (await addDeliveryLink.count() === 0) {
      test.skip(true, 'Multiple shipping link not found on shopping page');
      return;
    }
    await addDeliveryLink.click();
    await page.waitForLoadState('load');

    for (let i = 0; i < 10; i++) {
      // Click add new delivery address
      await page.locator('a[href*="shopping/shipping_multiple_edit"]').click();
      await page.waitForLoadState('load');

      // Fill address form
      await page.locator('#customer_address_name_name01').fill('あいおい0302');
      await page.locator('#customer_address_name_name02').fill('名0302');
      await page.locator('#customer_address_kana_kana01').fill('セイ');
      await page.locator('#customer_address_kana_kana02').fill('メイ');
      await page.locator('#customer_address_postal_code').fill('530-0001');
      await page.locator('#customer_address_address_pref').selectOption({ value: '27' });
      await page.waitForTimeout(500);
      await page.locator('#customer_address_address_addr01').fill('大阪市北区2');
      await page.locator('#customer_address_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F2');
      await page.locator('#customer_address_phone_number').fill('222-222-222');
      await page.locator('button[type="submit"]').click();
      await page.waitForLoadState('load');
      await page.waitForTimeout(1000);
    }

    // Trigger throttle
    await page.locator('a[href*="shopping/shipping_multiple_edit"]').click();
    await page.waitForLoadState('load');

    await page.locator('#customer_address_name_name01').fill('あいおい0302');
    await page.locator('#customer_address_name_name02').fill('名0302');
    await page.locator('#customer_address_kana_kana01').fill('セイ');
    await page.locator('#customer_address_kana_kana02').fill('メイ');
    await page.locator('#customer_address_postal_code').fill('530-0001');
    await page.locator('#customer_address_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(500);
    await page.locator('#customer_address_address_addr01').fill('大阪市北区2');
    await page.locator('#customer_address_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F2');
    await page.locator('#customer_address_phone_number').fill('222-222-222');
    await page.locator('button[type="submit"]').click();
    await page.waitForLoadState('load');

    await expect(page.locator('p.ec-reportDescription')).toContainText(
      '試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。'
    );
  });

  // --- Order flow: change delivery address (10 changes in order flow) ---
  test.fixme('order_お届け先変更', async ({ page }) => {
    // EF0901-UC01-T17: Change delivery address during order flow throttling
    // Requires login, adding to cart, going through checkout,
    // then repeatedly changing the delivery address
    test.setTimeout(300_000);

    const email = getTestCustomerEmail();
    await loginAsMember(page, email);

    // Add product to cart
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');
    await page.locator('#quantity').fill('1');
    await page.locator('.add-cart').click();
    await page.waitForTimeout(3000);
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    // Go to checkout
    await page.goto('/cart');
    await page.waitForLoadState('load');
    await page.getByRole('link', { name: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    for (let i = 0; i < 10; i++) {
      // Click 'お届け先を変更' link
      const changeDeliveryLink = page.locator('a[href*="shopping/shipping_change"]').first();
      if (await changeDeliveryLink.count() === 0) {
        test.skip(true, 'Shipping change link not found on shopping page');
        return;
      }
      await changeDeliveryLink.click();
      await page.waitForLoadState('load');

      // Fill new delivery address form
      await page.locator('#customer_address_name_name01').fill('あいおい0302');
      await page.locator('#customer_address_name_name02').fill('名0302');
      await page.locator('#customer_address_kana_kana01').fill('セイ');
      await page.locator('#customer_address_kana_kana02').fill('メイ');
      await page.locator('#customer_address_postal_code').fill('530-0001');
      await page.locator('#customer_address_address_pref').selectOption({ value: '27' });
      await page.waitForTimeout(500);
      await page.locator('#customer_address_address_addr01').fill('大阪市北区2');
      await page.locator('#customer_address_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F2');
      await page.locator('#customer_address_phone_number').fill('222-222-222');
      await page.locator('button[type="submit"]').click();
      await page.waitForLoadState('load');
      await page.waitForTimeout(1000);
    }

    // Trigger throttle
    const changeDeliveryLink = page.locator('a[href*="shopping/shipping_change"]').first();
    await changeDeliveryLink.click();
    await page.waitForLoadState('load');

    await page.locator('#customer_address_name_name01').fill('あいおい0302');
    await page.locator('#customer_address_name_name02').fill('名0302');
    await page.locator('#customer_address_kana_kana01').fill('セイ');
    await page.locator('#customer_address_kana_kana02').fill('メイ');
    await page.locator('#customer_address_postal_code').fill('530-0001');
    await page.locator('#customer_address_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(500);
    await page.locator('#customer_address_address_addr01').fill('大阪市北区2');
    await page.locator('#customer_address_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F2');
    await page.locator('#customer_address_phone_number').fill('222-222-222');
    await page.locator('button[type="submit"]').click();
    await page.waitForLoadState('load');

    await expect(page.locator('p.ec-reportDescription')).toContainText(
      '試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。'
    );
  });

  // --- Admin 2FA throttling ---
  test('管理画面二段階認証', async ({ page }) => {
    test.setTimeout(180_000);

    // First, log in as admin to create a 2FA-enabled member
    const browser = page.context().browser()!;
    const adminContext = await browser.newContext();
    const adminPage = await adminContext.newPage();

    await adminPage.goto(`/${adminRoute}/`);
    await adminPage.waitForLoadState('load');
    await adminPage.locator('#login_id').fill(process.env.ADMIN_USER || 'admin');
    await adminPage.locator('#password').fill(process.env.ADMIN_PASSWORD || 'password');
    await adminPage.getByRole('button', { name: 'ログイン' }).click();
    await expect(adminPage.locator('.c-pageTitle__titles')).toContainText('ホーム', { timeout: 30_000 });

    // Create a new member with 2FA enabled
    const loginId = 'admin_2fa_' + Date.now().toString(36);
    const memberPassword = 'password1234';

    await adminPage.goto(`/${adminRoute}/setting/system/member/new`);
    await adminPage.waitForLoadState('load');
    await expect(adminPage.locator('.c-pageTitle')).toContainText('メンバー登録');

    await adminPage.locator('#admin_member_name').fill('2FA管理者');
    await adminPage.locator('#admin_member_department').fill('admin_throttling');
    await adminPage.locator('#admin_member_login_id').fill(loginId);
    await adminPage.locator('#admin_member_plain_password_first').fill(memberPassword);
    await adminPage.locator('#admin_member_plain_password_second').fill(memberPassword);
    await adminPage.locator('#admin_member_Authority').selectOption({ label: 'システム管理者' });
    await adminPage.locator('label[for="admin_member_Work_1"]').click();
    await adminPage.locator('label[for="admin_member_two_factor_auth_enabled"]').click();

    await adminPage.locator('#member_form .c-conversionArea__container button').click();
    await adminPage.waitForLoadState('load');
    await expect(adminPage.locator('.c-contentsArea .alert-success')).toContainText('保存しました');

    // Log out admin
    await adminPage.goto(`/${adminRoute}/logout`);
    await adminPage.waitForLoadState('load');
    await adminContext.close();

    // Log in as the new 2FA member
    await page.goto(`/${adminRoute}/`);
    await page.waitForLoadState('load');
    await page.locator('#login_id').fill(loginId);
    await page.locator('#password').fill(memberPassword);
    await page.getByRole('button', { name: 'ログイン' }).click();
    await page.waitForLoadState('load');

    // Should be on the 2FA setup page with QR code
    // Get the TOTP secret from the hidden field
    const secret = await page.locator('#admin_two_factor_auth_auth_key').inputValue();
    expect(secret).toBeTruthy();

    // Generate a valid TOTP code and complete setup
    const validCode = authenticator.generate(secret);
    await page.locator('#admin_two_factor_auth_device_token').fill(validCode);
    await page.getByRole('button', { name: '登録' }).click();
    await page.waitForLoadState('load');

    // Should be redirected to admin home
    await expect(page.locator('.c-pageTitle__titles')).toContainText('ホーム', { timeout: 30_000 });

    // Log out and clear the 2FA cookie
    await page.goto(`/${adminRoute}/logout`);
    await page.waitForLoadState('load');

    // Clear 2FA cookie
    const cookies = await page.context().cookies();
    const twofaCookies = cookies.filter(c => c.name === 'eccube_2fa');
    if (twofaCookies.length > 0) {
      await page.context().clearCookies({ name: 'eccube_2fa' });
    }

    // Log in again
    await page.goto(`/${adminRoute}/`);
    await page.waitForLoadState('load');
    await page.locator('#login_id').fill(loginId);
    await page.locator('#password').fill(memberPassword);
    await page.getByRole('button', { name: 'ログイン' }).click();
    await page.waitForLoadState('load');

    // Should be on the 2FA token entry page (not setup page this time)
    // Enter wrong tokens 5 times
    for (let i = 0; i < 5; i++) {
      await page.locator('#admin_two_factor_auth_device_token').fill(`${100000 + i}`);
      await page.getByRole('button', { name: '認証' }).click();
      await page.waitForLoadState('load');
      await expect(page.locator('body')).toContainText('トークンに誤りがあります。再度入力してください。');
    }

    // One more wrong attempt should trigger throttling
    await page.locator('#admin_two_factor_auth_device_token').fill('999999');
    await page.getByRole('button', { name: '認証' }).click();
    await page.waitForLoadState('load');

    await expect(page.locator('body')).toContainText(
      '試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。'
    );
  });
});
