import { type Page, expect } from '@playwright/test';

/**
 * キャッシュ管理ページ
 * Codeception の CacheManagePage に相当
 */
export class CacheManagePage {
  constructor(private readonly page: Page) {}

  static async go(page: Page, adminRoute = 'admin'): Promise<CacheManagePage> {
    await page.goto(`/${adminRoute}/content/cache`);
    await expect(page.locator('.c-pageTitle')).toContainText('キャッシュ管理', { timeout: 30_000 });
    return new CacheManagePage(page);
  }

  async キャッシュ削除(): Promise<void> {
    await this.page.locator('button[type="submit"]').click();
    await this.page.waitForLoadState('load');
  }
}
