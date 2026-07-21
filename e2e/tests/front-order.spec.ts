import { test, expect } from '@playwright/test';
import { CUSTOMER_PASSWORD } from '../config/default.config';

/**
 * Helper: Add a product to cart and go to the cart page.
 * Uses Product ID 2 (チェリーアイスサンド) which has no class categories.
 */
async function addProductToCartAndGoToCart(page: import('@playwright/test').Page, quantity = 1) {
  await page.goto('/products/detail/2');
  await page.waitForLoadState('load');
  await page.locator('#quantity').fill(String(quantity));
  await page.locator('.add-cart').click();
  await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
  await expect(page.locator('#ec-modal-header')).toContainText('カートに追加しました');
  await page.locator('div.ec-modal-box > div > a').click();
  await page.waitForLoadState('load');
  await expect(page.locator('div.ec-pageHeader h1')).toContainText('ショッピングカート');
}

/**
 * Helper: Login as the test customer via /mypage/login.
 */
async function loginAsTestCustomer(page: import('@playwright/test').Page) {
  await page.goto('/mypage/login');
  await page.waitForLoadState('load');
  await page.locator('input[name="login_email"]').fill('playwright@test.test');
  await page.locator('input[name="login_pass"]').fill(CUSTOMER_PASSWORD);
  await page.getByRole('button', { name: 'ログイン' }).click();
  await page.waitForLoadState('load');
}

/**
 * Helper: Clean up cart by deleting all items.
 */
async function clearCart(page: import('@playwright/test').Page) {
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
 * Helper: Add a customer address from MyPage with a distinctive name02 marker.
 * Returns to caller on the address list page.
 */
async function addCustomerAddressFromMypage(page: import('@playwright/test').Page, name02: string) {
  await page.goto('/mypage/delivery');
  await page.waitForLoadState('load');
  await page.locator('div.ec-addressRole div.ec-addressRole__actions a').click();
  await page.waitForLoadState('load');
  await page.locator('#customer_address_name_name01').fill('配送6259');
  await page.locator('#customer_address_name_name02').fill(name02);
  await page.locator('#customer_address_kana_kana01').fill('ハイソウ');
  await page.locator('#customer_address_kana_kana02').fill('ハナコ');
  await page.locator('#customer_address_postal_code').fill('530-0001');
  await page.locator('#customer_address_address_pref').selectOption({ value: '27' });
  await page.waitForTimeout(1000);
  await page.locator('#customer_address_address_addr01').fill('大阪市北区');
  await page.locator('#customer_address_address_addr02').fill('梅田1-1-1');
  await page.locator('#customer_address_phone_number').fill('111-111-111');
  await page.locator('div.ec-RegisterRole__actions button').click();
  await page.waitForLoadState('load');
  await expect(page.locator('div.ec-pageHeader h1')).toContainText('お届け先一覧');
}

test.describe('Front Order (EF03)', () => {

  test.afterEach(async ({ page }) => {
    // Clean up cart after each test to avoid interference
    await clearCart(page);
  });

  test('EF0301-UC01-T01 カート 買い物を続ける', async ({ page }) => {
    // ログインしてカートに商品追加
    await loginAsTestCustomer(page);
    await addProductToCartAndGoToCart(page, 1);

    // 「お買い物を続ける」ボタンをクリック
    await page.locator('a.ec-blockBtn--cancel', { hasText: 'お買い物を続ける' }).click();
    await page.waitForLoadState('load');

    // トップページに遷移する
    await expect(page).toHaveURL(/\/$/);
    await expect(page.locator('.ec-newsRole')).toBeVisible();
  });

  test('EF0302-UC01-T01 ログインユーザ購入', async ({ page }) => {
    // ログアウト状態から開始
    await page.goto('/logout');
    await page.waitForLoadState('load');

    // カートに商品追加
    await addProductToCartAndGoToCart(page, 1);

    // レジに進む
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    // ショッピングログインページでログイン
    await expect(page).toHaveURL(/\/shopping\/login/);
    await page.locator('input[name="login_email"]').fill('playwright@test.test');
    await page.locator('input[name="login_pass"]').fill(CUSTOMER_PASSWORD);
    await page.getByRole('button', { name: 'ログイン' }).click();
    await page.waitForLoadState('load');

    // ご注文手続きページ
    await expect(page).toHaveURL(/\/shopping$/);

    // 確認する
    await page.locator('.ec-blockBtn--action', { hasText: '確認する' }).click();
    await page.waitForLoadState('load');

    // ご注文確認ページ
    await expect(page).toHaveURL(/\/shopping\/confirm/);
    await expect(page.locator('.ec-pageHeader h1')).toContainText('ご注文内容のご確認');

    // 注文する
    await page.locator('.ec-blockBtn--action', { hasText: '注文する' }).click();
    await page.waitForLoadState('load');

    // 注文完了ページ
    await expect(page).toHaveURL(/\/shopping\/complete/);
    await expect(page.locator('.ec-pageHeader h1')).toContainText('ご注文完了');

    // トップページへ戻る
    await page.locator('a.ec-blockBtn--cancel', { hasText: 'トップページへ' }).click();
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-newsRole')).toBeVisible();
  });

  test('EF0302-UC02-T01 ゲスト購入', async ({ page }) => {
    // ログアウト状態から開始
    await page.goto('/logout');
    await page.waitForLoadState('load');

    // カートに商品追加
    await addProductToCartAndGoToCart(page, 1);

    // レジに進む
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    // ゲスト購入リンクをクリック
    await expect(page).toHaveURL(/\/shopping\/login/);
    await page.getByRole('link', { name: 'ゲスト購入' }).click();
    await page.waitForLoadState('load');

    // お客様情報入力ページ
    await expect(page).toHaveURL(/\/shopping\/nonmember/);
    await expect(page.locator('.ec-pageHeader h1')).toContainText('お客様情報の入力');

    const guestEmail = `guest_${Date.now()}@test.test`;

    // お客様情報を入力
    await page.locator('#nonmember_name_name01').fill('テスト');
    await page.locator('#nonmember_name_name02').fill('太郎');
    await page.locator('#nonmember_kana_kana01').fill('テスト');
    await page.locator('#nonmember_kana_kana02').fill('タロウ');
    await page.locator('#nonmember_postal_code').fill('530-0001');
    await page.waitForTimeout(2000); // 郵便番号の自動入力を待つ
    await page.locator('#nonmember_address_pref').selectOption({ value: '27' });
    await page.locator('#nonmember_address_addr01').fill('大阪市北区');
    await page.locator('#nonmember_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
    await page.locator('#nonmember_phone_number').fill('111-111-111');
    await page.locator('#nonmember_email_first').fill(guestEmail);
    await page.locator('#nonmember_email_second').fill(guestEmail);

    // 次へ
    await page.locator('button.ec-blockBtn--action', { hasText: '次へ' }).click();
    await page.waitForLoadState('load');

    // ご注文手続きページ
    await expect(page).toHaveURL(/\/shopping$/);

    // 確認する
    await page.locator('.ec-blockBtn--action', { hasText: '確認する' }).click();
    await page.waitForLoadState('load');

    // ご注文確認ページ
    await expect(page).toHaveURL(/\/shopping\/confirm/);
    await expect(page.locator('.ec-pageHeader h1')).toContainText('ご注文内容のご確認');

    // 注文する
    await page.locator('.ec-blockBtn--action', { hasText: '注文する' }).click();
    await page.waitForLoadState('load');

    // 注文完了ページ
    await expect(page).toHaveURL(/\/shopping\/complete/);
    await expect(page.locator('.ec-pageHeader h1')).toContainText('ご注文完了');

    // トップページへ戻る
    await page.locator('a.ec-blockBtn--cancel', { hasText: 'トップページへ' }).click();
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-newsRole')).toBeVisible();
  });

  test('EF0301-UC01-T02 一覧からカートに入れる', async ({ page }) => {
    // 商品一覧ページへ直接アクセス
    await page.goto('/products/list?name=%E5%BD%A9%E3%81%AE%E3%82%B8%E3%82%A7%E3%83%A9%E3%83%BC%E3%83%88CUBE');
    await page.waitForLoadState('load');

    // 商品一覧ページにいることを確認
    await expect(page).toHaveURL(/\/products\/list/);

    // 規格を選択してカートに入れる
    const firstItem = page.locator('ul.ec-shelfGrid li.ec-shelfGrid__item').first();
    await firstItem.locator('select[name="classcategory_id1"]').selectOption({ label: 'チョコ' });
    // classcategory_id2 が動的に更新されるのを待つ
    await page.waitForTimeout(1000);
    await firstItem.locator('select[name="classcategory_id2"]').selectOption({ label: '16mm × 16mm' });
    await firstItem.locator('input[name="quantity"]').fill('1');
    await firstItem.locator('.add-cart').click();

    // モーダル表示を確認
    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await expect(page.locator('#ec-modal-header')).toContainText('カートに追加しました');

    // カートへ進む
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    // カートページ
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('ショッピングカート');

    // 商品名・規格の確認
    const cartItemName = page.locator('.ec-cartRow__name').first();
    await expect(cartItemName).toContainText('彩のジェラートCUBE');
    await expect(cartItemName).toContainText('チョコ');
    await expect(cartItemName).toContainText('16mm × 16mm');

    // 数量が1であること
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('1');
  });

  test('EF0301-UC01-T02 カート削除', async ({ page }) => {
    // ログインしてカートに商品追加
    await loginAsTestCustomer(page);
    await addProductToCartAndGoToCart(page, 1);

    // カートに商品があることを確認
    await expect(page.locator('ul.ec-cartRow').first()).toBeVisible();

    // 削除ダイアログを受け入れ
    page.on('dialog', dialog => dialog.accept());

    // 削除ボタンをクリック
    await page.locator('.ec-cartRow__delColumn a').first().click();
    await page.waitForLoadState('load');

    // カートが空になったことを確認 (商品行が無い)
    await expect(page.locator('ul.ec-cartRow')).toHaveCount(0);
  });

  test('EF0301-UC01-T03 カート数量増やす', async ({ page }) => {
    // ログインしてカートに商品追加(数量1)
    await loginAsTestCustomer(page);
    await addProductToCartAndGoToCart(page, 1);

    // 数量が1であること
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('1');

    // 数量を増やすボタンをクリック
    await page.locator('a.ec-cartRow__amountUpButton').first().click();
    await page.waitForLoadState('load');

    // 数量が2になったこと
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('2');
  });

  test('EF0301-UC01-T04 カート数量減らす', async ({ page }) => {
    // ログインしてカートに商品追加(数量2)
    await loginAsTestCustomer(page);
    await addProductToCartAndGoToCart(page, 2);

    // 数量が2であること
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('2');

    // 数量を減らすボタンをクリック
    await page.locator('a.ec-cartRow__amountDownButton').first().click();
    await page.waitForLoadState('load');

    // 数量が1になったこと
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('1');
  });

  test('EF0305-UC02-T01 ゲスト購入情報変更', async ({ page }) => {
    // ログアウト状態から開始
    await page.goto('/logout');
    await page.waitForLoadState('load');

    // カートに商品追加
    await addProductToCartAndGoToCart(page, 1);

    // レジに進む
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    // ゲスト購入
    await page.getByRole('link', { name: 'ゲスト購入' }).click();
    await page.waitForLoadState('load');

    // お客様情報入力
    const guestEmail = `guest_${Date.now()}@test.test`;
    await page.locator('#nonmember_name_name01').fill('姓03');
    await page.locator('#nonmember_name_name02').fill('名03');
    await page.locator('#nonmember_kana_kana01').fill('セイ');
    await page.locator('#nonmember_kana_kana02').fill('メイ');
    await page.locator('#nonmember_postal_code').fill('530-0001');
    await page.waitForTimeout(2000);
    await page.locator('#nonmember_address_pref').selectOption({ value: '27' });
    await page.locator('#nonmember_address_addr01').fill('大阪市北区');
    await page.locator('#nonmember_address_addr02').fill('梅田2-4-9 ブリーゼタワー13F');
    await page.locator('#nonmember_phone_number').fill('111-111-111');
    await page.locator('#nonmember_email_first').fill(guestEmail);
    await page.locator('#nonmember_email_second').fill(guestEmail);
    await page.locator('button.ec-blockBtn--action', { hasText: '次へ' }).click();
    await page.waitForLoadState('load');

    // ご注文手続きページ
    await expect(page).toHaveURL(/\/shopping$/);

    // お客様情報変更
    await page.locator('#shopping-form #customer').click();
    await page.waitForSelector('#edit0', { state: 'visible' });
    await page.locator('#edit0').fill('姓0301');
    await page.locator('#customer-ok button').click();
    await page.waitForTimeout(3000);

    // 変更が反映されている
    await expect(page.locator('#shopping-form .customer-edit.customer-name01')).toContainText('姓0301');

    // お届け先変更
    await page.locator('#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div.ec-orderDelivery__title > div > button').click();
    await page.waitForLoadState('load');

    // お届け先変更画面
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お届け先の変更');
    await page.locator('#shopping_shipping_name_name01').fill('姓0302');
    await page.locator('div.ec-RegisterRole__actions button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');

    // 変更が反映されている
    await expect(page.locator('div.ec-orderRole div.ec-orderDelivery__address')).toContainText('姓0302');

    // 確認して注文
    await page.locator('.ec-blockBtn--action', { hasText: '確認する' }).click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/shopping\/confirm/);

    await page.locator('.ec-blockBtn--action', { hasText: '注文する' }).click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/shopping\/complete/);
    await expect(page.locator('.ec-pageHeader h1')).toContainText('ご注文完了');
  });

  // お届け先選択画面での住所の編集・削除 (issue #6259)
  test('EF0305-UC10-T01 会員 お届け先の編集・削除', async ({ page }) => {
    await loginAsTestCustomer(page);

    // 一意な名前のお届け先を追加(テスト末尾で削除して原状復帰する).
    await addCustomerAddressFromMypage(page, '花子6259');

    // カート→レジ→ご注文手続き.
    await addProductToCartAndGoToCart(page, 1);
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/shopping$/);

    // お届け先選択画面へ.
    await page.locator('div.ec-orderDelivery__change button').first().click();
    await page.waitForLoadState('load');
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お届け先の指定');

    // 追加した住所の項目に「変更」「削除」ボタンがあること.
    const item = page.locator('.ec-addressList__item', { hasText: '花子6259' });
    await expect(item.getByRole('link', { name: '変更' })).toBeVisible();
    await expect(item.getByRole('link', { name: '削除' })).toBeVisible();

    // 編集: name02 を変更して登録 → 選択画面に戻り反映されること.
    await item.getByRole('link', { name: '変更' }).click();
    await page.waitForLoadState('load');
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お届け先の変更');
    await page.locator('#shopping_shipping_name_name02').fill('桜子6259');
    await page.locator('div.ec-RegisterRole__actions button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お届け先の指定');
    await expect(page.locator('.ec-addressList')).toContainText('桜子6259');

    // 削除: 確認ダイアログを承認 → 一覧から消えること.
    page.on('dialog', dialog => dialog.accept());
    const editedItem = page.locator('.ec-addressList__item', { hasText: '桜子6259' });
    await editedItem.getByRole('link', { name: '削除' }).click();
    await page.waitForLoadState('load');
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お届け先の指定');
    await expect(page.locator('.ec-addressList')).not.toContainText('桜子6259');
  });

  test('EF0305-UC07-T01 ログインしてカートをマージ', async ({ page }) => {
    // ログアウト状態から開始
    await page.goto('/logout');
    await page.waitForLoadState('load');

    // 未ログイン状態で商品(チェリーアイスサンド)をカートに入れる
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');
    await page.locator('#quantity').fill('1');
    await page.locator('.add-cart').click();
    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });

    // レジに進む -> ログイン
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    // ログイン
    await page.locator('input[name="login_email"]').fill('playwright@test.test');
    await page.locator('input[name="login_pass"]').fill(CUSTOMER_PASSWORD);
    await page.getByRole('button', { name: 'ログイン' }).click();
    await page.waitForLoadState('load');

    // ご注文手続きページ -> 確認
    await page.locator('.ec-blockBtn--action', { hasText: '確認する' }).click();
    await page.waitForLoadState('load');

    // ログアウト
    await page.goto('/logout');
    await page.waitForLoadState('load');

    // 別の商品(彩のジェラートCUBE)をカートに入れる
    await page.goto('/products/detail/1');
    await page.waitForLoadState('load');
    await page.locator('select[name="classcategory_id1"]').selectOption({ label: 'チョコ' });
    await page.waitForTimeout(1000);
    await page.locator('select[name="classcategory_id2"]').selectOption({ label: '16mm × 16mm' });
    await page.locator('#quantity').fill('1');
    await page.locator('.add-cart').click();
    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });

    // カートへ -> レジに進む
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    // ログイン
    await page.locator('input[name="login_email"]').fill('playwright@test.test');
    await page.locator('input[name="login_pass"]').fill(CUSTOMER_PASSWORD);
    await page.getByRole('button', { name: 'ログイン' }).click();
    await page.waitForLoadState('load');

    // 注文完了まで進む
    await page.locator('.ec-blockBtn--action', { hasText: '確認する' }).click();
    await page.waitForLoadState('load');
    await page.locator('.ec-blockBtn--action', { hasText: '注文する' }).click();
    await page.waitForLoadState('load');

    await expect(page).toHaveURL(/\/shopping\/complete/);
    await expect(page.locator('.ec-pageHeader h1')).toContainText('ご注文完了');
  });

  test('EF0305-UC08-T02 購入確認画面からカートに戻るWithお届け先初期化', async ({ page }) => {
    // ログインしてカートに商品追加
    await loginAsTestCustomer(page);
    await addProductToCartAndGoToCart(page, 1);

    // レジに進む
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    // ご注文手続きページ
    await expect(page).toHaveURL(/\/shopping$/);

    // 確認する
    await page.locator('.ec-blockBtn--action', { hasText: '確認する' }).click();
    await page.waitForLoadState('load');

    // ご注文確認ページ
    await expect(page).toHaveURL(/\/shopping\/confirm/);

    // カートに戻ってさらに商品を追加(カートの内容が変わる)
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');
    await page.locator('#quantity').fill('1');
    await page.locator('.add-cart').click();
    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    // カートで数量が増えていることを確認
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('2');

    // レジに進む
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    // ご注文手続きページに戻る(お届け先は初期化されるが、単一配送先の場合は見た目変わらない)
    await expect(page).toHaveURL(/\/shopping$/);

    // お届け先が表示されている(初期化後もデフォルトお届け先が設定される)
    await expect(page.locator('#shopping-form div.ec-orderDelivery__title')).toContainText('お届け先');

    // 注文完了まで
    await page.locator('.ec-blockBtn--action', { hasText: '確認する' }).click();
    await page.waitForLoadState('load');
    await page.locator('.ec-blockBtn--action', { hasText: '注文する' }).click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/shopping\/complete/);
  });

  test('EF0305-UC09-T01 複数ブラウザでログインしてカートに追加する', async ({ browser }) => {
    // ブラウザ1を作成しログイン
    const context1 = await browser.newContext();
    const page1 = await context1.newPage();
    await page1.goto('/mypage/login');
    await page1.waitForLoadState('load');
    await page1.locator('input[name="login_email"]').fill('playwright@test.test');
    await page1.locator('input[name="login_pass"]').fill(CUSTOMER_PASSWORD);
    await page1.getByRole('button', { name: 'ログイン' }).click();
    await page1.waitForLoadState('load');

    // ブラウザ2を作成しログイン
    const context2 = await browser.newContext();
    const page2 = await context2.newPage();
    await page2.goto('/mypage/login');
    await page2.waitForLoadState('load');
    await page2.locator('input[name="login_email"]').fill('playwright@test.test');
    await page2.locator('input[name="login_pass"]').fill(CUSTOMER_PASSWORD);
    await page2.getByRole('button', { name: 'ログイン' }).click();
    await page2.waitForLoadState('load');

    // ブラウザ1でカートに商品を入れる
    await page1.goto('/products/detail/2');
    await page1.waitForLoadState('load');
    await page1.locator('#quantity').fill('1');
    await page1.locator('.add-cart').click();
    await expect(page1.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await page1.locator('div.ec-modal-box > div > a').click();
    await page1.waitForLoadState('load');

    // ブラウザ1で商品がカートにあることを確認
    await expect(page1.locator('.ec-cartRow__name').first()).toContainText('チェリーアイスサンド');

    // ブラウザ2のカートにも反映されている
    await page2.goto('/cart');
    await page2.waitForLoadState('load');
    await expect(page2.locator('.ec-cartRow__name').first()).toContainText('チェリーアイスサンド');

    // クリーンアップ
    page1.on('dialog', dialog => dialog.accept());
    const deleteLinks1 = page1.locator('.ec-cartRow__delColumn a');
    while (await deleteLinks1.count() > 0) {
      await deleteLinks1.first().click();
      await page1.waitForLoadState('load');
    }

    await context1.close();
    await context2.close();
  });

  test('EF0305-UC09-T02 複数ブラウザ 片方でログインしてカートに追加しもう一方にログインして別の商品を追加する', async ({ browser }) => {
    // ブラウザ1を作成しログイン
    const context1 = await browser.newContext();
    const page1 = await context1.newPage();
    await page1.goto('/mypage/login');
    await page1.waitForLoadState('load');
    await page1.locator('input[name="login_email"]').fill('playwright@test.test');
    await page1.locator('input[name="login_pass"]').fill(CUSTOMER_PASSWORD);
    await page1.getByRole('button', { name: 'ログイン' }).click();
    await page1.waitForLoadState('load');

    // ブラウザ1でカートに商品(チェリーアイスサンド)を入れる
    await page1.goto('/products/detail/2');
    await page1.waitForLoadState('load');
    await page1.locator('#quantity').fill('1');
    await page1.locator('.add-cart').click();
    await expect(page1.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await page1.locator('div.ec-modal-box > div > a').click();
    await page1.waitForLoadState('load');
    await expect(page1.locator('.ec-cartRow__name').first()).toContainText('チェリーアイスサンド');

    // ブラウザ2(未ログイン)で別の商品(彩のジェラートCUBE)をカートに入れる
    const context2 = await browser.newContext();
    const page2 = await context2.newPage();
    await page2.goto('/products/detail/1');
    await page2.waitForLoadState('load');
    await page2.locator('select[name="classcategory_id1"]').selectOption({ label: 'チョコ' });
    await page2.waitForTimeout(1000);
    await page2.locator('select[name="classcategory_id2"]').selectOption({ label: '16mm × 16mm' });
    await page2.locator('#quantity').fill('1');
    await page2.locator('.add-cart').click();
    await expect(page2.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await page2.locator('div.ec-modal-box > div > a').click();
    await page2.waitForLoadState('load');
    await expect(page2.locator('.ec-cartRow__name').first()).toContainText('彩のジェラートCUBE');

    // ブラウザ2でログインするとカートがマージされる
    await page2.goto('/mypage/login');
    await page2.waitForLoadState('load');
    await page2.locator('input[name="login_email"]').fill('playwright@test.test');
    await page2.locator('input[name="login_pass"]').fill(CUSTOMER_PASSWORD);
    await page2.getByRole('button', { name: 'ログイン' }).click();
    await page2.waitForLoadState('load');

    await page2.goto('/cart');
    await page2.waitForLoadState('load');

    // マージされて2件あることを確認
    await expect(page2.locator('ul.ec-cartRow')).toHaveCount(2);
    const itemNames2 = await page2.locator('.ec-cartRow__name a').allTextContents();
    expect(itemNames2.some(n => n.includes('彩のジェラートCUBE'))).toBeTruthy();
    expect(itemNames2.some(n => n.includes('チェリーアイスサンド'))).toBeTruthy();

    // ブラウザ1のカートもマージされている
    await page1.goto('/cart');
    await page1.waitForLoadState('load');
    await expect(page1.locator('ul.ec-cartRow')).toHaveCount(2);
    const itemNames1 = await page1.locator('.ec-cartRow__name a').allTextContents();
    expect(itemNames1.some(n => n.includes('彩のジェラートCUBE'))).toBeTruthy();
    expect(itemNames1.some(n => n.includes('チェリーアイスサンド'))).toBeTruthy();

    // クリーンアップ
    page1.on('dialog', dialog => dialog.accept());
    const deleteLinks = page1.locator('.ec-cartRow__delColumn a');
    while (await deleteLinks.count() > 0) {
      await deleteLinks.first().click();
      await page1.waitForLoadState('load');
    }

    await context1.close();
    await context2.close();
  });

  test('EF0305-UC06-T01 ログインユーザ購入複数配送', async ({ page }) => {
    test.setTimeout(180_000);

    // Login
    await loginAsTestCustomer(page);

    // Add product 2 to cart with quantity 2
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');
    await page.locator('#quantity').fill('2');
    await page.locator('.add-cart').click();
    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    // Go to checkout
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/shopping$/);

    // Click multi-shipping button
    await page.locator('button[data-path*="shipping_multiple"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お届け先の複数指定');

    // Add a new delivery address
    await page.locator('.ec-AddAddress__new a').click();
    await page.waitForLoadState('load');

    // Fill in address form
    await page.locator('#shopping_shipping_name_name01').fill('テスト');
    await page.locator('#shopping_shipping_name_name02').fill('太郎');
    await page.locator('#shopping_shipping_kana_kana01').fill('テスト');
    await page.locator('#shopping_shipping_kana_kana02').fill('タロウ');
    await page.locator('#shopping_shipping_postal_code').fill('1500001');
    await page.waitForTimeout(2000); // Wait for yubinbango auto-fill
    const prefSelect = page.locator('#shopping_shipping_address_pref');
    if (await prefSelect.inputValue() === '') {
      await prefSelect.selectOption('13'); // Tokyo
    }
    const addr01 = page.locator('#shopping_shipping_address_addr01');
    if (await addr01.inputValue() === '') {
      await addr01.fill('渋谷区神宮前');
    }
    await page.locator('#shopping_shipping_address_addr02').fill('1-1-1');
    await page.locator('#shopping_shipping_phone_number').fill('09012345678');
    await page.locator('button[type="submit"].ec-blockBtn--action').click();
    await page.waitForLoadState('load');

    // Back on multi-shipping page
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お届け先の複数指定');

    // Add another shipping row for the product
    await page.locator('#button__add0').click();
    await page.waitForTimeout(500);

    // Set quantities: first row = 1, second row = 1
    await page.locator('#form_shipping_multiple_0_shipping_0_quantity').fill('1');
    await page.locator('#form_shipping_multiple_0_shipping_1_quantity').fill('1');

    // Select different address for second row
    const addressOptions = await page.locator('#form_shipping_multiple_0_shipping_1_customer_address option').all();
    if (addressOptions.length > 1) {
      const lastValue = await addressOptions[addressOptions.length - 1].getAttribute('value');
      if (lastValue) {
        await page.locator('#form_shipping_multiple_0_shipping_1_customer_address').selectOption(lastValue);
      }
    }

    // Submit multi-shipping
    await page.locator('#button__confirm').click();
    await page.waitForLoadState('load');

    // Should be back on shopping page
    await expect(page).toHaveURL(/\/shopping$/);

    // Confirm order
    await page.locator('.ec-blockBtn--action', { hasText: '確認する' }).click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/shopping\/confirm/);

    // Place order
    await page.locator('.ec-blockBtn--action', { hasText: '注文する' }).click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/shopping\/complete/);
  });

  test('EF0303-UC01-T01 ログイン後に複数カートになればカートに戻す', async ({ page }) => {
    test.setTimeout(120_000);

    // Login as test customer and add product 2 (Sale Type 1) to cart
    await loginAsTestCustomer(page);
    await addProductToCartAndGoToCart(page, 1);

    // Logout
    await page.goto('/logout');
    await page.waitForLoadState('load');

    // As guest, find and add the multi-cart product (Sale Type 2) to cart
    await page.goto('/products/list?name=複数カートテスト商品');
    await page.waitForLoadState('load');
    await page.locator('.ec-shelfGrid__item a').first().click();
    await page.waitForLoadState('load');

    // Add to cart
    await page.locator('.add-cart').click();
    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    // Go to checkout
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    // Login at shopping login page
    await page.locator('input[name="login_email"]').fill('playwright@test.test');
    await page.locator('input[name="login_pass"]').fill(CUSTOMER_PASSWORD);
    await page.getByRole('button', { name: 'ログイン' }).click();
    await page.waitForLoadState('load');

    // Should redirect to cart with warning about incompatible cart items
    await expect(page.locator('body')).toContainText('同時購入できない商品');
  });

  test('EF0305-UC09-T03 複数配送設定画面での販売制限エラー', async ({ page }) => {
    test.setTimeout(120_000);

    // Login as test customer
    await loginAsTestCustomer(page);

    // Add product 2 to cart
    await addProductToCartAndGoToCart(page, 1);

    // Go to checkout
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/shopping$/);

    // Navigate to multi-shipping page
    await page.locator('button[data-path*="shipping_multiple"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('お届け先の複数指定');

    // Set quantity to an excessive number (exceeds stock)
    await page.locator('#form_shipping_multiple_0_shipping_0_quantity').fill('9999');

    // Submit
    await page.locator('#button__confirm').click();
    await page.waitForLoadState('load');

    // Verify stock error message
    await expect(page.locator('body')).toContainText('在庫が不足しております');
  });

  test('EF0305-UC01-T01 購入確認画面からカートに戻る', async ({ page }) => {
    // ログインしてカートに商品追加
    await loginAsTestCustomer(page);
    await addProductToCartAndGoToCart(page, 1);

    // レジに進む
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');

    // ご注文手続きページ
    await expect(page).toHaveURL(/\/shopping$/);

    // 確認する
    await page.locator('.ec-blockBtn--action', { hasText: '確認する' }).click();
    await page.waitForLoadState('load');

    // ご注文確認ページ
    await expect(page).toHaveURL(/\/shopping\/confirm/);

    // 「ご注文手続きに戻る」をクリック
    await page.getByRole('link', { name: 'ご注文手続きに戻る' }).click();
    await page.waitForLoadState('load');

    // ご注文手続きページに戻る
    await expect(page).toHaveURL(/\/shopping$/);

    // さらに「カートに戻る」をクリック
    await page.locator('a.ec-blockBtn--cancel', { hasText: 'カートに戻る' }).click();
    await page.waitForLoadState('load');

    // カートページに戻る
    await expect(page).toHaveURL(/\/cart/);
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('ショッピングカート');

    // カートに商品が残っていることを確認
    await expect(page.locator('.ec-cartRow__name').first()).toContainText('チェリーアイスサンド');
  });
});

/**
 * 配送方法・支払い方法の保存と復元 (#6819)。
 *
 * 保存（注文確定）→ 情報ボックス表示 → 手動復元のユーザージャーニーを検証する。
 * ゲスト除外・複数配送先・非公開時の警告などのバリエーションは
 * PHPUnit の機能テスト（ShoppingControllerPreferredShippingPaymentTest）でカバーする。
 *
 * 2 つのテストは保存→復元の順序に依存するため serial で実行する。
 */
test.describe.serial('Front Order 配送方法・支払い方法の保存と復元 (#6819)', () => {

  /**
   * Helper: カートに商品を入れ、レジに進んで /shopping まで遷移する。
   */
  async function goToShopping(page: import('@playwright/test').Page) {
    await addProductToCartAndGoToCart(page, 1);
    await page.locator('a.ec-blockBtn--action', { hasText: 'レジに進む' }).click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/shopping$/);
  }

  /**
   * Helper: 支払方法のラジオを選択する。
   * data-trigger="change" によりフォームが /shopping/redirect_to へ送信され、画面がリロードされる。
   */
  async function selectPayment(page: import('@playwright/test').Page, label: string) {
    const radio = page.getByRole('radio', { name: label });
    if (await radio.isChecked()) {
      return;
    }
    await Promise.all([
      page.waitForResponse((resp) => resp.url().includes('/shopping/redirect_to')),
      radio.check(),
    ]);
    await page.waitForLoadState('load');
    await expect(page.getByRole('radio', { name: label })).toBeChecked();
  }

  test.afterEach(async ({ page }) => {
    await clearCart(page);
  });

  test('保存チェックボックスONで注文すると次回の注文手続きに保存設定が表示される', async ({ page }) => {
    await loginAsTestCustomer(page);
    await goToShopping(page);

    // 保存対象を明確にするため支払方法を「銀行振込」に変更する
    await selectPayment(page, '銀行振込');

    // 確認画面へ
    await page.locator('.ec-blockBtn--action', { hasText: '確認する' }).click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/shopping\/confirm/);

    // 保存チェックボックスが表示され、初期値は OFF
    const checkbox = page.locator('input[name="_shopping_order[save_preferred_shipping_payment]"]');
    await expect(checkbox).toBeVisible();
    await expect(checkbox).not.toBeChecked();
    const checkboxArea = page.locator('.ec-totalBox__preferred');
    await expect(checkboxArea).toContainText('この配送方法と支払い方法を保存する');
    await expect(checkboxArea).toContainText('次回の注文時にワンクリックで設定できます');

    // ON にして注文する
    await checkbox.check();
    await page.locator('.ec-blockBtn--action', { hasText: '注文する' }).click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/shopping\/complete/);
    await expect(page.locator('.ec-pageHeader h1')).toContainText('ご注文完了');

    // 再度購入フローへ進むと、お客様情報の直後に情報ボックスが表示される
    await goToShopping(page);
    const box = page.locator('.ec-orderPreferred');
    await expect(box).toBeVisible();
    await expect(box).toContainText('保存された設定があります');
    await expect(box).toContainText('配送方法: サンプル業者');
    await expect(box).toContainText('支払い方法: 銀行振込');
    await expect(box.locator('button', { hasText: 'この設定を使用する' })).toBeVisible();
  });

  test('「この設定を使用する」で保存された設定が復元され合計が再計算される', async ({ page }) => {
    await loginAsTestCustomer(page);
    await goToShopping(page);

    // 前のテストで保存済みのため情報ボックスが表示される
    const box = page.locator('.ec-orderPreferred');
    await expect(box).toBeVisible();

    // 保存値(銀行振込)と異なる支払方法を選択しておく
    await selectPayment(page, '郵便振替');

    // 復元ボタンをクリックすると /shopping に戻り、成功メッセージが表示される
    await box.locator('button', { hasText: 'この設定を使用する' }).click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/shopping$/);
    await expect(page.locator('.ec-cartRole')).toContainText('保存された設定を適用しました');

    // 支払方法・配送方法が保存値に復元される
    await expect(page.getByRole('radio', { name: '銀行振込' })).toBeChecked();
    const selectedDelivery = page.locator('select[name="_shopping_order[Shippings][0][Delivery]"] option:checked');
    await expect(selectedDelivery).toHaveText('サンプル業者');

    // 合計金額(送料込み)が表示されている
    await expect(page.locator('.ec-totalBox__paymentTotal')).toContainText('￥');
  });
});
