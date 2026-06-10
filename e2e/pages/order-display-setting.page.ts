import { type Page, expect } from '@playwright/test';

/**
 * 受注一覧 表示項目設定ページ
 *
 * CSV出力項目設定（CsvController）と同じ2カラム DnD 方式の設定画面.
 */
export class OrderDisplaySettingPage {
  constructor(private readonly page: Page) {}

  static async go(page: Page, adminRoute = 'admin'): Promise<OrderDisplaySettingPage> {
    await page.goto(`/${adminRoute}/order/display_setting`);
    await expect(page.locator('.c-pageTitle')).toContainText('表示項目設定', { timeout: 30_000 });
    return new OrderDisplaySettingPage(page);
  }

  /** 指定した表示名の列を「非表示項目」へ移動する */
  async 列を非表示にする(dispName: string): Promise<void> {
    await this.page.locator('#display-items').selectOption({ label: dispName });
    await this.page.locator('#remove').click();
  }

  /** 指定した表示名の列を「表示項目」へ移動する */
  async 列を表示する(dispName: string): Promise<void> {
    await this.page.locator('#non-display-items').selectOption({ label: dispName });
    await this.page.locator('#add').click();
  }

  /** すべての列を表示項目へ移動する */
  async 全て表示する(): Promise<void> {
    await this.page.locator('#add-all').click();
  }

  /** 選択中の表示項目を先頭へ移動する */
  async 先頭へ移動(dispName: string): Promise<void> {
    await this.page.locator('#display-items').selectOption({ label: dispName });
    await this.page.locator('.move-most[data-value="top"]').click();
  }

  /** 登録（保存）する */
  async 登録(): Promise<void> {
    await this.page.locator('button[type="submit"]').click();
    await this.page.waitForLoadState('load');
  }

  /** 現在の「表示項目」ボックスの表示名一覧（並び順） */
  async 表示項目の並び(): Promise<string[]> {
    return this.page.locator('#display-items option').allTextContents();
  }

  /** 現在の「非表示項目」ボックスの表示名一覧 */
  async 非表示項目の並び(): Promise<string[]> {
    return this.page.locator('#non-display-items option').allTextContents();
  }
}
