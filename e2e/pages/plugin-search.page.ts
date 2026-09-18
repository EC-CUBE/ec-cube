import { type Page, expect } from '@playwright/test';
import { PluginStoreConfirmPage } from './plugin-manage.page';

/**
 * プラグインを探すページ (オーナーズストア検索)
 * Codeception の PluginSearchPage に相当
 */
export class PluginSearchPage {
  constructor(private readonly page: Page) {}

  static async go(page: Page, adminRoute = 'admin'): Promise<PluginSearchPage> {
    await page.goto(`/${adminRoute}/store/plugin/api/search`);
    await expect(page.locator('.c-pageTitle')).toContainText('プラグインを探す', { timeout: 30_000 });
    return new PluginSearchPage(page);
  }

  async 入手する(pluginCode: string): Promise<PluginStoreConfirmPage> {
    const row = this.page.locator('#plugin-list .row.border-bt').filter({
      has: this.page.locator(`a[data-code="${pluginCode}"]`),
    });
    await row.getByRole('link', { name: '入手する' }).click();
    return PluginStoreConfirmPage.at(this.page);
  }
}
