import { type Page } from '@playwright/test';
import { type DbClient } from '../../helpers/db-client';
import { type PluginTestConfig } from '../../fixtures/plugin-test';
import { LocalPlugin } from '../local-plugin';

export class BoomerangLocal extends LocalPlugin {
  constructor(page: Page, db: DbClient, config: PluginTestConfig) {
    super(page, db, config, 'Boomerang');
    this.tables.push('dtb_bar');
    this.columns.push('dtb_cart.is_boomerang');
    this.traits.set('Plugin\\Boomerang\\Entity\\CartTrait', 'src/Eccube/Entity/Cart');
  }

  static async start(page: Page, db: DbClient, config: PluginTestConfig): Promise<BoomerangLocal> {
    return new BoomerangLocal(page, db, config);
  }
}
