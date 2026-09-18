import { type Page } from '@playwright/test';
import { type DbClient } from '../../helpers/db-client';
import { type PluginTestConfig } from '../../fixtures/plugin-test';
import { StorePlugin } from '../store-plugin';

export class Boomerang10Store extends StorePlugin {
  constructor(page: Page, db: DbClient, config: PluginTestConfig, dependency?: StorePlugin) {
    super(page, db, config, 'Boomerang10', dependency);
    this.columns.push('dtb_bar.mail');
  }

  static async start(page: Page, db: DbClient, config: PluginTestConfig, dependency?: StorePlugin): Promise<Boomerang10Store> {
    const plugin = new Boomerang10Store(page, db, config, dependency);
    await plugin.init();
    return plugin;
  }
}
