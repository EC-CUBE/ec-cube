import { type Page, expect } from '@playwright/test';
import { type DbClient } from '../helpers/db-client';
import { type PluginTestConfig } from '../fixtures/plugin-test';
import { compressPlugin } from '../helpers/tar-helper';
import { AbstractPlugin } from './abstract-plugin';
import { PluginLocalInstallPage } from '../pages/plugin-local-install.page';
import { PluginManagePage } from '../pages/plugin-manage.page';
import path from 'path';

/**
 * ローカルプラグインの操作チェーン。
 * Codeception の Local_Plugin に相当。
 */
export class LocalPlugin extends AbstractPlugin {
  protected managePage!: PluginManagePage;
  protected code: string;

  constructor(page: Page, db: DbClient, config: PluginTestConfig, code: string) {
    super(page, db, config);
    this.code = code;
  }

  private tgzPath(dirName: string): string {
    return path.join(this.config.pluginDataDir, `${dirName}.tgz`);
  }

  async インストール(): Promise<this> {
    const dirName = `${this.code}-1.0.0`;
    await compressPlugin(dirName, this.config.pluginDataDir, this.config.pluginDataDir);

    const installPage = await PluginLocalInstallPage.go(this.page, this.config.adminRoute);
    this.managePage = await installPage.アップロード(this.tgzPath(dirName));

    // フラッシュメッセージ確認
    await expect(this.page.locator(PluginManagePage.ALERT_SELECTOR))
      .toContainText('プラグインをインストールしました。', { timeout: 30_000 });

    this.initialized = true;

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin, 'プラグインが存在する').not.toBeNull();
    expect(plugin!.initialized, '初期化されている').toBe(true);
    expect(plugin!.enabled, '有効化されていない').toBe(false);

    return this;
  }

  async 有効化(): Promise<this> {
    await this.managePage.独自プラグイン_有効化(this.code);

    this.enabled = true;

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin!.initialized, '初期化されている').toBe(true);
    expect(plugin!.enabled, '有効化されている').toBe(true);

    return this;
  }

  async 無効化(): Promise<this> {
    await this.managePage.独自プラグイン_無効化(this.code);

    this.enabled = false;

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin!.initialized, '初期化されている').toBe(true);
    expect(plugin!.enabled, '無効化されている').toBe(false);

    return this;
  }

  async 削除(): Promise<this> {
    await this.managePage.独自プラグイン_削除(this.code);

    this.initialized = false;
    this.enabled = false;

    await expect(this.page.locator(PluginManagePage.ALERT_SELECTOR))
      .toContainText('プラグインを削除しました。', { timeout: 30_000 });

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin, '削除されている').toBeNull();

    return this;
  }

  async アップデート(): Promise<this> {
    const dirName = `${this.code}-1.0.1`;
    await compressPlugin(dirName, this.config.pluginDataDir, this.config.pluginDataDir);

    await this.managePage.独自プラグイン_アップデート(this.code, this.tgzPath(dirName));

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin!.initialized, '初期化されている').toBe(true);
    expect(plugin!.enabled, '有効/無効').toBe(this.enabled);

    return this;
  }
}
