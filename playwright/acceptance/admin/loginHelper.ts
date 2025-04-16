import { Page, expect } from '@playwright/test';

/**
 * 管理画面にログインする共通関数
 */
export async function loginAsAdmin(page: Page) {
    await page.goto('http://127.0.0.1:8080/admin/');
    await page.fill('#login_id', 'admin');      // 必要に応じて環境変数化可
    await page.fill('#password', 'password');   // 同上
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/admin\/$/); // ログイン成功判定
}
