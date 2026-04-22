import { test, expect } from '@playwright/test';

test.describe('Front Product (EF02)', () => {

  test('EF0201-UC01-T01 商品一覧ページ 初期表示', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('load');

    // ヘッダーナビからカテゴリ「新入荷」を選択
    await page.locator('.ec-itemNav__nav li a', { hasText: '新入荷' }).first().click();
    await page.waitForLoadState('load');

    // 商品一覧ページに遷移する
    await expect(page).toHaveURL(/products\/list\?category_id=/);

    // パンくずにカテゴリ名が表示される
    await expect(page.locator('.ec-topicpath')).toContainText('新入荷');

    // 商品がサムネイル表示される
    await expect(page.locator('.ec-shelfGrid')).toBeVisible();
    const items = page.locator('.ec-shelfGrid__item');
    await expect(items.first()).toBeVisible();
  });

  test('EF0201-UC03-T01 商品一覧ページ ソート', async ({ page }) => {
    // 全件検索で商品一覧を表示
    await page.goto('/products/list');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-shelfGrid__item').first()).toBeVisible();

    // デフォルトは「価格が低い順」
    const orderbySelect = page.locator('select[name="orderby"]');
    await expect(orderbySelect.locator('option[selected]')).toHaveText('価格が低い順');

    // ソート条件を「価格が高い順」に変更
    await orderbySelect.selectOption('価格が高い順');
    await page.waitForLoadState('load');

    // ソート条件が変更されている
    await expect(page.locator('select[name="orderby"] option[selected]')).toHaveText('価格が高い順');

    // 「新着順」に変更
    await page.locator('select[name="orderby"]').selectOption('新着順');
    await page.waitForLoadState('load');
    await expect(page.locator('select[name="orderby"] option[selected]')).toHaveText('新着順');
  });

  test('EF0201-UC04-T01 商品一覧ページ 表示件数', async ({ page }) => {
    // 全件検索で商品一覧を表示
    await page.goto('/products/list');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-shelfGrid__item').first()).toBeVisible();

    // デフォルトは20件
    const dispSelect = page.locator('select[name="disp_number"]');
    await expect(dispSelect.locator('option[selected]')).toHaveText('20件');

    // 表示件数を40件に変更
    await dispSelect.selectOption('40件');
    await page.waitForLoadState('load');
    await expect(page.locator('select[name="disp_number"] option[selected]')).toHaveText('40件');

    // 表示件数を60件に変更
    await page.locator('select[name="disp_number"]').selectOption('60件');
    await page.waitForLoadState('load');
    await expect(page.locator('select[name="disp_number"] option[selected]')).toHaveText('60件');
  });

  test('EF0202-UC01-T02 商品詳細 カテゴリリンク', async ({ page }) => {
    // 商品詳細ページへ遷移 (product 1 = EC-CUBE fixture data: '彩のジェラートCUBE')
    await page.goto('/products/detail/1');
    await page.waitForLoadState('load');

    // 商品名が表示される (EC-CUBE標準フィクスチャの商品名)
    await expect(page.locator('.ec-headingTitle')).toContainText('彩のジェラートCUBE');

    // ヘッダーナビからカテゴリを選択
    const navItem = page.locator('.ec-itemNav__nav li a', { hasText: '新入荷' }).first();
    await navItem.click();
    await page.waitForLoadState('load');

    // 商品一覧ページに遷移する
    await expect(page).toHaveURL(/products\/list\?category_id=/);
    await expect(page.locator('.ec-topicpath')).toContainText('新入荷');
    await expect(page.locator('.ec-shelfGrid')).toBeVisible();
  });

  test('EF0202-UC01-T03 商品詳細 サムネイル', async ({ page }) => {
    await page.goto('/products/detail/1');
    await page.waitForLoadState('load');

    // サムネイル画像が表示される
    const thumbContainer = page.locator('.item_nav');
    await expect(thumbContainer).toBeVisible();

    // 最初のサムネイル画像のsrcを確認
    const firstThumb = thumbContainer.locator('div:nth-child(1) img');
    const src = await firstThumb.getAttribute('src');
    expect(src).toMatch(/\/upload\/save_image\/.+\.png$/);
  });

  test('EF0202-UC02-T04 商品詳細(規格あり) カートに追加', async ({ page }) => {
    await page.goto('/products/detail/1');
    await page.waitForLoadState('load');

    // 規格1を選択（チョコ）
    await page.locator('#classcategory_id1').selectOption('チョコ');
    await page.waitForTimeout(500);

    // 規格2が表示されたら選択
    const select2 = page.locator('#classcategory_id2');
    if (await select2.isVisible()) {
      // 最初の実際のオプションを選択（「選択してください」以外）
      const options = select2.locator('option');
      const count = await options.count();
      for (let i = 0; i < count; i++) {
        const text = await options.nth(i).textContent();
        if (text && !text.includes('選択してください')) {
          await select2.selectOption({ index: i });
          break;
        }
      }
      await page.waitForTimeout(500);
    }

    // 数量を設定
    await page.locator('#quantity').fill('1');

    // カートに入れるボタンをクリック
    await page.locator('.add-cart').click();

    // モーダルが表示される
    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });

    // カートに追加されたメッセージを確認
    await expect(page.locator('#ec-modal-header')).toContainText('カートに追加しました');

    // カートへ進む
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    // カートページに遷移
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('ショッピングカート');

    // 商品名が表示される
    const cartItemName = page.locator('.ec-cartRow__name').first();
    await expect(cartItemName).toContainText('彩のジェラートCUBE');

    // 数量が表示される
    const cartItemAmount = page.locator('.ec-cartRow__amount').first();
    await expect(cartItemAmount).toContainText('1');

    // カートから削除
    page.on('dialog', dialog => dialog.accept());
    await page.locator('.ec-cartRow__delColumn a').first().click();
    await page.waitForLoadState('load');
  });

  test('EF0202-UC02-T01_simple 商品詳細 カートに入れる(規格なし)', async ({ page }) => {
    // Product ID 2 = チェリーアイスサンド (no product classes / 規格なし)
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');

    await expect(page.locator('.ec-headingTitle')).toContainText('チェリーアイスサンド');

    // Set quantity to 1 and add to cart
    await page.locator('#quantity').fill('1');
    await page.locator('.add-cart').click();

    // Modal appears
    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await expect(page.locator('#ec-modal-header')).toContainText('カートに追加しました');

    // Go to cart
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    // Verify cart page
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('ショッピングカート');
    await expect(page.locator('.ec-cartRow__name').first()).toContainText('チェリーアイスサンド');
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('1');

    // Clean up - delete from cart
    page.on('dialog', dialog => dialog.accept());
    await page.locator('.ec-cartRow__delColumn a').first().click();
    await page.waitForLoadState('load');
  });

  test('EF0202-UC02-T01_quantity 商品詳細 数量変更してカートに入れる', async ({ page }) => {
    // Product ID 2 = チェリーアイスサンド (no product classes / 規格なし)
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');

    await expect(page.locator('.ec-headingTitle')).toContainText('チェリーアイスサンド');

    // Set quantity to 3 and add to cart
    await page.locator('#quantity').fill('3');
    await page.locator('.add-cart').click();

    // Modal appears
    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await expect(page.locator('#ec-modal-header')).toContainText('カートに追加しました');

    // Go to cart
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    // Verify cart page shows quantity 3
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('ショッピングカート');
    await expect(page.locator('.ec-cartRow__name').first()).toContainText('チェリーアイスサンド');
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('3');

    // Clean up - delete from cart
    page.on('dialog', dialog => dialog.accept());
    await page.locator('.ec-cartRow__delColumn a').first().click();
    await page.waitForLoadState('load');
  });

  /**
   * Helper: search for '在庫テスト商品' and navigate to its detail page.
   * Returns the detail page URL path (e.g. '/products/detail/22').
   */
  async function gotoStockProduct(page: import('@playwright/test').Page): Promise<string> {
    await page.goto('/products/list?name=在庫テスト商品');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-shelfGrid__item').first()).toBeVisible();
    await page.locator('.ec-shelfGrid__item a').first().click();
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-headingTitle')).toContainText('在庫テスト商品');
    return new URL(page.url()).pathname;
  }

  test('EF0202-UC02-T01_stock1 商品詳細カート 注文数<販売制限数<在庫数', async ({ page }) => {
    // 在庫テスト商品: stock=10, saleLimit=5
    // qty=4 < saleLimit=5 < stock=10 => カートに正常追加
    await gotoStockProduct(page);

    await page.locator('#quantity').fill('4');
    await page.locator('.add-cart').click();

    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await expect(page.locator('#ec-modal-header')).toContainText('カートに追加しました');

    // カートへ進んで数量確認
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-cartRow__name').first()).toContainText('在庫テスト商品');
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('4');

    // カートを空にする
    page.on('dialog', dialog => dialog.accept());
    await page.locator('.ec-cartRow__delColumn a').first().click();
    await page.waitForLoadState('load');
  });

  test('EF0202-UC02-T02_stock2 商品詳細カート 販売制限数<注文数<在庫数', async ({ page }) => {
    // 在庫テスト商品: stock=10, saleLimit=5
    // saleLimit=5 < qty=6 < stock=10 => 販売制限エラー、カート数量は5
    await gotoStockProduct(page);

    await page.locator('#quantity').fill('6');
    await page.locator('.add-cart').click();

    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await expect(page.locator('#ec-modal-header')).toContainText('販売制限しております');

    // カートへ進んで数量が販売制限数に制限されていることを確認
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-cartRow__name').first()).toContainText('在庫テスト商品');
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('5');

    // カートを空にする
    page.on('dialog', dialog => dialog.accept());
    await page.locator('.ec-cartRow__delColumn a').first().click();
    await page.waitForLoadState('load');
  });

  test('EF0202-UC02-T03_stock3 商品詳細カート 販売制限数<在庫数<注文数', async ({ page }) => {
    // 在庫テスト商品: stock=10, saleLimit=5
    // saleLimit=5 < stock=10 < qty=12 => 販売制限エラー、カート数量は5
    await gotoStockProduct(page);

    await page.locator('#quantity').fill('12');
    await page.locator('.add-cart').click();

    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await expect(page.locator('#ec-modal-header')).toContainText('販売制限しております');

    // カートへ進んで数量が販売制限数に制限されていることを確認
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-cartRow__name').first()).toContainText('在庫テスト商品');
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('5');

    // カートを空にする
    page.on('dialog', dialog => dialog.accept());
    await page.locator('.ec-cartRow__delColumn a').first().click();
    await page.waitForLoadState('load');
  });

  test('EF0201-UC04-T02 商品一覧ページング', async ({ page }) => {
    // Navigate to product list (新入荷 category)
    await page.goto('/');
    await page.waitForLoadState('load');
    await page.locator('.ec-itemNav__nav li a', { hasText: '新入荷' }).first().click();
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-shelfGrid__item').first()).toBeVisible();

    // Verify page 1 is active
    await page.locator('li.ec-pager__item--active').scrollIntoViewIfNeeded();
    await page.waitForTimeout(500);
    await expect(page.locator('li.ec-pager__item--active')).toContainText('1');

    // Verify page 2 and '次へ' links exist
    await expect(page.locator('li.ec-pager__item').first()).toBeVisible();

    // Click page 2
    await page.locator('li.ec-pager__item').first().locator('a').click();
    await page.waitForLoadState('load');
    await page.locator('li.ec-pager__item--active').scrollIntoViewIfNeeded();
    await page.waitForTimeout(500);
    await expect(page.locator('li.ec-pager__item--active')).toContainText('2');

    // Click '前へ' (should be first pager item now)
    await page.locator('li.ec-pager__item').first().locator('a').click();
    await page.waitForLoadState('load');
    await page.locator('li.ec-pager__item--active').scrollIntoViewIfNeeded();
    await page.waitForTimeout(500);
    await expect(page.locator('li.ec-pager__item--active')).toContainText('1');

    // Click '次へ' (should be last pager item)
    await page.locator('li.ec-pager__item').last().locator('a').click();
    await page.waitForLoadState('load');
    await page.locator('li.ec-pager__item--active').scrollIntoViewIfNeeded();
    await page.waitForTimeout(500);
    await expect(page.locator('li.ec-pager__item--active')).toContainText('2');
  });

  test('EF0202-UC01-T01 商品詳細初期表示 (品切れ)', async ({ page }) => {
    // Navigate to product detail for a product with 0 stock
    // This uses the admin API to set stock to 0, then checks front display.
    // We'll check product 2 (チェリーアイスサンド) which is a simple product.
    // First, set stock to 0 via admin
    const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
    await page.goto(`/${adminRoute}/product/product/2/edit`);
    await page.waitForLoadState('load');
    // May need to login
    if (await page.locator('#login_id').count() > 0) {
      await page.locator('#login_id').fill(process.env.ADMIN_USER || 'admin');
      await page.locator('#password').fill(process.env.ADMIN_PASSWORD || 'password');
      await page.getByRole('button', { name: 'ログイン' }).click();
      await page.waitForLoadState('load');
      await page.goto(`/${adminRoute}/product/product/2/edit`);
      await page.waitForLoadState('load');
    }
    await page.locator('#admin_product_class_stock').fill('0');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Check front display
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');

    // "カートに入れる" button should be disabled and show out of stock message
    // Out-of-stock button uses type="button" with disabled attribute, not type="submit"
    await expect(page.locator('#form1 .ec-productRole__btn button')).toContainText('ただいま品切れ中です');

    // Restore stock
    await page.goto(`/${adminRoute}/product/product/2/edit`);
    await page.waitForLoadState('load');
    await page.locator('#admin_product_class_stock').fill('100');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('EF0202-UC02-T05 商品詳細カート5 規格あり_販売制限数<注文数<在庫数', async ({ page }) => {
    // Product 1 (彩のジェラートCUBE) has class categories
    // Stock=10, saleLimit=2 per fixture; qty=3 > saleLimit=2
    await page.goto('/products/detail/1');
    await page.waitForLoadState('load');

    // Select class category 1 (チョコ)
    await page.locator('#classcategory_id1').selectOption('チョコ');
    await page.waitForTimeout(500);

    // Select class category 2 if visible
    const select2 = page.locator('#classcategory_id2');
    if (await select2.isVisible()) {
      const options = select2.locator('option');
      const count = await options.count();
      for (let i = 0; i < count; i++) {
        const text = await options.nth(i).textContent();
        if (text && !text.includes('選択してください')) {
          await select2.selectOption({ index: i });
          break;
        }
      }
      await page.waitForTimeout(500);
    }

    // Set quantity to 3 (above sale limit of 2)
    await page.locator('#quantity').fill('3');
    await page.locator('.add-cart').click();

    // Modal should appear with sale limit message
    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await expect(page.locator('#ec-modal-header')).toContainText('販売制限しております');

    // Go to cart and verify quantity is capped at sale limit
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-cartRow__name').first()).toContainText('彩のジェラートCUBE');
    // Quantity should be sale limit (2)
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('2');

    // Clean up
    page.on('dialog', dialog => dialog.accept());
    await page.locator('.ec-cartRow__delColumn a').first().click();
    await page.waitForLoadState('load');
  });

  test('EF0202-UC02-T06 商品詳細カート6 規格あり_販売制限数<在庫数<注文数', async ({ page }) => {
    // Product 1 (彩のジェラートCUBE) has class categories
    // Stock=10, saleLimit=2 per fixture; qty=12 > stock=10 > saleLimit=2
    await page.goto('/products/detail/1');
    await page.waitForLoadState('load');

    await page.locator('#classcategory_id1').selectOption('チョコ');
    await page.waitForTimeout(500);

    const select2 = page.locator('#classcategory_id2');
    if (await select2.isVisible()) {
      const options = select2.locator('option');
      const count = await options.count();
      for (let i = 0; i < count; i++) {
        const text = await options.nth(i).textContent();
        if (text && !text.includes('選択してください')) {
          await select2.selectOption({ index: i });
          break;
        }
      }
      await page.waitForTimeout(500);
    }

    // Set quantity much higher than stock and sale limit
    await page.locator('#quantity').fill('12');
    await page.locator('.add-cart').click();

    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    await expect(page.locator('#ec-modal-header')).toContainText('販売制限しております');

    // Go to cart and verify quantity is capped at sale limit
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-cartRow__name').first()).toContainText('彩のジェラートCUBE');
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('2');

    // Clean up
    page.on('dialog', dialog => dialog.accept());
    await page.locator('.ec-cartRow__delColumn a').first().click();
    await page.waitForLoadState('load');
  });

  test('EF0202-UC03-T01 商品詳細カート7 在庫数<注文数', async ({ page }) => {
    // Use admin to set stock to 3 for product 2 (チェリーアイスサンド)
    const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
    await page.goto(`/${adminRoute}/product/product/2/edit`);
    await page.waitForLoadState('load');
    if (await page.locator('#login_id').count() > 0) {
      await page.locator('#login_id').fill(process.env.ADMIN_USER || 'admin');
      await page.locator('#password').fill(process.env.ADMIN_PASSWORD || 'password');
      await page.getByRole('button', { name: 'ログイン' }).click();
      await page.waitForLoadState('load');
      await page.goto(`/${adminRoute}/product/product/2/edit`);
      await page.waitForLoadState('load');
    }
    await page.locator('#admin_product_class_stock').fill('3');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Go to front product detail
    await page.goto('/products/detail/2');
    await page.waitForLoadState('load');

    // Set quantity to 4 (> stock of 3)
    await page.locator('#quantity').fill('4');
    await page.locator('.add-cart').click();

    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });
    // Should show stock insufficient message
    await expect(page.locator('#ec-modal-header')).toContainText('在庫が不足しております');

    // Go to cart and verify quantity is capped at stock (3)
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-cartRow__name').first()).toContainText('チェリーアイスサンド');
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('3');

    // Clean up cart
    page.on('dialog', dialog => dialog.accept());
    await page.locator('.ec-cartRow__delColumn a').first().click();
    await page.waitForLoadState('load');

    // Restore stock
    await page.goto(`/${adminRoute}/product/product/2/edit`);
    await page.waitForLoadState('load');
    await page.locator('#admin_product_class_stock').fill('100');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('EF0202-UC02-T01 商品詳細 カート追加と削除', async ({ page }) => {
    await page.goto('/products/detail/1');
    await page.waitForLoadState('load');

    // 規格を選択
    await page.locator('#classcategory_id1').selectOption('チョコ');
    await page.waitForTimeout(500);

    const select2 = page.locator('#classcategory_id2');
    if (await select2.isVisible()) {
      const options = select2.locator('option');
      const count = await options.count();
      for (let i = 0; i < count; i++) {
        const text = await options.nth(i).textContent();
        if (text && !text.includes('選択してください')) {
          await select2.selectOption({ index: i });
          break;
        }
      }
      await page.waitForTimeout(500);
    }

    // 数量を2に設定
    await page.locator('#quantity').fill('2');
    await page.locator('.add-cart').click();

    // モーダルが表示される
    await expect(page.locator('div.ec-modal-box')).toBeVisible({ timeout: 10_000 });

    // カートへ進む
    await page.locator('div.ec-modal-box > div > a').click();
    await page.waitForLoadState('load');

    // カートページで数量2が表示される
    await expect(page.locator('.ec-cartRow__amount').first()).toContainText('2');

    // 商品を削除
    page.on('dialog', dialog => dialog.accept());
    await page.locator('.ec-cartRow__delColumn a').first().click();
    await page.waitForLoadState('load');

    // カートが空になっている（商品行が消える）
    await expect(page.locator('.ec-cartRow__name')).toHaveCount(0);
  });
});
