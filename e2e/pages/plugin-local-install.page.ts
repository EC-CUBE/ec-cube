import { type Page, expect } from '@playwright/test';
import { PluginManagePage } from './plugin-manage.page';

/**
 * 独自プラグインのアップロードページ
 * Codeception の PluginLocalInstallPage に相当
 */
export class PluginLocalInstallPage {
  constructor(private readonly page: Page) {}

  static async go(page: Page, adminRoute = 'admin'): Promise<PluginLocalInstallPage> {
    await page.goto(`/${adminRoute}/store/plugin/install`);
    await expect(page.locator('.c-pageTitle')).toContainText('独自プラグインのアップロード', { timeout: 30_000 });
    return new PluginLocalInstallPage(page);
  }

  async アップロード(tgzPath: string): Promise<PluginManagePage> {
    await this.page.locator('#plugin_local_install_plugin_archive').setInputFiles(tgzPath);
    await this.page.locator('#upload-form button[type="submit"]').click();
    await this.page.waitForLoadState('load');
    return PluginManagePage.at(this.page);
  }
}
