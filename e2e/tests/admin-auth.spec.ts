import { test, expect } from '@playwright/test';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';

// 認証テストはログイン/ログアウトを繰り返すため、共有storageStateを使わない
test.use({ storageState: { cookies: [], origins: [] } });

async function loginAsAdmin(page: import('@playwright/test').Page, loginId = process.env.ADMIN_USER || 'admin', password = process.env.ADMIN_PASSWORD || 'password') {
  await page.goto(`/${adminRoute}/`);
  await page.locator('#login_id').fill(loginId);
  await page.locator('#password').fill(password);
  await page.getByRole('button', { name: 'ログイン' }).click();
  await expect(page.locator('.c-pageTitle__titles')).toContainText('ホーム', { timeout: 30_000 });
}

async function logoutAsAdmin(page: import('@playwright/test').Page) {
  await page.goto(`/${adminRoute}/logout`);
  await page.waitForLoadState('load');
}

async function submitLoginForm(page: import('@playwright/test').Page, loginId: string, password: string) {
  await page.goto(`/${adminRoute}/`);
  await page.locator('#login_id').fill(loginId);
  await page.locator('#password').fill(password);
  await page.getByRole('button', { name: 'ログイン' }).click();
  await page.waitForLoadState('load');
}

test.describe('Admin Authentication (EA02)', () => {
  // パスワード変更テストが他テストの admin パスワードに影響するため serial 実行
  // 非稼働テストを先に、パスワード変更テストを最後に配置
  test.describe.configure({ mode: 'serial' });

  test('authentication_パスワード認証 異常系', async ({ page }) => {
    await submitLoginForm(page, 'invalid', 'invalidpassword');
    await expect(page.locator('#form1 > div:nth-child(5) > span')).toContainText('ログインできませんでした。');
  });

  test('authentication_最終ログイン日時確認', async ({ page }) => {
    await loginAsAdmin(page);

    await page.locator('header.c-headerBar a.c-headerBar__userMenu').click();
    const popoverText = page.locator('.popover .popover-body > p');
    await expect(popoverText).toBeVisible({ timeout: 5_000 });
    await expect(popoverText).toContainText(/\d{4}\/\d{1,2}\/\d{1,2} \d{1,2}:\d{2}/);
  });

  test('authentication_非稼働_削除', async ({ page }) => {
    const loginId = 'not_active_test';
    const memberName = '非稼働テストメンバー';
    const memberPassword = 'memberPass1234';

    await loginAsAdmin(page);

    // 非稼働ユーザを作成する
    await page.goto(`/${adminRoute}/setting/system/member/new`);
    await expect(page.locator('.c-pageTitle')).toContainText('メンバー登録');
    await page.locator('#admin_member_name').fill(memberName);
    await page.locator('#admin_member_department').fill('テスト部');
    await page.locator('#admin_member_login_id').fill(loginId);
    await page.locator('#admin_member_plain_password_first').fill(memberPassword);
    await page.locator('#admin_member_plain_password_second').fill(memberPassword);
    await page.locator('#admin_member_Authority').selectOption({ label: 'システム管理者' });
    await page.locator('#admin_member_Work_0').check(); // 非稼働
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // 非稼働ユーザではログインできないことを確認
    await logoutAsAdmin(page);
    await submitLoginForm(page, loginId, memberPassword);
    await expect(page.locator('#form1 > div:nth-child(5) > span')).toContainText('ログインできませんでした。');

    // admin でログインし直して稼働にする
    await loginAsAdmin(page);
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.locator(`table tr:has-text("${memberName}") a[href*="/edit"]`).click();
    await expect(page.locator('.c-pageTitle')).toContainText('メンバー登録');
    await page.locator('#admin_member_Work_1').check(); // 稼働
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');

    // 稼働ユーザとしてログインできることを確認
    await logoutAsAdmin(page);
    await loginAsAdmin(page, loginId, memberPassword);
    await logoutAsAdmin(page);

    // admin でログインし直してユーザを削除する
    await loginAsAdmin(page);
    await page.goto(`/${adminRoute}/setting/system/member`);
    const memberRow = page.locator(`table tr:has-text("${memberName}")`);
    await memberRow.locator('.action-delete').click();
    await page.waitForTimeout(500);
    await page.locator('.modal .btn-ec-delete').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('削除しました');

    // 削除されたユーザではログインできないことを確認
    await logoutAsAdmin(page);
    await submitLoginForm(page, loginId, memberPassword);
    await expect(page.locator('#form1 > div:nth-child(5) > span')).toContainText('ログインできませんでした。');
  });

  // パスワード変更テストは admin パスワードを変更するため最後に配置
  test('authentication_パスワード変更', async ({ page }) => {
    const password1 = 'testPassword1234';
    const password2 = 'anotherPass5678';

    await loginAsAdmin(page);

    // メンバー管理からパスワードを password1 に変更する
    await page.goto(`/${adminRoute}/setting/system/member`);
    await expect(page.locator('.c-pageTitle')).toContainText('メンバー管理');
    await page.locator('table tr:has-text("管理者") a[href*="/edit"]').first().click();
    await expect(page.locator('.c-pageTitle')).toContainText('メンバー登録');
    await page.locator('#admin_member_plain_password_first').fill(password1);
    await page.locator('#admin_member_plain_password_second').fill(password1);
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // ログアウトして旧パスワードではログインできないことを確認
    await logoutAsAdmin(page);
    await submitLoginForm(page, 'admin', 'password');
    await expect(page.locator('#form1 > div:nth-child(5) > span')).toContainText('ログインできませんでした。');

    // password1 でログインできることを確認
    await loginAsAdmin(page, 'admin', password1);

    // 画面右上のパスワード変更で password2 に変更する
    await page.goto(`/${adminRoute}/change_password`);
    await expect(page.locator('.c-pageTitle')).toContainText('パスワード変更');
    await page.locator('#admin_change_password_current_password').fill(password1);
    await page.locator('#admin_change_password_change_password_first').fill(password2);
    await page.locator('#admin_change_password_change_password_second').fill(password2);
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('パスワードを更新しました');

    // password1 ではログインできないことを確認
    await logoutAsAdmin(page);
    await submitLoginForm(page, 'admin', password1);
    await expect(page.locator('#form1 > div:nth-child(5) > span')).toContainText('ログインできませんでした。');

    // password2 でログインできることを確認
    await loginAsAdmin(page, 'admin', password2);
  });
});
