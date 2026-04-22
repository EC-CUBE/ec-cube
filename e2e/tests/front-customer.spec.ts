import { test, expect } from '@playwright/test';

test.describe('Front Customer Registration (EF04)', () => {

  test('EF0401-UC01-T01 会員登録 入力確認画面表示', async ({ page }) => {
    await page.goto('/entry');
    await page.waitForLoadState('load');

    const email = `test_${Date.now()}@example.com`;

    // 会員情報入力フォームに情報を入力
    await page.locator('#entry_name_name01').fill('姓');
    await page.locator('#entry_name_name02').fill('名');
    await page.locator('#entry_kana_kana01').fill('セイ');
    await page.locator('#entry_kana_kana02').fill('メイ');
    await page.locator('#entry_postal_code').fill('530-0001');
    await page.locator('#entry_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(1000); // 郵便番号の自動入力を待つ
    await page.locator('#entry_address_addr01').fill('大阪市北区');
    await page.locator('#entry_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
    await page.locator('#entry_phone_number').fill('111-111-111');
    await page.locator('#entry_email_first').fill(email);
    await page.locator('#entry_email_second').fill(email);
    await page.locator('#entry_plain_password_first').fill('password1234');
    await page.locator('#entry_plain_password_second').fill('password1234');
    await page.locator('#entry_job').selectOption({ value: '1' });
    await page.locator('#entry_user_policy_check').check();

    // 「同意する」ボタンを押下
    await page.locator('button.ec-blockBtn--action[type="submit"]').click();
    await page.waitForLoadState('load');

    // 確認画面に遷移
    await expect(page.locator('.ec-registerRole')).toBeVisible();

    // 入力した情報が表示される
    await expect(page.locator('.ec-registerRole')).toContainText('姓');
    await expect(page.locator('.ec-registerRole')).toContainText('名');
    await expect(page.locator('.ec-registerRole')).toContainText('111111111');
    await expect(page.locator('.ec-registerRole')).toContainText(email);
  });

  test('EF0401-UC01-T03 会員登録 異常パターン 入力ミス', async ({ page }) => {
    await page.goto('/entry');
    await page.waitForLoadState('load');

    const email = `test_${Date.now()}@example.com`;

    // 姓を空にして入力（異常パターン）
    await page.locator('#entry_name_name01').fill('');
    await page.locator('#entry_name_name02').fill('名');
    await page.locator('#entry_kana_kana01').fill('セイ');
    await page.locator('#entry_kana_kana02').fill('メイ');
    await page.locator('#entry_postal_code').fill('530-0001');
    await page.locator('#entry_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(1000);
    await page.locator('#entry_address_addr01').fill('大阪市北区');
    await page.locator('#entry_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
    await page.locator('#entry_phone_number').fill('111-111-111');
    await page.locator('#entry_email_first').fill(email);
    await page.locator('#entry_email_second').fill(email);
    await page.locator('#entry_plain_password_first').fill('password1234');
    await page.locator('#entry_plain_password_second').fill('password1234');

    // 「同意する」ボタンを押下
    await page.locator('button.ec-blockBtn--action[type="submit"]').click();
    await page.waitForLoadState('load');

    // 会員登録画面のままで姓フィールドのバリデーションエラーが表示される
    await expect(page.locator('.ec-pageHeader h1')).toContainText('新規会員登録');
    // The name input container gets the 'error' class and an ec-errorMessage is rendered for the empty last name
    const nameInputContainer = page.locator('#entry_name_name01').locator('..');
    await expect(nameInputContainer).toHaveClass(/error/);
    await expect(nameInputContainer.locator('.ec-errorMessage')).toBeVisible();
  });

  test('EF0401-UC01-T04 会員登録 同意しないボタン', async ({ page }) => {
    await page.goto('/entry');
    await page.waitForLoadState('load');

    // 「同意しない」ボタンを押下
    await page.locator('a.ec-blockBtn--cancel').click();
    await page.waitForLoadState('load');

    // トップページに戻る
    await expect(page).toHaveURL(/\/$/);
    await expect(page.locator('.ec-newsRole')).toBeVisible();
  });

  test('EF0401-UC01-T05 会員登録 戻るボタン', async ({ page }) => {
    await page.goto('/entry');
    await page.waitForLoadState('load');

    const email = `test_${Date.now()}@example.com`;

    // 会員情報入力
    await page.locator('#entry_name_name01').fill('姓');
    await page.locator('#entry_name_name02').fill('名');
    await page.locator('#entry_kana_kana01').fill('セイ');
    await page.locator('#entry_kana_kana02').fill('メイ');
    await page.locator('#entry_postal_code').fill('530-0001');
    await page.locator('#entry_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(1000);
    await page.locator('#entry_address_addr01').fill('大阪市北区');
    await page.locator('#entry_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
    await page.locator('#entry_phone_number').fill('111-111-111');
    await page.locator('#entry_email_first').fill(email);
    await page.locator('#entry_email_second').fill(email);
    await page.locator('#entry_plain_password_first').fill('password1234');
    await page.locator('#entry_plain_password_second').fill('password1234');
    await page.locator('#entry_job').selectOption({ value: '1' });
    await page.locator('#entry_user_policy_check').check();

    // 「同意する」ボタンで確認画面へ
    await page.locator('button.ec-blockBtn--action[type="submit"]').click();
    await page.waitForLoadState('load');

    // 確認画面が表示される
    await expect(page.locator('.ec-registerRole')).toBeVisible();

    // 「戻る」ボタンを押下
    await page.locator('.ec-registerRole form button.ec-blockBtn--cancel').click();
    await page.waitForLoadState('load');

    // 入力画面に戻る
    await expect(page.locator('.ec-pageHeader h1')).toContainText('新規会員登録');
  });

  test('EF0401-UC01-T02 会員登録 異常パターン 重複メール', async ({ page }) => {
    // まず会員登録を確認画面まで進める(既存メールアドレスを使う)
    await page.goto('/entry');
    await page.waitForLoadState('load');

    const existingEmail = 'playwright@test.test'; // 既存のテストユーザーのメールアドレス

    await page.locator('#entry_name_name01').fill('姓');
    await page.locator('#entry_name_name02').fill('名');
    await page.locator('#entry_kana_kana01').fill('セイ');
    await page.locator('#entry_kana_kana02').fill('メイ');
    await page.locator('#entry_postal_code').fill('530-0001');
    await page.locator('#entry_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(1000);
    await page.locator('#entry_address_addr01').fill('大阪市北区');
    await page.locator('#entry_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
    await page.locator('#entry_phone_number').fill('111-111-111');
    await page.locator('#entry_email_first').fill(existingEmail);
    await page.locator('#entry_email_second').fill(existingEmail);
    await page.locator('#entry_plain_password_first').fill('password1234');
    await page.locator('#entry_plain_password_second').fill('password1234');

    // 「同意する」ボタンを押下
    await page.locator('button.ec-blockBtn--action[type="submit"]').click();
    await page.waitForLoadState('load');

    // 重複メールアドレスエラーが表示される
    await expect(page.locator('.ec-registerRole')).toContainText('このメールアドレスは利用できません');
  });

  test('EF0401-UC01-T06 会員登録正常 ログイン', async ({ page }) => {
    // 会員登録
    await page.goto('/entry');
    await page.waitForLoadState('load');

    const newEmail = `register_login_${Date.now()}@test.test`;

    await page.locator('#entry_name_name01').fill('姓');
    await page.locator('#entry_name_name02').fill('名');
    await page.locator('#entry_kana_kana01').fill('セイ');
    await page.locator('#entry_kana_kana02').fill('メイ');
    await page.locator('#entry_postal_code').fill('530-0001');
    await page.locator('#entry_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(1000);
    await page.locator('#entry_address_addr01').fill('大阪市北区');
    await page.locator('#entry_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
    await page.locator('#entry_phone_number').fill('111-111-111');
    await page.locator('#entry_email_first').fill(newEmail);
    await page.locator('#entry_email_second').fill(newEmail);
    await page.locator('#entry_plain_password_first').fill('password1234');
    await page.locator('#entry_plain_password_second').fill('password1234');
    await page.locator('#entry_job').selectOption({ value: '1' });
    await page.locator('#entry_user_policy_check').check();

    // 確認画面へ
    await page.locator('button.ec-blockBtn--action[type="submit"]').click();
    await page.waitForLoadState('load');

    // 確認画面に遷移
    await expect(page.locator('.ec-registerRole')).toBeVisible();
    await expect(page.locator('.ec-registerRole')).toContainText('姓');
    await expect(page.locator('.ec-registerRole')).toContainText(newEmail);

    // 「会員登録をする」ボタンを押下
    await page.locator('.ec-registerRole form button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');

    // 仮会員登録完了 - トップページへ
    await page.locator('a.ec-blockBtn--cancel').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-newsRole')).toBeVisible();

    // アクティベーション(メール確認はスキップ。DBを直接操作してアクティベートする)
    // DB上のCustomerStatusをactivateするためにadminから会員を検索して本会員にする
    // admin認証を使うためにブラウザコンテキストを分ける
    const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
    const browser = page.context().browser()!;
    const fs = await import('fs');
    const path = await import('path');
    const authFile = path.join(__dirname, '..', '.auth', 'admin.json');
    const storageState = JSON.parse(fs.readFileSync(authFile, 'utf-8'));
    const adminContext = await browser.newContext({ storageState });
    const adminPage = await adminContext.newPage();

    // 管理画面から会員を検索
    await adminPage.goto(`/${adminRoute}/customer`);
    await adminPage.waitForLoadState('load');
    await adminPage.locator('#admin_search_customer_multi').fill(newEmail);
    await adminPage.locator('button.btn-ec-conversion').click();
    await adminPage.waitForLoadState('load');

    // 会員編集ページへ (検索結果のテーブルの最初の行のリンク)
    await adminPage.locator('table.table tbody tr td a').first().click();
    await adminPage.waitForLoadState('load');

    // 本会員に変更
    await adminPage.locator('#admin_customer_status').selectOption({ value: '2' });
    await adminPage.locator('button.btn-ec-conversion').click();
    await adminPage.waitForLoadState('load');

    await adminContext.close();

    // ログインを試す
    await page.goto('/mypage/login');
    await page.waitForLoadState('load');
    await page.locator('input[name="login_email"]').fill(newEmail);
    await page.locator('input[name="login_pass"]').fill('password1234');
    await page.locator('#login_mypage button[type="submit"]').click();
    await page.waitForLoadState('load');

    // ログイン成功確認 - マイページに遷移できる
    await page.goto('/mypage');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-pageHeader h1')).toContainText('マイページ');
  });

  test('EF0401-UC01-T07 会員登録正常 カート', async ({ page }) => {
    // ログアウト状態を確認
    await page.goto('/logout');
    await page.waitForLoadState('load');

    // 商品をカートに入れる
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');
    await page.locator('#quantity').fill('1');
    await page.locator('.add-cart').click();
    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    // レジに進む
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    // ショッピングログインページ
    await expect(page).toHaveURL(/\/shopping\/login/);

    // 新規会員登録リンクをクリック (ショッピングログインページの右カラム)
    await page.getByRole('link', { name: '新規会員登録', exact: true }).click();
    await page.waitForLoadState('load');

    // 会員登録ページに遷移
    await expect(page).toHaveURL(/\/entry/);

    const newEmail = `register_cart_${Date.now()}@test.test`;

    // 会員情報入力
    await page.locator('#entry_name_name01').fill('姓');
    await page.locator('#entry_name_name02').fill('名');
    await page.locator('#entry_kana_kana01').fill('セイ');
    await page.locator('#entry_kana_kana02').fill('メイ');
    await page.locator('#entry_postal_code').fill('530-0001');
    await page.locator('#entry_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(1000);
    await page.locator('#entry_address_addr01').fill('大阪市北区');
    await page.locator('#entry_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
    await page.locator('#entry_phone_number').fill('111-111-111');
    await page.locator('#entry_email_first').fill(newEmail);
    await page.locator('#entry_email_second').fill(newEmail);
    await page.locator('#entry_plain_password_first').fill('password1234');
    await page.locator('#entry_plain_password_second').fill('password1234');
    await page.locator('#entry_job').selectOption({ value: '1' });
    await page.locator('#entry_user_policy_check').check();

    // 確認画面へ
    await page.locator('button.ec-blockBtn--action[type="submit"]').click();
    await page.waitForLoadState('load');

    // 確認画面
    await expect(page.locator('.ec-registerRole')).toBeVisible();
    await expect(page.locator('.ec-registerRole')).toContainText(newEmail);

    // 「会員登録をする」ボタンを押下
    await page.locator('.ec-registerRole form button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');

    // 仮会員登録完了 - トップページへ
    await page.locator('a.ec-blockBtn--cancel').click();
    await page.waitForLoadState('load');

    // DBで直接アクティベート(adminで操作)
    const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
    const browser = page.context().browser()!;
    const fs = await import('fs');
    const path = await import('path');
    const authFile = path.join(__dirname, '..', '.auth', 'admin.json');
    const storageState = JSON.parse(fs.readFileSync(authFile, 'utf-8'));
    const adminContext = await browser.newContext({ storageState });
    const adminPage = await adminContext.newPage();

    await adminPage.goto(`/${adminRoute}/customer`);
    await adminPage.waitForLoadState('load');
    await adminPage.locator('#admin_search_customer_multi').fill(newEmail);
    await adminPage.locator('button.btn-ec-conversion').click();
    await adminPage.waitForLoadState('load');

    await adminPage.locator('table.table tbody tr td a').first().click();
    await adminPage.waitForLoadState('load');

    await adminPage.locator('#admin_customer_status').selectOption({ value: '2' });
    await adminPage.locator('button.btn-ec-conversion').click();
    await adminPage.waitForLoadState('load');

    await adminContext.close();

    // カートへ戻り、レジに進む
    await page.goto('/cart');
    await page.waitForLoadState('load');
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    // ログイン
    await page.locator('input[name="login_email"]').fill(newEmail);
    await page.locator('input[name="login_pass"]').fill('password1234');
    await page.getByRole('button', { name: 'ログイン' }).click();
    await page.waitForLoadState('load');

    // ご注文手続きページに遷移
    await expect(page).toHaveURL(/\/shopping/);
  });

  test('EF0404-UC01-T01 会員登録 利用規約リンク', async ({ page }) => {
    await page.goto('/entry');
    await page.waitForLoadState('load');

    // 利用規約リンクをクリック
    const [newPage] = await Promise.all([
      page.context().waitForEvent('page'),
      page.locator('.ec-registerRole form a.ec-link').click(),
    ]);

    await newPage.waitForLoadState();

    // 利用規約ページに遷移
    expect(newPage.url()).toContain('/help/agreement');
    await newPage.close();
  });
});
