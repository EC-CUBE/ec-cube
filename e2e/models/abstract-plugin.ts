import { type Page, expect } from '@playwright/test';
import { type DbClient } from '../helpers/db-client';
import { traitExists } from '../helpers/file-helper';
import { type PluginTestConfig } from '../fixtures/plugin-test';

/**
 * プラグインのライフサイクル状態を追跡し、DB/ファイルレベルの検証を行う基底クラス。
 * Codeception の Abstract_Plugin に相当。
 */
export abstract class AbstractPlugin {
  protected page: Page;
  protected db: DbClient;
  protected config: PluginTestConfig;

  protected initialized = false;
  protected enabled = false;

  protected tables: string[] = [];
  protected columns: string[] = [];
  protected traits: Map<string, string> = new Map(); // traitClass -> proxyTarget

  constructor(page: Page, db: DbClient, config: PluginTestConfig) {
    this.page = page;
    this.db = db;
    this.config = config;
  }

  async 検証(): Promise<this> {
    // テーブルの存在確認
    for (const table of this.tables) {
      const exists = await this.db.tableExists(table);
      if (this.initialized) {
        expect(exists, `テーブルがあるはず ${table}`).toBe(true);
      } else {
        expect(exists, `テーブルがないはず ${table}`).toBe(false);
      }
    }

    // カラムの存在確認
    for (const col of this.columns) {
      const [tableName, columnName] = col.split('.');
      const exists = await this.db.columnExists(tableName, columnName);
      if (this.initialized) {
        expect(exists, `カラムがあるはず ${col}`).toBe(true);
      } else {
        expect(exists, `カラムがないはず ${col}`).toBe(false);
      }
    }

    // Trait の注入確認
    for (const [traitClass, target] of this.traits) {
      const exists = traitExists(this.config.projectDir, traitClass, target);
      if (this.enabled) {
        expect(exists, `Traitがあるはず ${traitClass}`).toBe(true);
      } else {
        expect(exists, `Traitがないはず ${traitClass}`).toBe(false);
      }
    }

    return this;
  }

  async 新しいタブで開く(): Promise<Page> {
    const newPage = await this.page.context().newPage();
    await newPage.goto(this.page.url());
    await newPage.waitForLoadState('load');
    const oldPage = this.page;
    this.page = newPage;
    return oldPage;
  }

  async タブを切り替え(targetPage: Page): Promise<void> {
    this.page = targetPage;
    await this.page.bringToFront();
  }
}
