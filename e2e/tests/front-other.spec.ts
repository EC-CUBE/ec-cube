import { test, expect } from '@playwright/test';
import fs from 'fs';
import path from 'path';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';

/**
 * Helper: get an active customer email from admin storage state.
 * Uses the saved admin auth to query the customer edit page.
 */
async function getActiveCustomerEmail(page: import('@playwright/test').Page): Promise<string> {
  // Load admin auth state to create an authenticated request context
  const authFile = path.join(__dirname, '..', '.auth', 'admin.json');
  const storageState = JSON.parse(fs.readFileSync(authFile, 'utf-8'));

  // Use a new context with admin auth to fetch customer email
  const browser = page.context().browser()!;
  const adminContext = await browser.newContext({ storageState });
  const adminPage = await adminContext.newPage();

  await adminPage.goto(`/${adminRoute}/customer/1/edit`);
  await adminPage.waitForLoadState('load');
  const email = await adminPage.locator('#admin_customer_email').inputValue();

  await adminContext.close();
  return email;
}

test.describe('Front Other Pages (EF06)', () => {

  test('EF0601-UC01-T01 ログイン・ログアウト', async ({ page }) => {
    // Get an active customer email
    const email = await getActiveCustomerEmail(page);

    // Login
    await page.goto('/mypage/login');
    await page.waitForLoadState('load');

    await page.locator('input[name="login_email"]').fill(email);
    await page.locator('input[name="login_pass"]').fill('password');
    await page.locator('#login_mypage button[type="submit"]').click();
    await page.waitForLoadState('load');

    // After login, user is redirected (to top or mypage)
    // Navigate to mypage to verify logged-in state
    await page.goto('/mypage');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-pageHeader h1')).toContainText('マイページ');

    // Logout
    await page.locator('a[href*="/logout"]').first().click();
    await page.waitForLoadState('load');

    // Verify logged out - navigating to mypage should redirect to login
    await page.goto('/mypage');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-pageHeader h1')).toContainText('ログイン');
  });

  test('EF0601-UC01-T02 ログイン異常 仮会員', async ({ page }) => {
    // 仮会員を作成するためにadminから新規会員を仮会員ステータスで登録
    const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
    const browser = page.context().browser()!;
    const authFile = path.join(__dirname, '..', '.auth', 'admin.json');
    const storageState = JSON.parse(fs.readFileSync(authFile, 'utf-8'));
    const adminContext = await browser.newContext({ storageState });
    const adminPage = await adminContext.newPage();

    const tempEmail = `temp_${Date.now()}@test.test`;

    // 管理画面で仮会員を登録
    await adminPage.goto(`/${adminRoute}/customer/new`);
    await adminPage.waitForLoadState('load');

    await adminPage.locator('#admin_customer_name_name01').fill('仮会員');
    await adminPage.locator('#admin_customer_name_name02').fill('テスト');
    await adminPage.locator('#admin_customer_kana_kana01').fill('カリカイイン');
    await adminPage.locator('#admin_customer_kana_kana02').fill('テスト');
    await adminPage.locator('#admin_customer_postal_code').fill('530-0001');
    await adminPage.locator('#admin_customer_address_pref').selectOption({ value: '27' });
    await adminPage.locator('#admin_customer_address_addr01').fill('大阪市北区');
    await adminPage.locator('#admin_customer_address_addr02').fill('梅田');
    await adminPage.locator('#admin_customer_phone_number').fill('111111111');
    await adminPage.locator('#admin_customer_email').fill(tempEmail);
    await adminPage.locator('#admin_customer_plain_password_first').fill('password');
    await adminPage.locator('#admin_customer_plain_password_second').fill('password');
    // ステータスを仮会員(1)に設定
    await adminPage.locator('#admin_customer_status').selectOption({ value: '1' });
    await adminPage.locator('button.btn-ec-conversion').click();
    await adminPage.waitForLoadState('load');

    await adminContext.close();

    // 仮会員のメールでログインを試みる
    await page.goto('/mypage/login');
    await page.waitForLoadState('load');
    await page.locator('input[name="login_email"]').fill(tempEmail);
    await page.locator('input[name="login_pass"]').fill('password');
    await page.locator('#login_mypage button[type="submit"]').click();
    await page.waitForLoadState('load');

    // ログインエラーメッセージが表示される
    await expect(page.locator('div.ec-login p.ec-errorMessage')).toContainText('ログインできませんでした');
  });

  test('EF0601-UC01-T03 ログイン異常 入力ミス', async ({ page }) => {
    await page.goto('/mypage/login');
    await page.waitForLoadState('load');

    // 存在しないメールアドレスでログイン
    await page.locator('input[name="login_email"]').fill('nonexistent_wrong@test.test.bad');
    await page.locator('input[name="login_pass"]').fill('password');
    await page.locator('#login_mypage button[type="submit"]').click();
    await page.waitForLoadState('load');

    // ログインエラーメッセージが表示される
    await expect(page.locator('div.ec-login p.ec-errorMessage')).toContainText('ログインできませんでした');
  });

  test('EF0602-UC01-T01 パスワード再発行', async ({ page }) => {
    // ログインページへ
    await page.goto('/mypage/login');
    await page.waitForLoadState('load');

    // 「ログイン情報をお忘れですか？」リンクをクリック
    await page.locator('#login_mypage a').first().click();
    await page.waitForLoadState('load');

    // パスワード再発行ページ
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('パスワードの再発行');

    // テストユーザーのメールアドレスを入力して送信
    await page.locator('#login_email').fill('playwright@test.test');
    await page.locator('button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');

    // パスワード再発行(メール送信)ページに遷移
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('パスワードの再発行');
    // メール送信完了メッセージが表示される(完了ページは存在する)
    await expect(page.locator('.ec-layoutRole__main')).toBeVisible();
  });

  test('EF0608-UC01-T01 サイトマップ', async ({ page }) => {
    // sitemap.xml にアクセス
    const response = await page.goto('/sitemap.xml');
    expect(response).not.toBeNull();
    expect(response!.status()).toBe(200);

    // コンテンツを取得して確認
    const content = await page.content();
    expect(content).toContain('/sitemap_page.xml');
    expect(content).toContain('/sitemap_category.xml');
    expect(content).toContain('/sitemap_product_1.xml');
  });

  test('EF0608-UC01-T03 サイトマップ ページ', async ({ page }) => {
    // sitemap_page.xml にアクセス
    const response = await page.goto('/sitemap_page.xml');
    expect(response).not.toBeNull();
    expect(response!.status()).toBe(200);

    // TOPページのURLが含まれていることを確認
    const content = await page.content();
    // baseURLのlocを含むか確認 (http://127.0.0.1:8000/ のような形)
    expect(content).toContain('/</loc>');
  });

  test('EF0608-UC01-T03 サイトマップ カテゴリ', async ({ page }) => {
    // sitemap_category.xml にアクセス
    const response = await page.goto('/sitemap_category.xml');
    expect(response).not.toBeNull();
    expect(response!.status()).toBe(200);

    // カテゴリURLが含まれていることを確認
    const content = await page.content();
    expect(content).toContain('/products/list?category_id=');
  });

  test('EF0608-UC01-T04 サイトマップ 商品', async ({ page }) => {
    // sitemap_product_1.xml にアクセス
    const response = await page.goto('/sitemap_product_1.xml');
    expect(response).not.toBeNull();
    expect(response!.status()).toBe(200);

    // 商品URLが含まれていることを確認
    const content = await page.content();
    expect(content).toContain('/products/detail/');
  });

  test('EF0604-UC01-T01 当サイトについて', async ({ page }) => {
    await page.goto('/help/about');
    await page.waitForLoadState('load');

    // ページタイトルが表示される
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('当サイトについて');

    // ショップ名要素が表示される
    await expect(page.locator('#help_about_box__shop_name')).toBeVisible();
  });

  test('EF0605-UC01-T01 プライバシーポリシー', async ({ page }) => {
    await page.goto('/help/privacy');
    await page.waitForLoadState('load');

    // ページタイトルが表示される
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('プライバシーポリシー');

    // プライバシーポリシーの本文が表示される
    await expect(page.locator('.ec-layoutRole__main')).toContainText(
      '個人情報保護の重要性に鑑み'
    );
  });

  test('EF0606-UC01-T01 特定商取引法に基づく表記', async ({ page }) => {
    await page.goto('/help/tradelaw');
    await page.waitForLoadState('load');

    // ページタイトルが表示される
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('特定商取引法に基づく表記');
  });

  test('EF0607-UC01-T01 お問い合わせ 入力から確認画面', async ({ page }) => {
    const email = `contact_${Date.now()}@example.com`;

    await page.goto('/contact');
    await page.waitForLoadState('load');

    // ページタイトルが表示される
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お問い合わせ');

    // フォーム入力
    await page.locator('#contact_name_name01').fill('姓');
    await page.locator('#contact_name_name02').fill('名');
    await page.locator('#contact_kana_kana01').fill('セイ');
    await page.locator('#contact_kana_kana02').fill('メイ');
    await page.locator('#contact_postal_code').fill('530-0001');
    await page.locator('#contact_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(1000); // 郵便番号の自動入力を待つ
    await page.locator('#contact_address_addr01').fill('大阪市北区');
    await page.locator('#contact_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
    await page.locator('#contact_phone_number').fill('111-111-111');
    await page.locator('#contact_email').fill(email);
    await page.locator('#contact_contents').fill('お問い合わせ内容の送信');

    // 確認ページへボタンを押下
    await page.locator('div.ec-RegisterRole__actions button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');

    // 確認画面が表示される
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お問い合わせ');
    await expect(page.locator('.ec-contactConfirmRole')).toBeVisible();

    // 入力内容が確認画面に表示される
    await expect(page.locator('.ec-contactConfirmRole')).toContainText('姓');
    await expect(page.locator('.ec-contactConfirmRole')).toContainText('名');
    await expect(page.locator('.ec-contactConfirmRole')).toContainText(email);
    await expect(page.locator('.ec-contactConfirmRole')).toContainText('お問い合わせ内容の送信');

    // 送信ボタンを押下
    await page.locator('div.ec-contactConfirmRole div.ec-RegisterRole__actions button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');

    // 完了ページが表示される
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お問い合わせ(完了)');
  });

  test('EF0607-UC01-T02 お問い合わせ 戻るボタン', async ({ page }) => {
    const email = `contact_${Date.now()}@example.com`;

    await page.goto('/contact');
    await page.waitForLoadState('load');

    // フォーム入力
    await page.locator('#contact_name_name01').fill('姓');
    await page.locator('#contact_name_name02').fill('名');
    await page.locator('#contact_kana_kana01').fill('セイ');
    await page.locator('#contact_kana_kana02').fill('メイ');
    await page.locator('#contact_postal_code').fill('530-0001');
    await page.locator('#contact_address_pref').selectOption({ value: '27' });
    await page.waitForTimeout(1000);
    await page.locator('#contact_address_addr01').fill('大阪市北区');
    await page.locator('#contact_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
    await page.locator('#contact_phone_number').fill('111-111-111');
    await page.locator('#contact_email').fill(email);
    await page.locator('#contact_contents').fill('お問い合わせ内容の送信');

    // 確認ページへ
    await page.locator('div.ec-RegisterRole__actions button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');

    // 確認画面が表示される
    await expect(page.locator('.ec-contactConfirmRole')).toBeVisible();

    // 戻るボタンを押下
    await page.locator('div.ec-contactConfirmRole div.ec-RegisterRole__actions button.ec-blockBtn--cancel').click();
    await page.waitForLoadState('load');

    // 入力画面に戻る
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お問い合わせ');

    // フォーム入力内容が保持されている
    await expect(page.locator('#contact_name_name01')).toHaveValue('姓');
    await expect(page.locator('#contact_name_name02')).toHaveValue('名');
    await expect(page.locator('#contact_contents')).toHaveValue('お問い合わせ内容の送信');
  });

  test('EF0607-UC01-T03 お問い合わせ 異常パターン', async ({ page }) => {
    await page.goto('/contact');
    await page.waitForLoadState('load');

    // 何も入力せずに確認ボタンを押下
    await page.locator('div.ec-RegisterRole__actions button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');

    // エラーメッセージが表示される
    await expect(page.locator('.ec-contactRole')).toContainText('入力されていません');
  });

  test('EF0609-UC01-T01 新着商品ブロック表示', async ({ page }) => {
    await page.goto('/block/auto_new_item');
    await page.waitForLoadState('load');

    // タイトル表示
    await expect(page.locator('.ec-secHeading__en')).toContainText('NEW ITEM');
    await expect(page.locator('.ec-secHeading__ja')).toContainText('新着商品');

    // 商品が表示される
    await expect(page.locator('.ec-newItemRole__listItem').first()).toBeVisible();

    // 商品名が表示される
    await expect(page.locator('.ec-newItemRole__listItemTitle').first()).toBeVisible();

    // 商品金額が表示される
    await expect(page.locator('.ec-newItemRole__listItemPrice').first()).toBeVisible();
  });
});
