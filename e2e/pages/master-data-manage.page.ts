import { type Page, expect } from '@playwright/test';

/**
 * マスタデータ管理ページ
 * Codeception の Page\Admin\MasterDataManagePage に相当。
 * Issue #5400 / #6273: Entity/Master 配下のマスタEntityをtraitで拡張しても
 * プルダウンから消えないことを検証する。
 */
export class MasterDataManagePage {
  /** マスタデータ選択プルダウン (form1) */
  static readonly SELECT_SELECTOR = '#admin_system_masterdata_masterdata';
  /** 更新完了フラッシュ */
  static readonly ALERT_SUCCESS_SELECTOR = '.c-contentsArea .alert-success';

  constructor(private readonly page: Page) {}

  static async go(page: Page, adminRoute = 'admin'): Promise<MasterDataManagePage> {
    await page.goto(`/${adminRoute}/setting/system/masterdata`);
    await expect(page.locator(MasterDataManagePage.SELECT_SELECTOR)).toBeVisible({ timeout: 30_000 });
    return new MasterDataManagePage(page);
  }

  /**
   * プルダウンに指定ラベル(テーブル名)の選択肢が存在するか。
   * バグがある場合は拡張したマスタのテーブル名が選択肢から消えるため false になる。
   */
  async 選択肢が存在する(label: string): Promise<boolean> {
    const count = await this.page
      .locator(MasterDataManagePage.SELECT_SELECTOR)
      .locator('option', { hasText: label })
      .count();
    return count > 0;
  }

  /**
   * マスタデータを選択して form1 を submit し、編集フォーム(form2)を表示する。
   * 選択肢が存在しない場合は selectOption がタイムアウトして回帰を検知する。
   */
  async 選択(label: string): Promise<this> {
    await expect(
      this.page.locator(MasterDataManagePage.SELECT_SELECTOR).locator('option', { hasText: label }),
      `マスタデータのプルダウンに ${label} が存在する`,
    ).toHaveCount(1);

    await this.page.locator(MasterDataManagePage.SELECT_SELECTOR).selectOption({ label });
    await this.page.locator('#form1 button[type="submit"]').click();
    await this.page.waitForLoadState('load');

    // 選択後は編集フォーム(form2)が表示される
    await expect(this.page.locator('#form2')).toBeVisible({ timeout: 30_000 });

    return this;
  }

  /**
   * 編集フォーム(form2)を保存し、更新完了メッセージを確認する。
   */
  async 保存(): Promise<this> {
    await this.page.locator('#form2 .c-conversionArea button[type="submit"]').click();
    await this.page.waitForLoadState('load');
    await expect(this.page.locator(MasterDataManagePage.ALERT_SUCCESS_SELECTOR)).toContainText('保存しました', {
      timeout: 30_000,
    });

    return this;
  }
}
