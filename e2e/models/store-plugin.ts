import { type Page, expect } from '@playwright/test';
import { type DbClient } from '../helpers/db-client';
import { type PluginTestConfig } from '../fixtures/plugin-test';
import { compressPlugin } from '../helpers/tar-helper';
import { AbstractPlugin } from './abstract-plugin';
import { PluginSearchPage } from '../pages/plugin-search.page';
import { PluginManagePage } from '../pages/plugin-manage.page';

/**
 * ストアプラグインの操作チェーン。
 * Codeception の Store_Plugin に相当。
 */
export class StorePlugin extends AbstractPlugin {
  protected managePage!: PluginManagePage;
  protected code: string;
  protected dependency?: StorePlugin;

  constructor(page: Page, db: DbClient, config: PluginTestConfig, code: string, dependency?: StorePlugin) {
    super(page, db, config);
    this.code = code;
    this.dependency = dependency;
  }

  protected async publishPlugin(fileName: string): Promise<void> {
    const dirName = fileName.replace('.tgz', '');
    await compressPlugin(dirName, this.config.reposDir, this.config.pluginDataDir);
  }

  protected async init(): Promise<void> {
    await this.publishPlugin(`${this.code}-1.0.0.tgz`);
    if (this.dependency) {
      this.managePage = this.dependency.managePage;
    }
  }

  async インストール(errorMessage?: string): Promise<this> {
    const searchPage = await PluginSearchPage.go(this.page, this.config.adminRoute);
    const confirmPage = await searchPage.入手する(this.code);

    if (errorMessage) {
      this.managePage = await confirmPage.インストール(errorMessage);
    } else {
      this.managePage = await confirmPage.インストール();
    }

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin, 'プラグインが存在する').not.toBeNull();
    expect(plugin!.initialized, '初期化されていない').toBe(false);
    expect(plugin!.enabled, '有効化されていない').toBe(false);

    if (this.dependency) {
      this.dependency.managePage = this.managePage;
    }

    return this;
  }

  async 有効化(): Promise<this> {
    await this.managePage.ストアプラグイン_有効化(this.code);

    this.initialized = true;
    this.enabled = true;

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin!.initialized, '初期化されている').toBe(true);
    expect(plugin!.enabled, '有効化されている').toBe(true);

    return this;
  }

  async 既に有効なものを有効化(): Promise<this> {
    await this.managePage.ストアプラグイン_有効化(this.code, '既に有効です。');

    this.initialized = true;
    this.enabled = true;

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin!.initialized, '初期化されている').toBe(true);
    expect(plugin!.enabled, '有効化されている').toBe(true);

    return this;
  }

  async 無効化(): Promise<this> {
    await this.managePage.ストアプラグイン_無効化(this.code);

    this.enabled = false;

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin!.initialized, '初期化されている').toBe(true);
    expect(plugin!.enabled, '無効化されている').toBe(false);

    return this;
  }

  async 既に無効なものを無効化(): Promise<this> {
    await this.managePage.ストアプラグイン_無効化(this.code, '既に無効です。');

    this.enabled = false;

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin!.initialized, '初期化されている').toBe(true);
    expect(plugin!.enabled, '無効化されている').toBe(false);

    return this;
  }

  async 削除(errorMessage?: string): Promise<this> {
    if (errorMessage) {
      await this.managePage.ストアプラグイン_削除(this.code, errorMessage);
      await this.検証();

      const plugin = await this.db.getPlugin(this.code);
      expect(plugin, '削除されていない').not.toBeNull();
      return this;
    }

    await this.managePage.ストアプラグイン_削除(this.code);

    this.initialized = false;
    this.enabled = false;

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin, '削除されている').toBeNull();

    return this;
  }

  async アップデート(): Promise<this> {
    await this.publishPlugin(`${this.code}-1.0.1.tgz`);

    await this.page.reload();
    const confirmPage = await this.managePage.ストアプラグイン_アップデート(this.code);
    this.managePage = await confirmPage.アップデート();

    this.initialized = true;
    this.enabled = false;

    await this.検証();

    const plugin = await this.db.getPlugin(this.code);
    expect(plugin!.initialized, '初期化').toBe(this.initialized);
    expect(plugin!.enabled, '有効/無効').toBe(this.enabled);

    return this;
  }

  async 依存より先に有効化(): Promise<this> {
    // サブクラスでオーバーライド
    return this;
  }

  async 依存されているのが有効なのに無効化(): Promise<this> {
    // サブクラスでオーバーライド
    return this;
  }

  async 依存されているのが削除されていないのに削除(): Promise<this> {
    // サブクラスでオーバーライド
    return this;
  }
}
