import { type Page, expect } from '@playwright/test';
import { type DbClient } from '../helpers/db-client';
import { traitExists } from '../helpers/file-helper';
import { type PluginTestConfig } from '../fixtures/plugin-test';
import { PluginManagePage } from '../pages/plugin-manage.page';

/**
 * プラグインのライフサイクル状態を追跡し、DB/ファイルレベルの検証を行う基底クラス。
 * Codeception の Abstract_Plugin に相当。
 */
export abstract class AbstractPlugin {
  protected page: Page;
  protected db: DbClient;
  protected config: PluginTestConfig;

  /**
   * プラグイン一覧のページオブジェクト。
   *
   * 生成時の Page を持ち続けるため、タブを切り替えたら貼り替えが必要になる。
   * サブクラス側で個別に持たせるとタブ切り替えの取り漏らしが起きるので、ここで一元管理する。
   */
  protected managePage!: PluginManagePage;

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
    await this.タブを切り替え(newPage);

    return oldPage;
  }

  async タブを切り替え(targetPage: Page): Promise<void> {
    this.page = targetPage;
    await this.page.bringToFront();
    await this.ページオブジェクトを貼り替え();
  }

  /**
   * 保持しているページオブジェクトを現在のタブへ貼り替える。
   *
   * ページオブジェクトは生成時の Page を持ち続けるため、`this.page` だけ差し替えても
   * 操作先は元のタブに残る（マルチタブの競合テストが「別タブで操作したつもりで
   * 同じタブを操作する」ことになり、成立しない）。
   */
  protected async ページオブジェクトを貼り替え(): Promise<void> {
    // タブ切り替えはプラグイン一覧に居る前提。別の画面で切り替えると
    // PluginManagePage.at() の 30 秒待ちのあと落ちて原因が分かりにくいので、
    // ここで短く落として理由を出す。
    await expect(this.page.locator('.c-pageTitle'), 'タブ切り替えはプラグイン一覧で行う')
      .toContainText('インストールプラグイン一覧', { timeout: 5_000 });
    this.managePage = await PluginManagePage.at(this.page);
  }
}
