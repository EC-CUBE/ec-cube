import { type Page } from '@playwright/test';
import { type DbClient } from '../../helpers/db-client';
import { type PluginTestConfig } from '../../fixtures/plugin-test';
import { LocalPlugin } from '../local-plugin';

/**
 * マスタデータのEntity(Entity/Master配下)をtraitで拡張するローカルプラグイン。
 * Issue #5400 / #6273 の回帰テスト用。
 * Codeception の MasterEntityExtension_Local に相当。
 */
export class MasterEntityExtensionLocal extends LocalPlugin {
  constructor(page: Page, db: DbClient, config: PluginTestConfig) {
    super(page, db, config, 'MasterEntityExtension');
    // Entity/Master 配下のEntityを拡張するtrait。
    // 有効化でこのtraitがproxyに注入され、mtb_device_type がプルダウンに残ることを検証する。
    this.traits.set('Plugin\\MasterEntityExtension\\Entity\\DeviceTypeTrait', 'src/Eccube/Entity/Master/DeviceType');
  }

  static async start(page: Page, db: DbClient, config: PluginTestConfig): Promise<MasterEntityExtensionLocal> {
    return new MasterEntityExtensionLocal(page, db, config);
  }
}
