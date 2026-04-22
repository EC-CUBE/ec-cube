import { type Page, expect } from '@playwright/test';
import { type DbClient } from '../../helpers/db-client';
import { type PluginTestConfig } from '../../fixtures/plugin-test';
import { StorePlugin } from '../store-plugin';

export class HorizonStore extends StorePlugin {
  constructor(page: Page, db: DbClient, config: PluginTestConfig) {
    super(page, db, config, 'Horizon');
    this.tables.push('dtb_dash');
    this.columns.push('dtb_cart.is_horizon', 'dtb_cart.dash_id');
    this.traits.set('Plugin\\Horizon\\Entity\\CartTrait', 'src/Eccube/Entity/Cart');
  }

  static async start(page: Page, db: DbClient, config: PluginTestConfig): Promise<HorizonStore> {
    const plugin = new HorizonStore(page, db, config);
    await plugin.init();
    return plugin;
  }

  async アップデート(): Promise<this> {
    this.columns.push('dtb_dash.new_column');
    await super.アップデート();
    return this;
  }

  async 依存されているのが有効なのに無効化(): Promise<this> {
    await this.managePage.ストアプラグイン_無効化(
      this.code,
      '「ホライゾン」を無効にする前に、「エンペラー」を無効にしてください。'
    );

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin!.initialized, '初期化されているはず').toBe(true);
    expect(plugin!.enabled, '有効化されているはず').toBe(true);

    return this;
  }

  async 依存されているのが削除されていないのに削除(): Promise<this> {
    await this.managePage.ストアプラグイン_削除(
      this.code,
      '「エンペラー」が「ホライゾン」に依存しているため削除できません。'
    );

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin, '削除されていない').not.toBeNull();

    return this;
  }
}
