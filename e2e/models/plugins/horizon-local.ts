import { type Page } from '@playwright/test';
import { type DbClient } from '../../helpers/db-client';
import { type PluginTestConfig } from '../../fixtures/plugin-test';
import { LocalPlugin } from '../local-plugin';

export class HorizonLocal extends LocalPlugin {
  constructor(page: Page, db: DbClient, config: PluginTestConfig) {
    super(page, db, config, 'Horizon');
    this.tables.push('dtb_dash');
    this.columns.push('dtb_cart.is_horizon', 'dtb_cart.dash_id');
    this.traits.set('Plugin\\Horizon\\Entity\\CartTrait', 'src/Eccube/Entity/Cart');
  }

  static async start(page: Page, db: DbClient, config: PluginTestConfig): Promise<HorizonLocal> {
    return new HorizonLocal(page, db, config);
  }

  async アップデート(): Promise<this> {
    this.columns.push('dtb_dash.new_column');
    await super.アップデート();
    return this;
  }
}
