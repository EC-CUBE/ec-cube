import {expect, test} from '@playwright/test';
import {loginAsAdmin} from './loginHelper';

test.describe('EA01TopCest: @admin @admin01 @toppage @ea1', () => {
    // 事前にログイン
    test.beforeEach(async ({page}) => {
        await loginAsAdmin(page);
    });
    test.describe('EA0101-UC01-T01 TOPページ 初期表示', () => {
        test('top_001: 表示と遷移確認', async ({page, context}) => {
            // 初期表示要素確認
            await expect(page.locator('text=新規受付')).toBeVisible();
            await expect(page.locator('text=お知らせ')).toBeVisible();
            await expect(page.locator('text=売上状況')).toBeVisible();
            await expect(page.locator('text=ショップ状況')).toBeVisible();
            await expect(page.locator('text=おすすめのプラグイン')).toBeVisible();

            // 新規受付 → 受注一覧へ
            await page.click('text=新規受付');
            await expect(page.getByRole('heading', {name: '受注一覧'})).toBeVisible();
            await page.goto('/admin/');

            // 新規受付件数の一致確認
            const visibleCountText = await page
                .getByRole('link', {name: '新規受付'})
                .locator('span.h4')
                .innerText();

            const visibleCount = parseInt(visibleCountText, 10);

            // APIから件数を取得
            const response = await page.request.get('/admin/api/test/orders/count?order_status=1');
            const json = await response.json();
            const apiCount = json.count;

            // 一致することを確認
            expect(visibleCount).toBe(apiCount);

            // 新規受付 → 検索条件チェック
            await page.click('text=新規受付');
            await expect(page.getByRole('heading', {name: '受注一覧'})).toBeVisible();
            await expect(page.locator('#admin_search_order_status_1')).toBeChecked(); // 新規受付
            await expect(page.locator('#admin_search_order_status_6')).not.toBeChecked(); // 入金済み
            await expect(page.locator('#admin_search_order_status_4')).not.toBeChecked(); // 対応中
            await page.goto('/admin/');

            // 入金済みクリック → 検索条件確認
            await page.click('text=入金済み');
            await expect(page.getByRole('heading', {name: '受注一覧'})).toBeVisible();
            await expect(page.locator('#admin_search_order_status_6')).toBeChecked(); // 入金済み
            await expect(page.locator('#admin_search_order_status_1')).not.toBeChecked(); // 新規受付
            await expect(page.locator('#admin_search_order_status_4')).not.toBeChecked(); // 対応中
            await page.goto('/admin/');

            // 対応中クリック → 検索条件確認
            await page.click('text=対応中');
            await expect(page.getByRole('heading', {name: '受注一覧'})).toBeVisible();
            await expect(page.locator('#admin_search_order_status_4')).toBeChecked(); // // 対応中
            await expect(page.locator('#admin_search_order_status_1')).not.toBeChecked(); // 新規受付
            await expect(page.locator('#admin_search_order_status_6')).not.toBeChecked(); // 入金済み
            await page.goto('/admin/');

            // iframe内のお知らせリンククリック → 新しいウィンドウに遷移
            const frame = page.frameLocator('iframe[name="information"]');
            const link = frame.locator('.news_area .link_list .tableish a').first();
            const currentUrl = page.url();

            const [newPage] = await Promise.all([context.waitForEvent('page'), link.click()]);

            await newPage.waitForLoadState();
            expect(newPage.url()).not.toContain(currentUrl);
            await newPage.close();
            await page.bringToFront(); // メイン画面に戻る

            // 在庫切れ商品 → 商品一覧 + チェック
            await page.click('text=在庫切れ商品');
            await expect(page.getByRole('heading', {name: '商品一覧'})).toBeVisible();
            await expect(page.locator('#admin_search_product_stock_1')).toBeChecked();
            await expect(page.locator('#admin_search_product_stock_0')).not.toBeChecked();
            await page.goto('/admin/');

            // 取扱商品数 → 商品一覧
            await page.click('text=取扱商品数');
            await expect(page.getByRole('heading', {name: '商品一覧'})).toBeVisible();
            await page.goto('/admin/');

            // 会員数 → 会員一覧 + チェック
            await page.click('text=会員数');
            await expect(page.getByRole('heading', {name: '会員一覧'})).toBeVisible();
            await expect(page.locator('#admin_search_customer_customer_status_2')).toBeChecked(); // 本会員
            await expect(page.locator('#admin_search_customer_customer_status_1')).not.toBeChecked(); // 仮会員
            await expect(page.locator('#admin_search_customer_customer_status_3')).not.toBeChecked(); // 退会
        });
    });
});
