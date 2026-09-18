import { type Page } from '@playwright/test';
import { type DbClient } from '../../helpers/db-client';
import { type PluginTestConfig } from '../../fixtures/plugin-test';
import { StorePlugin } from '../store-plugin';

export class BoomerangStore extends StorePlugin {
  constructor(page: Page, db: DbClient, config: PluginTestConfig) {
    super(page, db, config, 'Boomerang');
    this.tables.push('dtb_bar');
    this.columns.push('dtb_cart.is_boomerang', 'dtb_cart.bar_id');
    this.traits.set('Plugin\\Boomerang\\Entity\\CartTrait', 'src/Eccube/Entity/Cart');
  }

  static async start(page: Page, db: DbClient, config: PluginTestConfig): Promise<BoomerangStore> {
    const plugin = new BoomerangStore(page, db, config);
    await plugin.init();
    return plugin;
  }

  async カート一覧(): Promise<void> {
    await this.page.goto('/boomerang');
  }

  async カート作成(): Promise<this> {
    await this.page.goto('/boomerang/new');
    await this.page.waitForURL(/\/boomerang$/);
    return this;
  }
}
