import { test, expect, Page } from '@playwright/test';
import { ADMIN_PASSWORD, ADMIN_ROUTE, ADMIN_USER, NORMALIZED_PASSWORD, RAW_PASSWORD } from '../config/default.config';

/**
 * NIST SP 800-63B-4 対応(#6488)の E2E。
 * - NFKC 正規化の往復: 半角カナで登録した会員が, 半角(非正規化)/全角(正規化済み)
 *   どちらの入力でもフロントログインできること。
 * - 入力検証: 15文字未満・ブロックリスト掲載パスワードが会員登録で拒否されること。
 *
 * 登録は管理画面から行う(フロント登録のメール確認フローを避けるため)。
 * 管理会員登録フォームもフロント登録と同じ RepeatedPasswordType を経由し,
 * PRE_SUBMIT で NFKC 正規化されるため, 保存値は正規化後の文字列になる。
 */

const adminRoute = ADMIN_ROUTE;

/**
 * 管理画面から本会員を作成し, メールアドレスを返す。
 */
async function createActiveCustomerViaAdmin(page: Page, password: string): Promise<string> {
  const email = `nist_${Date.now()}@example.com`;

  const adminPage = await page.context().newPage();
  await adminPage.goto(`/${adminRoute}/`);
  await adminPage.waitForLoadState('load');
  await adminPage.locator('#login_id').fill(ADMIN_USER);
  await adminPage.locator('#password').fill(ADMIN_PASSWORD);
  await adminPage.getByRole('button', { name: 'ログイン' }).click();
  await adminPage.waitForLoadState('load');

  await adminPage.goto(`/${adminRoute}/customer/new`);
  await adminPage.waitForLoadState('load');

  await adminPage.locator('#admin_customer_name_name01').fill('正規化');
  await adminPage.locator('#admin_customer_name_name02').fill('会員');
  await adminPage.locator('#admin_customer_kana_kana01').fill('セイキカ');
  await adminPage.locator('#admin_customer_kana_kana02').fill('カイイン');
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

  await adminPage.locator('.c-conversionArea button[type="submit"]').first().click();
  await adminPage.waitForLoadState('load');
  await expect(adminPage.locator('.alert-success')).toBeVisible();
  await adminPage.close();

  return email;
}

/**
 * フロントの会員ログインを試行する。
 */
async function loginAtFront(page: Page, email: string, password: string) {
  await page.goto('/mypage/login');
  await page.waitForLoadState('load');
  await page.locator('input[name="login_email"]').fill(email);
  await page.locator('input[name="login_pass"]').fill(password);
  await page.getByRole('button', { name: 'ログイン' }).click();
  await page.waitForLoadState('load');
}

/**
 * /mypage にアクセスし, 認証済み(ログインへリダイレクトされない)であることを確認する。
 */
async function assertLoggedIn(page: Page) {
  await page.goto('/mypage');
  await page.waitForLoadState('load');
  await expect(page).toHaveURL(/\/mypage\/?$/);
}

async function logoutFront(page: Page) {
  await page.goto('/logout');
  await page.waitForLoadState('load');
}

/**
 * /entry の会員登録フォームを, パスワード以外を有効値で埋めて送信する。
 */
async function submitEntryForm(page: Page, password: string) {
  await page.goto('/entry');
  await page.waitForLoadState('load');

  const email = `nist_entry_${Date.now()}@example.com`;
  await page.locator('#entry_name_name01').fill('姓');
  await page.locator('#entry_name_name02').fill('名');
  await page.locator('#entry_kana_kana01').fill('セイ');
  await page.locator('#entry_kana_kana02').fill('メイ');
  await page.locator('#entry_postal_code').fill('530-0001');
  await page.locator('#entry_address_pref').selectOption({ value: '27' });
  await page.waitForTimeout(1000);
  await page.locator('#entry_address_addr01').fill('大阪市北区');
  await page.locator('#entry_address_addr02').fill('梅田2-4-9');
  await page.locator('#entry_phone_number').fill('111-111-111');
  await page.locator('#entry_email_first').fill(email);
  await page.locator('#entry_email_second').fill(email);
  await page.locator('#entry_plain_password_first').fill(password);
  await page.locator('#entry_plain_password_second').fill(password);
  await page.locator('#entry_job').selectOption({ value: '1' });
  await page.locator('#entry_user_policy_check').check();

  await page.locator('button.ec-blockBtn--action[type="submit"]').click();
  await page.waitForLoadState('load');
}

test.describe('Front Password NIST (EF04 #6488)', () => {
  test('NFKC 正規化: 半角カナで登録した会員が半角でも全角でもログインできる', async ({ page }) => {
    // 前提: 半角カナと全角は別表記だが NFKC で同一になる
    expect(RAW_PASSWORD).not.toBe(NORMALIZED_PASSWORD);

    // 半角カナのパスワードで会員作成(保存時に NFKC 正規化される)
    const email = await createActiveCustomerViaAdmin(page, RAW_PASSWORD);

    // 半角カナ(非正規化)のままでログイン成功
    await loginAtFront(page, email, RAW_PASSWORD);
    await assertLoggedIn(page);

    await logoutFront(page);

    // 全角(正規化済み)でもログイン成功
    await loginAtFront(page, email, NORMALIZED_PASSWORD);
    await assertLoggedIn(page);
  });

  test('入力検証: 15文字未満のパスワードは会員登録で拒否される', async ({ page }) => {
    await submitEntryForm(page, 'Short12345'); // 10文字

    // 確認画面へ進まず入力ページに留まる(確認画面では password は hidden になる),
    // かつ長さエラーが表示される
    await expect(page.locator('#entry_plain_password_first')).toBeVisible();
    await expect(page.locator('body')).toContainText('15文字以上');
  });

  test('入力検証: ブロックリスト掲載パスワードは会員登録で拒否される', async ({ page }) => {
    await submitEntryForm(page, 'passwordpassword'); // 16文字・ブロックリスト掲載

    await expect(page.locator('#entry_plain_password_first')).toBeVisible();
    await expect(page.locator('body')).toContainText('推測されやすいパスワード');
  });
});
