import { test, expect } from '@playwright/test';
import path from 'path';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
const pageTitle = '.c-pageTitle';
const searchResultMsg = '#search_form > div.c-outsideBlock__contents.mb-5 > span';
const searchResultList = '#page_admin_product table tbody';
const searchBtn = '#search_form .c-outsideBlock__contents button';
const noResultMsg = '.c-contentsArea .c-contentsArea__cols div.text-center.h5';

async function goProductList(page: import('@playwright/test').Page) {
  await page.goto(`/${adminRoute}/product`);
  await page.waitForLoadState('load');
}

async function searchProduct(page: import('@playwright/test').Page, keyword: string = '') {
  await page.locator('#admin_search_product_id').fill(keyword);
  await page.locator(searchBtn).click();
  await page.waitForLoadState('load');
}

test.describe('Admin Product (EA03)', () => {
  // Tests are interdependent (e.g., create then search), so run in serial mode
  test.describe.configure({ mode: 'serial' });

  test('product_商品検索', async ({ page }) => {
    await goProductList(page);
    await searchProduct(page, 'ジェラート');
    // May match duplicates from prior retries, so just verify at least 1 result
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);
    await expect(page.locator(searchResultList)).toContainText('彩のジェラートCUBE');

    // 空検索 → 全件表示 (件数は固定値でなく正規表現で確認)
    await goProductList(page);
    await searchProduct(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // 結果0件
    await goProductList(page);
    await searchProduct(page, 'gege@gege.com');
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：0件が該当しました');
  });

  test('product_商品検索結果無', async ({ page }) => {
    await goProductList(page);
    await searchProduct(page, 'お箸');
    await expect(page.locator(noResultMsg)).toContainText('検索条件に合致するデータが見つかりませんでした');
  });

  test('product_商品検索エラー - EA0301-UC01-T03', async ({ page }) => {
    await goProductList(page);
    // Open advanced search
    await page.locator('#search_form .c-outsideBlock__contents a span, #search_form a.d-inline-block').first().click();
    await page.waitForTimeout(500);

    // Inject a fake status value (999) that doesn't exist in the database
    await page.evaluate(() => {
      const form = document.getElementById('search_form');
      if (form) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'admin_search_product[status][]';
        input.value = '999';
        form.appendChild(input);
      }
    });

    await page.locator(searchBtn).click();
    await page.waitForLoadState('load');
    await expect(page.locator(noResultMsg)).toContainText('検索条件に誤りがあります');
  });

  test('product_規格確認のポップアップ表示', async ({ page }) => {
    await goProductList(page);
    await searchProduct(page, '');

    // 規格あり商品 (ex-product-1) の規格確認ボタンをクリック
    await page.locator('#ex-product-1 td:nth-child(7) button').click();
    await expect(page.locator('#productClassesModal')).toBeVisible({ timeout: 5_000 });

    // キャンセル
    await page.locator('#productClassesModal [data-bs-dismiss="modal"]:visible').last().click();
    await page.waitForTimeout(500);
    await expect(page.locator('#productClassesModal')).not.toBeVisible();
  });

  test('product_ポップアップから規格編集画面に遷移', async ({ page }) => {
    await goProductList(page);
    await searchProduct(page, '');

    // 規格確認ポップアップを開く
    await page.locator('#ex-product-1 td:nth-child(7) button').click();
    await expect(page.locator('#productClassesModal')).toBeVisible({ timeout: 5_000 });

    // 規格編集リンクをクリック
    await page.locator('#productClassesModal a[href*="class"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品規格登録');
  });

  test('product_CSV出力', async ({ page }) => {
    await goProductList(page);
    await searchProduct(page, '');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // CSVダウンロード
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.locator('a[href*="/product/export"]').click(),
    ]);
    expect(download.suggestedFilename()).toMatch(/^product_\d{14}\.csv$/);
  });

  test('product_CSV出力項目設定', async ({ page }) => {
    await goProductList(page);

    // CSV出力項目設定リンクをクリック
    await page.locator('a[href*="/setting/shop/csv/1"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('CSV出力項目設定');
  });

  test('product_一覧でのソート', async ({ page }) => {
    await goProductList(page);
    await searchProduct(page, '');

    // ID昇順
    await page.locator('[data-sortkey="product_id"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.listSort-current[data-sortkey="product_id"] .fa-arrow-up')).toBeVisible();

    // ID降順
    await page.locator('[data-sortkey="product_id"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.listSort-current[data-sortkey="product_id"] .fa-arrow-down')).toBeVisible();

    // 更新日昇順
    await page.locator('[data-sortkey="update_date"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.listSort-current[data-sortkey="update_date"] .fa-arrow-up')).toBeVisible();

    // 更新日降順
    await page.locator('[data-sortkey="update_date"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.listSort-current[data-sortkey="update_date"] .fa-arrow-down')).toBeVisible();
  });

  test('product_商品登録非公開', async ({ page }) => {
    await page.goto(`/${adminRoute}/product/product/new`);
    await page.waitForLoadState('load');
    await page.locator('#admin_product_name').fill('test product1');
    await page.locator('#admin_product_class_price02').fill('1000');
    await page.locator('#admin_product_category_1').check();
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('product_商品登録公開', async ({ page }) => {
    await page.goto(`/${adminRoute}/product/product/new`);
    await page.waitForLoadState('load');
    await page.locator('#admin_product_name').fill('test product2');
    await page.locator('#admin_product_class_price02').fill('2000');
    await page.locator('#admin_product_Status').selectOption('公開');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('product_商品の削除', async ({ page }) => {
    // 削除用商品を作成
    await page.goto(`/${adminRoute}/product/product/new`);
    await page.waitForLoadState('load');
    await page.locator('#admin_product_name').fill('削除用商品');
    await page.locator('#admin_product_class_price02').fill('1000');
    await page.locator('#admin_product_Status').selectOption('公開');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // 商品一覧で削除
    await goProductList(page);
    await searchProduct(page, '削除用商品');
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：1件');
    await page.locator('#form_bulk table tbody tr:first-child input[type="checkbox"]').check();
    await page.locator('#form_bulk button.btn-ec-delete').click();
    await page.waitForTimeout(500);
    await page.locator('.modal.show .btn-ec-delete').click();
    await expect(page.locator('.modal.show .modal-body')).toContainText('商品の削除処理が完了しました', { timeout: 30_000 });

    await goProductList(page);
    await searchProduct(page, '削除用商品');
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：0件');
  });

  test('product_商品の廃止', async ({ page }) => {
    // Create a dedicated product for the discontinue test to avoid affecting existing products
    await page.goto(`/${adminRoute}/product/product/new`);
    await page.waitForLoadState('load');
    await page.locator('#admin_product_name').fill('廃止テスト用商品');
    await page.locator('#admin_product_class_price02').fill('500');
    await page.locator('#admin_product_Status').selectOption('公開');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Search for the product and discontinue it
    await goProductList(page);
    await searchProduct(page, '廃止テスト用商品');
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：1件');

    await page.locator('#form_bulk table tbody tr:first-child input[type="checkbox"]').check();
    await page.locator('#form_bulk button:has-text("廃止")').first().click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('廃止: 1件が正常に適用されました');
  });

  test('product_商品編集規格なし', async ({ page }) => {
    // Search for "test product1" created by earlier test, go to edit
    await goProductList(page);
    await searchProduct(page, 'test product1');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Click on product name to go to edit page
    await page.locator('#form_bulk table tbody tr:first-child td:nth-child(4) a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品登録');

    // Change name and save
    await page.locator('#admin_product_name').fill('test product11');
    await page.locator('#admin_product_category_1').check();
    await page.locator('#admin_product_category_2').check();
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('product_商品編集規格あり', async ({ page }) => {
    // Product without classes: fields are visible
    await goProductList(page);
    await searchProduct(page, 'test product1');
    await page.locator('#form_bulk table tbody tr:first-child td:nth-child(4) a').click();
    await page.waitForLoadState('load');

    await expect(page.locator('#admin_product_class_sale_type')).toBeVisible();
    await expect(page.locator('#admin_product_class_price02')).toBeVisible();
    await expect(page.locator('#admin_product_class_price01')).toBeVisible();
    await expect(page.locator('#admin_product_class_stock')).toBeVisible();
    await expect(page.locator('#admin_product_class_code')).toBeVisible();
    await expect(page.locator('#admin_product_class_sale_limit')).toBeVisible();
    await expect(page.locator('#admin_product_class_delivery_duration')).toBeVisible();

    // Product with classes: fields are not present
    await goProductList(page);
    await searchProduct(page, '彩のジェラートCUBE');
    await page.locator('#form_bulk table tbody tr:first-child td:nth-child(4) a').click();
    await page.waitForLoadState('load');

    await expect(page.locator('#admin_product_class_sale_type')).toHaveCount(0);
    await expect(page.locator('#admin_product_class_price02')).toHaveCount(0);
    await expect(page.locator('#admin_product_class_price01')).toHaveCount(0);
    await expect(page.locator('#admin_product_class_stock')).toHaveCount(0);
    await expect(page.locator('#admin_product_class_code')).toHaveCount(0);
    await expect(page.locator('#admin_product_class_sale_limit')).toHaveCount(0);
    await expect(page.locator('#admin_product_class_delivery_duration')).toHaveCount(0);

    // Save should still work
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('product_商品の確認', async ({ page }) => {
    // From product edit page, click "商品を確認" link
    await goProductList(page);
    await searchProduct(page, 'test product1');
    await page.locator('#form_bulk table tbody tr:first-child td:nth-child(4) a').click();
    await page.waitForLoadState('load');

    // Click "商品を確認" button (opens in new tab)
    const [newPage] = await Promise.all([
      page.context().waitForEvent('page'),
      page.locator('#preview a[target="_blank"]').click(),
    ]);
    await newPage.waitForLoadState('load');
    expect(newPage.url()).toContain('/products/detail/');
    await newPage.close();
  });

  test('product_一覧からの商品確認', async ({ page }) => {
    // From product list, click the eye icon (view) link
    await goProductList(page);
    await searchProduct(page, '');

    // The view link is in the action column with target="_blank" and fa-eye icon
    const [newPage] = await Promise.all([
      page.context().waitForEvent('page'),
      page.locator('#form_bulk table tbody tr:first-child td.align-middle.pe-3 a[target="_blank"]').click(),
    ]);
    await newPage.waitForLoadState('load');
    expect(newPage.url()).toContain('/products/detail/');
    await newPage.close();
  });

  test('product_規格登録_', async ({ page }) => {
    // Empty input should not create
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('規格管理');

    await page.locator('#admin_class_name_backend_name').fill('');
    await page.locator('#admin_class_name_name').fill('');
    await page.locator('#form1 button').click();
    await page.waitForLoadState('load');

    // 成功メッセージが出ないことを確認 (バリデーションエラー)
    await expect(page.locator('.alert-success')).not.toBeVisible();

    // Create a valid class
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');
    await page.locator('#admin_class_name_backend_name').fill('backend test class1');
    await page.locator('#admin_class_name_name').fill('display test class1');
    await page.locator('#form1 button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('product_規格編集', async ({ page }) => {
    // Go to class name page, click edit on the first actual class (li:3)
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');

    // Click edit on row 3
    await page.locator('ul.list-group > li:nth-child(3) > div > div.col-auto.text-end > a.action-edit').click();
    await page.waitForTimeout(500);

    // Verify edit form appears with inputs
    await expect(page.locator('ul.list-group > li:nth-child(3) form')).toBeVisible();

    // Get current values and verify they match what we created
    const nameVal = await page.locator('ul.list-group > li:nth-child(3) form input[type=text]').first().inputValue();
    expect(nameVal).toBeTruthy();

    // Submit the edit (save without changing)
    await page.locator('ul.list-group > li:nth-child(3) form button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Clean up: delete the class we created in product_規格登録_
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');
    // Find the class with 'display test class1' and delete it
    const listItems = await page.locator('ul.list-group > li').count();
    for (let i = 3; i <= listItems; i++) {
      const nameEl = page.locator(`ul.list-group > li:nth-child(${i}) > div > div.col.d-flex.align-items-center > a`);
      const txt = await nameEl.textContent();
      if (txt?.includes('display test class1')) {
        await page.locator(`ul.list-group > li:nth-child(${i}) > div > div.col-auto.text-end > div > a`).click();
        await page.waitForTimeout(500);
        await page.locator('#DeleteModal > div > div > div.modal-footer > a').click();
        await page.waitForLoadState('load');
        break;
      }
    }
  });

  test('product_規格削除', async ({ page }) => {
    // Create a class to delete
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');
    await page.locator('#admin_class_name_backend_name').fill('backend test class1');
    await page.locator('#admin_class_name_name').fill('display test class1');
    await page.locator('#form1 button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Delete the newly created class (should be at li:nth-child(3))
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');
    // Find the new class (it's at the top)
    const nameText = await page.locator('ul.list-group > li:nth-child(3) > div > div.col.d-flex.align-items-center > a').textContent();
    expect(nameText).toContain('display test class1');

    await page.locator('ul.list-group > li:nth-child(3) > div > div.col-auto.text-end > div > a').click();
    await page.waitForTimeout(500);
    await expect(page.locator('#DeleteModal')).toBeVisible();
    await page.locator('#DeleteModal > div > div > div.modal-footer > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('削除しました');
  });

  test('product_規格CSVダウンロード', async ({ page }) => {
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.locator('a[href*="class_name/export"]').click(),
    ]);
    expect(download.suggestedFilename()).toMatch(/^class_name_\d{14}\.csv$/);
  });

  test('product_分類登録', async ({ page }) => {
    // Create a class first for the category
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');
    await page.locator('#admin_class_name_backend_name').fill('test class2');
    await page.locator('#admin_class_name_name').fill('test class2');
    await page.locator('#form1 button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Navigate to class categories
    await page.locator('ul.list-group > li:nth-child(3) > div > div.col.d-flex.align-items-center > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('規格分類管理');

    // Create a class category
    await page.locator('#admin_class_category_name').fill('test class2 category1');
    await page.locator('#form1 button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
    await expect(page.locator('ul.list-group > li:nth-child(3) > div > div.col.d-flex.align-items-center')).toContainText('test class2 category1');

    // Edit the class category
    await page.locator('ul.list-group > li:nth-child(3) > div > div.col-auto.text-end > a.action-edit').click();
    await page.waitForTimeout(500);
    await page.locator('ul.list-group > li:nth-child(3) form input[type=text]').first().fill('edit class category');
    await page.locator('ul.list-group > li:nth-child(3) form button[type=submit]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
    await expect(page.locator('ul.list-group > li:nth-child(3) > div > div.col.d-flex.align-items-center')).toContainText('edit class category');

    // Delete the class category
    await page.locator('ul.list-group > li:nth-child(3) > div > div.col-auto.text-end > div > a').click();
    await page.waitForTimeout(500);
    await expect(page.locator('#DeleteModal')).toBeVisible();
    await page.locator('#DeleteModal > div > div > div.modal-footer > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('削除しました');

    // Clean up: delete the test class2
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');
    const listItems = await page.locator('ul.list-group > li').count();
    for (let i = 3; i <= listItems; i++) {
      const nameEl = page.locator(`ul.list-group > li:nth-child(${i}) > div > div.col.d-flex.align-items-center > a`);
      const txt = await nameEl.textContent();
      if (txt?.includes('test class2')) {
        await page.locator(`ul.list-group > li:nth-child(${i}) > div > div.col-auto.text-end > div > a`).click();
        await page.waitForTimeout(500);
        await page.locator('#DeleteModal > div > div > div.modal-footer > a').click();
        await page.waitForLoadState('load');
        break;
      }
    }
  });

  test('product_分類CSVダウンロード', async ({ page }) => {
    // Navigate to first class's categories
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');
    await page.locator('ul.list-group > li:nth-child(3) > div > div.col.d-flex.align-items-center > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('規格分類管理');

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.locator('a[href*="class_category/export"]').click(),
    ]);
    expect(download.suggestedFilename()).toMatch(/^class_category_\d{14}\.csv$/);
  });

  test('product_カテゴリ登録', async ({ page }) => {
    await page.goto(`/${adminRoute}/product/category`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('カテゴリ管理');

    // Create a category
    await page.locator('#admin_category_name').fill('test category1');
    await page.locator('#form1 button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Edit the category inline
    // Find the category row - newly created appears at the bottom
    const listItems = await page.locator('ul.list-group > li').count();
    let targetRow = -1;
    for (let i = 3; i <= listItems; i++) {
      const nameEl = page.locator(`ul.list-group > li:nth-child(${i}) > div > div.col.d-flex.align-items-center > a`);
      const count = await nameEl.count();
      if (count > 0) {
        const txt = await nameEl.textContent();
        if (txt?.includes('test category1')) {
          targetRow = i;
          break;
        }
      }
    }
    expect(targetRow).toBeGreaterThan(0);

    // Click edit
    await page.locator(`ul.list-group > li:nth-child(${targetRow}) > div > div.col-auto.text-end > a:nth-child(3)`).click();
    await page.waitForTimeout(500);

    // Fill in new name and submit
    await page.locator(`ul.list-group > li:nth-child(${targetRow}) form.mode-edit input[type="text"]`).fill('test category11');
    await page.locator(`ul.list-group > li:nth-child(${targetRow}) form.mode-edit button[type="submit"]`).click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Delete the category
    const listItemsAfter = await page.locator('ul.list-group > li').count();
    for (let i = 3; i <= listItemsAfter; i++) {
      const nameEl = page.locator(`ul.list-group > li:nth-child(${i}) > div > div.col.d-flex.align-items-center > a`);
      const count = await nameEl.count();
      if (count > 0) {
        const txt = await nameEl.textContent();
        if (txt?.includes('test category11')) {
          await page.locator(`ul.list-group > li:nth-child(${i}) > div > div.col-auto.text-end > div > a`).click();
          await page.waitForTimeout(500);
          await expect(page.locator('#DeleteModal')).toBeVisible();
          await page.locator('#DeleteModal > div > div > div.modal-footer > a').click();
          await page.waitForLoadState('load');
          break;
        }
      }
    }
  });

  test('product_タグ登録', async ({ page }) => {
    // Empty tag should not create
    await page.goto(`/${adminRoute}/product/tag`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('タグ管理');

    await page.locator('#admin_product_tag_name').fill('');
    await page.locator('.c-primaryCol .list-group > li:first-child form button[type="submit"]').click();
    await page.waitForLoadState('load');
    // Should not see success message for empty tag (HTML5 required validation prevents submission)

    // Create a valid tag
    const tagName = `new-tag-${Date.now()}`;
    await page.goto(`/${adminRoute}/product/tag`);
    await page.waitForLoadState('load');
    await page.locator('#admin_product_tag_name').fill(tagName);
    await page.locator('.c-primaryCol .list-group > li:first-child form button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
    await expect(page.locator('.c-primaryCol .list-group')).toContainText(tagName);
  });

  test('product_タグ編集', async ({ page }) => {
    // Create a tag to edit
    const originalTagName = `edit-orig-tag-${Date.now()}`;
    const editedTagName = `edit-tag-${Date.now()}`;
    await page.goto(`/${adminRoute}/product/tag`);
    await page.waitForLoadState('load');
    await page.locator('#admin_product_tag_name').fill(originalTagName);
    await page.locator('.c-primaryCol .list-group > li:first-child form button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Find the tag row by name and click edit
    const tagRow = page.locator(`.c-primaryCol .list-group > li:has-text("${originalTagName}")`);
    await tagRow.locator('a[data-bs-original-title=編集]').click();
    await page.waitForTimeout(500);

    // Fill in new name
    await tagRow.locator('input[type=text]').fill(editedTagName);

    // Submit
    await tagRow.locator('.btn-ec-conversion').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
    await expect(page.locator('.c-primaryCol .list-group')).toContainText(editedTagName);
  });

  test('product_タグ削除', async ({ page }) => {
    // First create a tag so we have something safe to delete
    await page.goto(`/${adminRoute}/product/tag`);
    await page.waitForLoadState('load');
    const tagName = `delete-tag-${Date.now()}`;
    await page.locator('#admin_product_tag_name').fill(tagName);
    await page.locator('.c-primaryCol .list-group > li:first-child form button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
    await expect(page.locator('.c-primaryCol .list-group')).toContainText(tagName);

    // Find the tag row by name and click delete
    const tagRow = page.locator(`.c-primaryCol .list-group > li:has-text("${tagName}")`);
    await tagRow.locator('a[data-bs-target="#DeleteModal"]').click();
    await page.waitForTimeout(500);
    await expect(page.locator('#DeleteModal')).toBeVisible();
    await page.locator('.modal.show .btn-ec-delete').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('削除しました');
    await expect(page.locator('.c-primaryCol .list-group')).not.toContainText(tagName);
  });

  test('product_商品CSV登録雛形ファイルダウンロード', async ({ page }) => {
    await page.goto(`/${adminRoute}/product/product_csv_upload`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品CSV登録');

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.locator('#download-button').click(),
    ]);
    expect(download.suggestedFilename()).toBe('product.csv');
  });

  test('product_カテゴリCSV登録雛形ファイルダウンロード', async ({ page }) => {
    await page.goto(`/${adminRoute}/product/category_csv_upload`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('カテゴリCSV登録');

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.locator('#download-button').click(),
    ]);
    expect(download.suggestedFilename()).toBe('category.csv');
  });

  test('product_規格CSV登録雛形ファイルダウンロード', async ({ page }) => {
    await page.goto(`/${adminRoute}/product/class_name_csv_upload`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('規格CSV登録');

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.locator('#download-button').click(),
    ]);
    expect(download.suggestedFilename()).toBe('class_name.csv');
  });

  test('product_規格分類CSV登録雛形ファイルダウンロード', async ({ page }) => {
    await page.goto(`/${adminRoute}/product/class_category_csv_upload`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('規格分類CSV登録');

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.locator('#download-button').click(),
    ]);
    expect(download.suggestedFilename()).toBe('class_category.csv');
  });

  test('product_商品の複製', async ({ page }) => {
    // Search for a product with classes (e.g., 彩のジェラートCUBE)
    await goProductList(page);
    await searchProduct(page, '彩のジェラートCUBE');
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：1件が該当しました');

    // Click the copy (複製) button on first row - opens a confirmation modal
    await page.locator('#form_bulk table tbody tr:first-child td.align-middle.pe-3 div div:nth-child(2) a[data-bs-toggle="modal"]').click();
    await page.waitForTimeout(500);

    // Confirm the modal by clicking the 複製 link
    await page.locator('.modal.show a.btn-ec-conversion').click();
    await page.waitForLoadState('load');

    // Verify success message on the product edit page
    await expect(page.locator('.alert-success')).toContainText('商品を複製しました');
  });

  test('product_一覧からの規格編集規格なし失敗', async ({ page }) => {
    // Search for 規格なし商品
    await goProductList(page);
    await searchProduct(page, '規格なし商品');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Click on product name to go to edit page
    await page.locator('#form_bulk table tbody tr:first-child td:nth-child(4) a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品登録');

    // Click 規格管理 link (navigates to class edit page)
    await page.locator('#standardConfig a[href*="product/class"]').click();
    // A confirmation modal appears: save before navigating?
    await page.waitForTimeout(500);
    const confirmModal = page.locator('#confirmFormChangeModal');
    if (await confirmModal.isVisible()) {
      await confirmModal.locator('a.btn-ec-conversion').click();
    }
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品規格登録');

    // Try to set class without selecting anything (submit empty)
    await page.locator('div.c-contentsArea form button[type="submit"]').click();
    await page.waitForTimeout(500);

    // Verify the class name 1 select is invalid (HTML5 required validation)
    const isInvalid = await page.locator('#product_class_matrix_class_name1').evaluate(
      (el: HTMLSelectElement) => !el.checkValidity()
    );
    expect(isInvalid).toBeTruthy();

    // The class table should not be visible (no class rows displayed)
    await expect(page.locator('#page_admin_product_product_class table')).not.toBeVisible();
  });

  test('product_一覧からの規格編集規格なし_', async ({ page }) => {
    // Search for 規格なし商品
    await goProductList(page);
    await searchProduct(page, '規格なし商品');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Click on product name to go to edit page
    await page.locator('#form_bulk table tbody tr:first-child td:nth-child(4) a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品登録');

    // Click 規格管理 link
    await page.locator('#standardConfig a[href*="product/class"]').click();
    await page.waitForTimeout(500);
    const confirmModal = page.locator('#confirmFormChangeModal');
    if (await confirmModal.isVisible()) {
      await confirmModal.locator('a.btn-ec-conversion').click();
    }
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品規格登録');

    // Select class name 1 = フレーバー (label includes extra text like "フレーバー (CUBE用味)")
    const className1Select = page.locator('#product_class_matrix_class_name1');
    const options = await className1Select.locator('option').all();
    for (const option of options) {
      const text = await option.textContent();
      if (text && text.includes('フレーバー')) {
        const value = await option.getAttribute('value');
        if (value) {
          await className1Select.selectOption(value);
          break;
        }
      }
    }

    // Submit class setting
    await page.locator('div.c-contentsArea form button[type="submit"]').click();
    await page.waitForLoadState('load');

    // Verify combinations are shown (e.g., "3件の組み合わせがあります")
    await expect(page.locator('div.c-contentsArea')).toContainText(/\d+件の組み合わせがあります/);

    // Select and fill each class row with stock unlimited and price
    const rowCount = await page.locator('table tbody tr').count();
    for (let i = 0; i < Math.min(rowCount, 3); i++) {
      // Check the "checked" checkbox
      await page.locator(`#product_class_matrix_product_classes_${i}_checked`).check();
      // Check stock unlimited
      await page.locator(`#product_class_matrix_product_classes_${i}_stock_unlimited`).check();
      // Fill sale price
      await page.locator(`#product_class_matrix_product_classes_${i}_price02`).fill('1000');
    }

    // Click save button
    await page.locator('button[name="product_class_matrix[save]"]').click();
    await page.waitForLoadState('load');

    // Verify success
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('product_一覧からの規格編集規格あり - EA0310-UC02-T01', async ({ page }) => {
    // Duplicate a product with classes to work with (avoid modifying original)
    await goProductList(page);
    await searchProduct(page, '彩のジェラートCUBE');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Click duplicate button on first row
    await page.locator('#form_bulk table tbody tr:first-child td.align-middle.pe-3 div div:nth-child(2) a[data-bs-toggle="modal"]').click();
    await page.waitForTimeout(500);
    await page.locator('.modal.show a.btn-ec-conversion').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('商品を複製しました');

    // We're now on the edit page of the duplicate — go directly to 規格管理
    await page.locator('#standardConfig a[href*="product/class"]').click();
    await page.waitForTimeout(500);
    const confirmModal = page.locator('#confirmFormChangeModal');
    if (await confirmModal.isVisible()) {
      await confirmModal.locator('a.btn-ec-conversion').click();
    }
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品規格登録');

    // Verify class table is visible (product has classes)
    await expect(page.locator('#page_admin_product_product_class table')).toBeVisible();

    // Click 規格初期化 button
    await page.locator('button[data-bs-target="#initializationConfirm"]').click();
    await page.waitForTimeout(500);

    // Confirm the initialization modal
    await page.locator('#initializationConfirm .modal-footer form button').click();
    await page.waitForLoadState('load');

    // Verify classes are reset
    await expect(page.locator('.alert-success')).toContainText('商品規格を初期化しました');
    await expect(page.locator('#page_admin_product_product_class table')).not.toBeVisible();
  });

  test.fixme('product_一覧からの規格編集_規格あり_重複在庫の修正 - EA0310-UC02-T03', async ({ page }) => {
    // Codeception marks this as incomplete:
    // "ローカルで通るが何故かGitHub Actionsでエラーになるためスキップ"
    // See: https://github.com/EC-CUBE/ec-cube/issues/6150
  });

  test('product_商品の一括削除_正常', async ({ page }) => {
    // Use a fixed timestamp prefix for all 5 products
    const timestamp = Date.now();
    const prefix = `一括削除用_${timestamp}`;
    for (let i = 1; i <= 5; i++) {
      const name = `${prefix}_${i}`;
      await page.goto(`/${adminRoute}/product/product/new`);
      await page.waitForLoadState('load');
      await page.locator('#admin_product_name').fill(name);
      await page.locator('#admin_product_class_price02').fill('1000');
      await page.locator('button.ladda-button[type="submit"]').click();
      await page.waitForLoadState('load');
      await expect(page.locator('.alert-success')).toContainText('保存しました');
    }

    // Search for the bulk delete products using common prefix
    await goProductList(page);
    await searchProduct(page, prefix);
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：5件が該当しました');

    // Select all
    await page.locator('#trigger_check_all').check();

    // Click delete button
    await page.locator('#form_bulk button.btn-ec-delete').click();
    await page.waitForSelector('#bulkDelete', { state: 'visible' });

    // Confirm delete
    await page.locator('#bulkDelete').click();

    // Wait for delete to complete
    await page.waitForSelector('#bulkDeleteDone', { state: 'visible', timeout: 30_000 });

    // Click done
    await page.locator('#bulkDeleteDone').click();
    await page.waitForLoadState('load');

    // Verify 0 results
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：0件が該当しました');
  });

  test('product_商品の一括削除_削除エラー - EA0302-UC05-T05', async ({ page }) => {
    test.setTimeout(120_000);
    const prefix = '一括削除エラーテスト';

    // Search for the bulk delete test products (created by setup-fixtures.php)
    await goProductList(page);
    await searchProduct(page, prefix);
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：5件が該当しました');

    // Select all
    await page.locator('#trigger_check_all').check();

    // Click delete button
    await page.locator('#form_bulk button.btn-ec-delete').click();
    await page.waitForSelector('#bulkDelete', { state: 'visible' });

    // Confirm delete
    await page.locator('#bulkDelete').click();

    // Wait for errors to appear (products with orders can't be deleted)
    await page.waitForSelector('#bulkErrors', { state: 'visible', timeout: 30_000 });
    await expect(page.locator('#bulkErrors')).toContainText('受注あり_1');
    await expect(page.locator('#bulkErrors')).toContainText('受注あり_2');

    // Click done
    await page.locator('#bulkDeleteDone').click();
    await page.waitForLoadState('load');

    // Only products with orders should remain
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：2件が該当しました');
    await expect(page.locator(searchResultList)).toContainText('受注あり');
    await expect(page.locator(searchResultList)).not.toContainText('受注なし');
  });

  test('product_規格表示順の変更 - EA0303-UC04-T01', async ({ page }) => {
    // Navigate to class name management
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('規格管理');

    // Helper: click sort button and wait for AJAX to complete.
    // Dismisses any Bootstrap tooltips first to avoid overlay interception.
    async function clickSortButton(selector: string) {
      await page.evaluate(() => {
        document.querySelectorAll('.tooltip').forEach(el => el.remove());
      });
      await page.locator(selector).click({ force: true });
      await page.waitForTimeout(200);
      await page.waitForFunction(() => !document.querySelector('.modal-backdrop'), {}, { timeout: 10_000 });
    }

    // (li:nth-child(1) = form, li:nth-child(2) = header, li:nth-child(3+) = data)
    const nameSelector = (n: number) => `ul.list-group > li:nth-child(${n}) > div > div.col.d-flex.align-items-center > a`;

    // Read initial names dynamically (data may vary between environments)
    const name3 = (await page.locator(nameSelector(3)).textContent())!.trim();
    const name4 = (await page.locator(nameSelector(4)).textContent())!.trim();

    // Move row 3 down (class_name uses a.down class, not a.action-down)
    await clickSortButton('ul.list-group > li:nth-child(3) a.down');

    // Verify swapped: [name4, name3]
    await expect(page.locator(nameSelector(3))).toContainText(name4);
    await expect(page.locator(nameSelector(4))).toContainText(name3);

    // Move row 4 up to restore: [name3, name4]
    await clickSortButton('ul.list-group > li:nth-child(4) a.up');

    // Verify restored
    await expect(page.locator(nameSelector(3))).toContainText(name3);
    await expect(page.locator(nameSelector(4))).toContainText(name4);
  });

  test('product_分類表示順の変更 - EA0311-UC01-T01', async ({ page }) => {
    // Navigate to class name management, then click into サイズ class categories
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');

    // Click サイズ (row 3) to go to class category management
    await page.locator('ul.list-group > li:nth-child(3) > div > div.col.d-flex.align-items-center > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('規格分類管理');

    // Helper: click sort button and wait for AJAX to complete.
    // Dismisses any Bootstrap tooltips first, then clicks the sort button via jQuery
    // to avoid tooltip overlay interception issues.
    async function clickSortButton(selector: string) {
      // Dismiss any visible tooltips by triggering their disposal
      await page.evaluate(() => {
        document.querySelectorAll('.tooltip').forEach(el => el.remove());
      });
      await page.locator(selector).click({ force: true });
      await page.waitForTimeout(200);
      await page.waitForFunction(() => !document.querySelector('.modal-backdrop'), {}, { timeout: 10_000 });
    }

    // (li:nth-child(1) = form, li:nth-child(2) = header, li:nth-child(3+) = data)
    const nameSelector = (n: number) => `ul.list-group > li:nth-child(${n}) > div > div.col.d-flex.align-items-center`;

    // Read initial names at positions 3, 4, 5
    const name3 = (await page.locator(nameSelector(3)).textContent())!.trim();
    const name4 = (await page.locator(nameSelector(4)).textContent())!.trim();
    const name5 = (await page.locator(nameSelector(5)).textContent())!.trim();

    // Move row 3 down: [name4, name3, name5]
    await clickSortButton('ul.list-group > li:nth-child(3) a.action-down');

    await expect(page.locator(nameSelector(3))).toContainText(name4);
    await expect(page.locator(nameSelector(4))).toContainText(name3);
    await expect(page.locator(nameSelector(5))).toContainText(name5);

    // Move row 4 down: [name4, name5, name3]
    await clickSortButton('ul.list-group > li:nth-child(4) a.action-down');

    await expect(page.locator(nameSelector(3))).toContainText(name4);
    await expect(page.locator(nameSelector(4))).toContainText(name5);
    await expect(page.locator(nameSelector(5))).toContainText(name3);

    // Move row 5 up: [name4, name3, name5]
    await clickSortButton('ul.list-group > li:nth-child(5) a.action-up');

    await expect(page.locator(nameSelector(3))).toContainText(name4);
    await expect(page.locator(nameSelector(4))).toContainText(name3);
    await expect(page.locator(nameSelector(5))).toContainText(name5);

    // Move row 4 up to restore original order: [name3, name4, name5]
    await clickSortButton('ul.list-group > li:nth-child(4) a.action-up');

    await expect(page.locator(nameSelector(3))).toContainText(name3);
    await expect(page.locator(nameSelector(4))).toContainText(name4);
    await expect(page.locator(nameSelector(5))).toContainText(name5);
  });

  test('product_カテゴリ表示順の変更 - EA0305-UC03-T01', async ({ page }) => {
    // Navigate to category management
    await page.goto(`/${adminRoute}/product/category`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('カテゴリ管理');

    // Helper: click sort button and wait for AJAX to complete.
    // Dismisses any Bootstrap tooltips first, then clicks the sort button via jQuery
    // to avoid tooltip overlay interception issues.
    async function clickSortButton(selector: string) {
      // Dismiss any visible tooltips by triggering their disposal
      await page.evaluate(() => {
        document.querySelectorAll('.tooltip').forEach(el => el.remove());
      });
      await page.locator(selector).click({ force: true });
      await page.waitForTimeout(200);
      await page.waitForFunction(() => !document.querySelector('.modal-backdrop'), {}, { timeout: 10_000 });
    }

    // Category list selector
    // (li:nth-child(1) = form, li:nth-child(2) = header, li:nth-child(3+) = data)
    const nameSelector = (n: number) =>
      `div.c-contentsArea__primaryCol ul.list-group > li:nth-child(${n}) > div > div.col.d-flex.align-items-center > a`;

    // Read initial names at positions 3, 4, 5
    const name3 = (await page.locator(nameSelector(3)).textContent())!.trim();
    const name4 = (await page.locator(nameSelector(4)).textContent())!.trim();
    const name5 = (await page.locator(nameSelector(5)).textContent())!.trim();

    // Move row 3 down: [name4, name3, name5]
    await clickSortButton(`div.c-contentsArea__primaryCol ul.list-group > li:nth-child(3) a.action-down`);

    await expect(page.locator(nameSelector(3))).toContainText(name4);
    await expect(page.locator(nameSelector(4))).toContainText(name3);
    await expect(page.locator(nameSelector(5))).toContainText(name5);

    // Move row 4 down: [name4, name5, name3]
    await clickSortButton(`div.c-contentsArea__primaryCol ul.list-group > li:nth-child(4) a.action-down`);

    await expect(page.locator(nameSelector(3))).toContainText(name4);
    await expect(page.locator(nameSelector(4))).toContainText(name5);
    await expect(page.locator(nameSelector(5))).toContainText(name3);

    // Move row 5 up: [name4, name3, name5]
    await clickSortButton(`div.c-contentsArea__primaryCol ul.list-group > li:nth-child(5) a.action-up`);

    await expect(page.locator(nameSelector(3))).toContainText(name4);
    await expect(page.locator(nameSelector(4))).toContainText(name3);
    await expect(page.locator(nameSelector(5))).toContainText(name5);

    // Move row 4 up to restore original order: [name3, name4, name5]
    await clickSortButton(`div.c-contentsArea__primaryCol ul.list-group > li:nth-child(4) a.action-up`);

    await expect(page.locator(nameSelector(3))).toContainText(name3);
    await expect(page.locator(nameSelector(4))).toContainText(name4);
    await expect(page.locator(nameSelector(5))).toContainText(name5);
  });

  test('product_新製品はタグを持っています - EA0302-UC01-T05', async ({ page }) => {
    // Create a product with tags
    await page.goto(`/${adminRoute}/product/product/new`);
    await page.waitForLoadState('load');
    await page.locator('#admin_product_name').fill('タグテスト商品');
    await page.locator('#admin_product_class_price02').fill('50000');

    // Open tag list and select tags
    await page.locator('div[href="#allTags"] > a').click();
    await page.waitForTimeout(500);

    // Select tags (2nd, 3rd, 4th in the allTags list)
    await page.locator('#allTags > div:nth-child(2) button').click();
    await page.locator('#allTags > div:nth-child(3) button').click();
    await page.locator('#allTags > div:nth-child(4) button').click();

    // Submit
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Verify tags are displayed on the edit page (tag buttons in #tag section)
    await expect(page.locator('#tag > div > div:nth-child(1) > button')).toBeVisible();
    await expect(page.locator('#tag > div > div:nth-child(2) > button')).toBeVisible();
    await expect(page.locator('#tag > div > div:nth-child(3) > button')).toBeVisible();
  });

  test('product_商品編集からの商品確認_公開 - EA0310-UC05-T02', async ({ page }) => {
    // Search for a known product
    await goProductList(page);
    await searchProduct(page, 'チェリーアイスサンド');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Click on product name to go to edit page
    await page.locator('#form_bulk table tbody tr:first-child td:nth-child(4) a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品登録');

    // Set status to public and save
    await page.locator('#admin_product_Status').selectOption('公開');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Click preview link (opens in new tab)
    const [newPage] = await Promise.all([
      page.context().waitForEvent('page'),
      page.locator('#preview a[target="_blank"]').click(),
    ]);
    await newPage.waitForLoadState('load');
    expect(newPage.url()).toContain('/products/detail/');
    await newPage.close();
  });

  test('product_商品編集からの商品確認_非公開 - EA0310-UC05-T03', async ({ page }) => {
    // Search for a known product
    await goProductList(page);
    await searchProduct(page, 'チェリーアイスサンド');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：\d+件が該当しました/);

    // Click on product name to go to edit page
    await page.locator('#form_bulk table tbody tr:first-child td:nth-child(4) a').click();
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品登録');

    // Set status to non-public and save
    await page.locator('#admin_product_Status').selectOption('非公開');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Click preview link (opens in new tab)
    const [newPage] = await Promise.all([
      page.context().waitForEvent('page'),
      page.locator('#preview a[target="_blank"]').click(),
    ]);
    await newPage.waitForLoadState('load');
    expect(newPage.url()).toContain('/products/detail/');
    await newPage.close();

    // Restore to public status
    await page.locator('#admin_product_Status').selectOption('公開');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('product_詳細検索_タグ - EA0312-UC01-T01', async ({ page }) => {
    // Create a unique product name for the tag search test
    const uniqueName = `タグ検索テスト_${Date.now()}`;
    await page.goto(`/${adminRoute}/product/product/new`);
    await page.waitForLoadState('load');
    await page.locator('#admin_product_name').fill(uniqueName);
    await page.locator('#admin_product_class_price02').fill('1000');
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Search by tag -- initially, this product has no tags, so tag-filtered search should return 0
    await goProductList(page);
    await page.locator('#admin_search_product_id').fill(uniqueName);
    // Open advanced search
    await page.locator('#search_form .c-outsideBlock__contents a span, #search_form a.d-inline-block').first().click();
    await page.waitForTimeout(500);
    // Select the first tag in the dropdown
    await page.locator('#admin_search_product_tag_id').selectOption({ index: 1 });
    await page.locator(searchBtn).click();
    await page.waitForLoadState('load');
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：0件が該当しました');

    // Now add tags to the product via edit page
    await goProductList(page);
    await searchProduct(page, uniqueName);
    await page.locator('#form_bulk table tbody tr:first-child td:nth-child(4) a').click();
    await page.waitForLoadState('load');

    // Add tags
    await page.locator('div[href="#allTags"] > a').click();
    await page.waitForTimeout(500);
    await page.locator('#allTags > div:nth-child(2) button').click();
    await page.locator('#allTags > div:nth-child(3) button').click();
    await page.locator('#allTags > div:nth-child(4) button').click();
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Search by tag again -- now should return 1
    await goProductList(page);
    await page.locator('#admin_search_product_id').fill(uniqueName);
    await page.locator('#search_form .c-outsideBlock__contents a span, #search_form a.d-inline-block').first().click();
    await page.waitForTimeout(500);
    await page.locator('#admin_search_product_tag_id').selectOption({ index: 1 });
    await page.locator(searchBtn).click();
    await page.waitForLoadState('load');
    await expect(page.locator(searchResultMsg)).toContainText('検索結果：1件が該当しました');
  });

  test('product_商品CSV登録 - EA0306-UC01-T01', async ({ page }) => {
    test.setTimeout(120_000);

    // Verify that upload products don't exist yet
    await goProductList(page);
    await searchProduct(page, 'アップロード商品');
    // May show 0 results or results from a prior run -- just proceed with upload

    // Navigate to product CSV upload page
    await page.goto(`/${adminRoute}/product/product_csv_upload`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品CSV登録');

    // Upload the product CSV file
    const csvPath = path.join(__dirname, '..', 'fixtures', 'product.csv');
    await page.locator('#admin_csv_import_import_file').setInputFiles(csvPath);

    // Enable the upload button (since setInputFiles doesn't fire the change event properly)
    await page.evaluate(() => {
      (document.getElementById('upload-button') as HTMLButtonElement).disabled = false;
    });

    // Click upload button to open the modal
    await page.locator('#upload-button').click();
    await page.waitForTimeout(1000);

    // Click the import button inside the modal
    await page.locator('#importCsv').click();

    // Wait for upload to complete (the modal body text changes)
    await page.waitForSelector('#importCsvDone:not([style*="display: none"])', { timeout: 60_000 });

    // Verify success message
    await expect(page.locator('#importCsvModal .modal-body p')).toContainText('CSVファイルをアップロードしました');

    // Close the modal
    await page.locator('#importCsvDone').click();
    await page.waitForLoadState('load');

    // Verify the uploaded products appear in search
    await goProductList(page);
    await searchProduct(page, 'アップロード商品');
    await expect(page.locator(searchResultMsg)).toContainText(/検索結果：[3-9]\d*件が該当しました/);

    // Test upload failure: upload category CSV as product CSV (format mismatch)
    await page.goto(`/${adminRoute}/product/product_csv_upload`);
    await page.waitForLoadState('load');

    const categoryCsvPath = path.join(__dirname, '..', 'fixtures', 'category.csv');
    await page.locator('#admin_csv_import_import_file').setInputFiles(categoryCsvPath);
    await page.evaluate(() => {
      (document.getElementById('upload-button') as HTMLButtonElement).disabled = false;
    });
    await page.locator('#upload-button').click();
    await page.waitForTimeout(1000);
    await page.locator('#importCsv').click();
    await page.waitForTimeout(3000);

    // Verify error message about format mismatch
    await expect(page.locator('#bulkMessages')).toContainText('CSVのフォーマットが一致しません');
  });

  test('product_カテゴリCSV登録 - EA0307-UC01-T01', async ({ page }) => {
    test.setTimeout(60_000);

    // Navigate to category CSV upload page
    await page.goto(`/${adminRoute}/product/category_csv_upload`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('カテゴリCSV登録');

    // Upload the category CSV file
    const csvPath = path.join(__dirname, '..', 'fixtures', 'category.csv');
    await page.locator('#admin_csv_import_import_file').setInputFiles(csvPath);
    await page.locator('#upload-button').click();
    await page.waitForLoadState('load');

    // Verify success message
    await expect(page.locator('.c-container .c-contentsArea .alert-success')).toContainText('CSVファイルをアップロードしました');

    // Verify uploaded categories appear
    await page.goto(`/${adminRoute}/product/category`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea')).toContainText('アップロードカテゴリ1');

    // Test upload failure: upload product CSV as category CSV
    await page.goto(`/${adminRoute}/product/category_csv_upload`);
    await page.waitForLoadState('load');
    const productCsvPath = path.join(__dirname, '..', 'fixtures', 'product.csv');
    await page.locator('#admin_csv_import_import_file').setInputFiles(productCsvPath);
    await page.locator('#upload-button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#upload-form')).toContainText('CSVのフォーマットが一致しません');
  });

  test('product_規格CSV登録 - EA0307-UC02-T01', async ({ page }) => {
    test.setTimeout(60_000);

    // Navigate to class name CSV upload page
    await page.goto(`/${adminRoute}/product/class_name_csv_upload`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('規格CSV登録');

    // Upload the class name CSV file
    const csvPath = path.join(__dirname, '..', 'fixtures', 'class_name.csv');
    await page.locator('#admin_csv_import_import_file').setInputFiles(csvPath);
    await page.locator('#upload-button').click();
    await page.waitForLoadState('load');

    // Verify success message
    await expect(page.locator('.c-container .c-contentsArea .alert-success')).toContainText('CSVファイルをアップロードしました');

    // Verify uploaded class names appear
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea')).toContainText('アップロード規格1');

    // Test upload failure: upload product CSV as class name CSV
    await page.goto(`/${adminRoute}/product/class_name_csv_upload`);
    await page.waitForLoadState('load');
    const productCsvPath = path.join(__dirname, '..', 'fixtures', 'product.csv');
    await page.locator('#admin_csv_import_import_file').setInputFiles(productCsvPath);
    await page.locator('#upload-button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#upload-form')).toContainText('CSVのフォーマットが一致しません');
  });

  test('product_規格分類CSV登録 - EA0307-UC03-T01', async ({ page }) => {
    test.setTimeout(60_000);

    // Navigate to class category CSV upload page
    await page.goto(`/${adminRoute}/product/class_category_csv_upload`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('規格分類CSV登録');

    // Upload the class category CSV file
    const csvPath = path.join(__dirname, '..', 'fixtures', 'class_category.csv');
    await page.locator('#admin_csv_import_import_file').setInputFiles(csvPath);
    await page.locator('#upload-button').click();
    await page.waitForLoadState('load');

    // Verify success message
    await expect(page.locator('.c-container .c-contentsArea .alert-success')).toContainText('CSVファイルをアップロードしました');

    // Navigate to class name management, then click into the class with ID=1 (フレーバー) to verify
    // class_category.csv references 規格ID=1
    await page.goto(`/${adminRoute}/product/class_name`);
    await page.waitForLoadState('load');

    // Find and click on フレーバー to see its categories
    const classListItems = page.locator('ul.list-group > li');
    const count = await classListItems.count();
    for (let i = 2; i < count; i++) {
      const nameEl = classListItems.nth(i).locator('div > div.col.d-flex.align-items-center > a');
      const txt = await nameEl.textContent();
      if (txt?.includes('フレーバー')) {
        await nameEl.click();
        break;
      }
    }
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea')).toContainText('アップロード規格分類1');

    // Test upload failure: upload product CSV as class category CSV
    await page.goto(`/${adminRoute}/product/class_category_csv_upload`);
    await page.waitForLoadState('load');
    const productCsvPath = path.join(__dirname, '..', 'fixtures', 'product.csv');
    await page.locator('#admin_csv_import_import_file').setInputFiles(productCsvPath);
    await page.locator('#upload-button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#upload-form')).toContainText('CSVのフォーマットが一致しません');
  });

  test('product_一覧からの規格編集_規格あり_規格登録 - EA0310-UC02-T02', async ({ page }) => {
    // Go directly to product 1's class edit page (guaranteed to have classes)
    await page.goto(`/${adminRoute}/product/product/class/1`);
    await page.waitForLoadState('load');
    await expect(page.locator(pageTitle)).toContainText('商品規格登録');

    // The product already has classes, so the class table should be visible
    // Just click save (register) to confirm it works
    await page.locator('button[name="product_class_matrix[save]"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });
});
