import { type Page } from '@playwright/test';
import { type DbClient } from '../../helpers/db-client';
import { type PluginTestConfig } from '../../fixtures/plugin-test';
import { StorePlugin } from '../store-plugin';

export class BundleStore extends StorePlugin {
  constructor(page: Page, db: DbClient, config: PluginTestConfig) {
    super(page, db, config, 'Bundle');
    this.tables.push('oauth2_client', 'oauth2_refresh_token', 'oauth2_access_token', 'oauth2_authorization_code');
  }

  static async start(page: Page, db: DbClient, config: PluginTestConfig): Promise<BundleStore> {
    const plugin = new BundleStore(page, db, config);
    await plugin.init();
    return plugin;
  }
}
