import { test, expect } from '@playwright/test';

// Desktop header search form (first form, not the mobile drawer duplicate)
const headerSearchForm = '.ec-layoutRole__header .ec-headerSearch';

test.describe('Front Top Page (EF01)', () => {

  test('EF0101-UC01-T01 TOPページ 初期表示', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('load');

    const searchArea = page.locator(headerSearchForm).first();

    // カテゴリ選択ボックスが表示されている
    const categorySelect = searchArea.locator('select.category_id');
    await expect(categorySelect).toBeVisible();

    // 「全ての商品」がデフォルトで表示されている
    const defaultOption = categorySelect.locator('option').first();
    await expect(defaultOption).toHaveText('全ての商品');

    // カテゴリオプションが存在する
    const options = categorySelect.locator('option');
    const count = await options.count();
    expect(count).toBeGreaterThan(1);

    // キーワード検索入力欄が表示されている
    await expect(searchArea.locator('input.search-name')).toBeVisible();

    // 検索ボタンが表示されている
    await expect(searchArea.locator('button.ec-headerSearch__keywordBtn')).toBeVisible();

    // 新着情報セクションが表示されている
    await expect(page.locator('.ec-newsRole')).toBeVisible();
  });

  test('EF0101-UC01-T02 TOPページ 新着情報', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('load');

    // Check if news items exist
    const newsItems = page.locator('.ec-newsRole__newsItem');
    const newsCount = await newsItems.count();

    if (newsCount > 0) {
      // Click the first news heading to expand it
      const firstHeading = page.locator('.ec-newsRole__newsHeading').first();
      await firstHeading.click();
      await page.waitForTimeout(500);

      // Verify that the description expands and is visible
      const description = page.locator('.ec-newsRole__newsDescription').first();
      await expect(description).toBeVisible();

      // Verify description has content
      const descText = await description.textContent();
      expect(descText!.trim().length).toBeGreaterThan(0);
    }
    // If no news exists, skip gracefully (test still passes)
  });

  test('EF0101-UC02-T01 TOPページ カテゴリ検索', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('load');

    // ヘッダーナビからカテゴリを選択（「新入荷」）
    const navItem = page.locator('.ec-itemNav__nav li a', { hasText: '新入荷' }).first();
    await navItem.click();
    await page.waitForLoadState('load');

    // 商品一覧ページに遷移する
    await expect(page).toHaveURL(/products\/list\?category_id=/);

    // パンくずにカテゴリ名が表示される
    await expect(page.locator('.ec-topicpath')).toContainText('新入荷');

    // 商品が表示される
    await expect(page.locator('.ec-shelfGrid')).toBeVisible();
  });

  test('EF0101-UC03-T01 TOPページ 全件検索', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('load');

    const searchArea = page.locator(headerSearchForm).first();

    // キーワード未入力で検索ボタンを押下する
    await searchArea.locator('button.ec-headerSearch__keywordBtn').click();
    await page.waitForLoadState('load');

    // 商品一覧に遷移する
    await expect(page).toHaveURL(/products\/list/);

    // 「全て」がトピックパスに表示される
    await expect(page.locator('.ec-topicpath')).toContainText('全て');

    // 商品が表示される
    const products = page.locator('ul.ec-shelfGrid li.ec-shelfGrid__item');
    await expect(products.first()).toBeVisible();
    const productCount = await products.count();
    expect(productCount).toBeGreaterThanOrEqual(1);
  });

  test('EF0101-UC03-T02 TOPページ カテゴリ絞込検索', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('load');

    const searchArea = page.locator(headerSearchForm).first();

    // カテゴリを選択する
    await searchArea.locator('select.category_id').selectOption({ label: '新入荷' });

    // 検索ボタンを押下する
    await searchArea.locator('button.ec-headerSearch__keywordBtn').click();
    await page.waitForLoadState('load');

    // パンくずに選択カテゴリが表示される
    await expect(page.locator('.ec-topicpath')).toContainText('新入荷');

    // 該当カテゴリの商品が表示される
    await expect(page.locator('.ec-shelfGrid')).toBeVisible();
  });

  test('EF0101-UC03-T02 TOPページ キーワード絞込検索', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('load');

    const searchArea = page.locator(headerSearchForm).first();

    // キーワードを入力する
    await searchArea.locator('input.search-name').fill('ジェラート');

    // 検索ボタンを押下する
    await searchArea.locator('button.ec-headerSearch__keywordBtn').click();
    await page.waitForLoadState('load');

    // パンくずにキーワードが表示される
    await expect(page.locator('.ec-topicpath')).toContainText('ジェラート');

    // 該当商品が表示される
    await expect(page.locator('.ec-shelfGrid')).toContainText('彩のジェラートCUBE');
  });
});
