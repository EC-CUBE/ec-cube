import { type Page, expect } from '@playwright/test';
import { type DbClient } from '../../helpers/db-client';
import { type PluginTestConfig } from '../../fixtures/plugin-test';
import { StorePlugin } from '../store-plugin';

export class EmperorStore extends StorePlugin {
  constructor(page: Page, db: DbClient, config: PluginTestConfig, dependency?: StorePlugin) {
    super(page, db, config, 'Emperor', dependency);
    this.tables.push('dtb_foo');
    this.columns.push('dtb_cart.foo_id');
    this.traits.set('Plugin\\Emperor\\Entity\\CartTrait', 'src/Eccube/Entity/Cart');
  }

  static async start(page: Page, db: DbClient, config: PluginTestConfig, dependency?: StorePlugin): Promise<EmperorStore> {
    const plugin = new EmperorStore(page, db, config, dependency);
    await plugin.init();
    return plugin;
  }

  async アップデート(): Promise<this> {
    this.tables = ['dtb_bar'];
    this.columns = ['dtb_cart.bar_id'];
    this.traits.clear();
    this.traits.set('Plugin\\Emperor\\Entity\\Cart2Trait', 'src/Eccube/Entity/Cart');

    await super.アップデート();
    return this;
  }

  async 依存より先に有効化(): Promise<this> {
    await this.managePage.ストアプラグイン_有効化(
      this.code,
      '「ホライゾン」を先に有効化してください。'
    );

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin!.initialized, '初期化されていないはず').toBe(false);
    expect(plugin!.enabled, '有効化されていないはず').toBe(false);

    return this;
  }
}
