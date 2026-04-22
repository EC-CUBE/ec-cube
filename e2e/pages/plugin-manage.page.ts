import { type Page, type Locator, expect } from '@playwright/test';

/**
 * プラグイン管理ページ (インストールプラグイン一覧)
 * Codeception の PluginManagePage に相当
 */
export class PluginManagePage {
  /** フラッシュメッセージ (成功/エラー) のセレクタ
   * 複数のアラートが表示される場合があるため、.first() と合わせて使用 */
  static readonly ALERT_SELECTOR = '#page_admin_store_plugin > div.c-container > div.c-contentsArea > div.alert:not(.alert-primary).alert-dismissible span';

  constructor(private readonly page: Page) {}

  static async at(page: Page): Promise<PluginManagePage> {
    await expect(page.locator('.c-pageTitle')).toContainText('インストールプラグイン一覧', { timeout: 30_000 });
    return new PluginManagePage(page);
  }

  // ============================================================
  // ストアプラグイン (オーナーズストアのプラグイン)
  // ============================================================

  private storePluginSection(): Locator {
    return this.page.locator('.card').filter({
      has: this.page.locator('h5.box-title', { hasText: 'オーナーズストアのプラグイン' }),
    });
  }

  private storePluginRow(code: string): Locator {
    return this.storePluginSection()
      .getByRole('row')
      .filter({ has: this.page.getByRole('cell').locator('p', { hasText: new RegExp(`^${code}$`) }) });
  }

  async ストアプラグイン_有効化(code: string, expectedMessage = '有効にしました。'): Promise<this> {
    await this.dismissAlerts();
    const row = this.storePluginRow(code);
    const enableLink = row.locator('a[href*="/enable"]');
    const disableLink = row.locator('a[href*="/disable"]');
    if (await enableLink.count() > 0) {
      await enableLink.click();
    } else if (await disableLink.count() > 0) {
      // 既に有効な場合は disable リンクの href から enable URL を構築
      const disableHref = await disableLink.getAttribute('href');
      const enableUrl = disableHref!.replace('/disable', '/enable');
      await this.page.goto(enableUrl);
    } else {
      // どちらもない場合はページをリロードして再試行
      await this.page.reload();
      await this.page.waitForLoadState('load');
      await this.storePluginRow(code).locator('a[href*="/enable"]').click();
    }
    await this.page.waitForLoadState('load');
    await expect(this.page.locator(PluginManagePage.ALERT_SELECTOR).first()).toContainText(expectedMessage, { timeout: 30_000 });
    return this;
  }

  async ストアプラグイン_無効化(code: string, expectedMessage = '無効にしました。'): Promise<this> {
    await this.dismissAlerts();
    const row = this.storePluginRow(code);
    const disableLink = row.locator('a[href*="/disable"]');
    const enableLink = row.locator('a[href*="/enable"]');
    if (await disableLink.count() > 0) {
      await disableLink.click();
    } else if (await enableLink.count() > 0) {
      const enableHref = await enableLink.getAttribute('href');
      const disableUrl = enableHref!.replace('/enable', '/disable');
      await this.page.goto(disableUrl);
    } else {
      // 有効化/無効化リンクがどちらもない場合はページをリロードして再試行
      await this.page.reload();
      await this.page.waitForLoadState('load');
      await this.storePluginRow(code).locator('a[href*="/disable"]').click();
    }
    await this.page.waitForLoadState('load');
    await expect(this.page.locator(PluginManagePage.ALERT_SELECTOR).first()).toContainText(expectedMessage, { timeout: 30_000 });
    return this;
  }

  async ストアプラグイン_削除(code: string, expectedMessage = '削除が完了しました。'): Promise<this> {
    await this.dismissAlerts();
    const row = this.storePluginRow(code);
    // fa-close = 削除アイコン
    await row.locator('i.fa-close').locator('..').click();

    const modal = this.page.locator('#officialPluginDeleteModal');
    await expect(modal.locator('#officialPluginDeleteButton')).toBeVisible({ timeout: 10_000 });

    // DELETE AJAX レスポンスを直接待つ (composer remove は CI で数分かかる場合がある)
    const deleteResponsePromise = this.page.waitForResponse(
      resp => resp.request().method() === 'DELETE' && resp.url().includes('/uninstall'),
      { timeout: 300_000 }
    );
    await modal.locator('#officialPluginDeleteButton').click();
    await deleteResponsePromise;

    // DELETE 完了後、JS が maintenance disable → DOM 更新するので、結果を待つ
    await expect(modal.locator('.modal-body p')).toContainText(expectedMessage, { timeout: 30_000 });
    // 「完了」ボタン (3番目のボタン) をクリック
    await modal.locator('.modal-footer button:nth-child(3)').click();
    return this;
  }

  async ストアプラグイン_アップデート(code: string): Promise<PluginStoreConfirmPage> {
    const row = this.storePluginRow(code);
    await row.locator('a.btn-ec-regular').click();
    return PluginStoreConfirmPage.at(this.page);
  }

  // ============================================================
  // 独自プラグイン (ユーザー独自プラグイン)
  // ============================================================

  private localPluginSection(): Locator {
    return this.page.locator('.card').filter({
      has: this.page.locator('h5.box-title', { hasText: 'ユーザー独自プラグイン' }),
    });
  }

  private localPluginRow(code: string): Locator {
    return this.localPluginSection()
      .getByRole('row')
      .filter({ hasText: code });
  }

  async 独自プラグイン_有効化(code: string): Promise<this> {
    await this.dismissAlerts();
    const row = this.localPluginRow(code);
    await row.locator('i.fa-play').locator('..').click();
    await this.page.waitForLoadState('load');
    await expect(this.page.locator(PluginManagePage.ALERT_SELECTOR).first()).toContainText('有効にしました。', { timeout: 30_000 });
    return this;
  }

  async 独自プラグイン_無効化(code: string): Promise<this> {
    await this.dismissAlerts();
    const row = this.localPluginRow(code);
    await row.locator('i.fa-pause').locator('..').click();
    await this.page.waitForLoadState('load');
    await expect(this.page.locator(PluginManagePage.ALERT_SELECTOR).first()).toContainText('無効にしました。', { timeout: 30_000 });
    return this;
  }

  async 独自プラグイン_削除(code: string): Promise<this> {
    await this.dismissAlerts();
    const row = this.localPluginRow(code);
    await row.locator('i.fa-close').locator('..').click();

    const modal = this.page.locator('#localPluginDeleteModal');
    await expect(modal.locator('.modal-footer a')).toBeVisible({ timeout: 10_000 });
    await modal.locator('.modal-footer a').click();
    await this.page.waitForLoadState('load');
    return this;
  }

  async 独自プラグイン_アップデート(code: string, tgzPath: string): Promise<this> {
    const row = this.localPluginRow(code);
    // ファイル選択
    const fileInput = row.locator('input[type="file"]');
    await fileInput.setInputFiles(tgzPath);
    // アップデートボタンクリック
    await row.locator('button.btn-primary').click();
    await this.page.waitForLoadState('load');
    await expect(this.page.locator(PluginManagePage.ALERT_SELECTOR)).toContainText('アップデートしました。', { timeout: 30_000 });
    return this;
  }

  // ============================================================
  // ヘルパー
  // ============================================================

  private async dismissAlerts(): Promise<void> {
    // 既存のフラッシュメッセージを閉じる (クリックを遮らないように)
    const closeButtons = this.page.locator('.alert-dismissible .btn-close');
    const count = await closeButtons.count();
    const clicks = Array.from({ length: count }, (_, i) => closeButtons.nth(i).click());
    await Promise.allSettled(clicks);
    // tooltip も除去
    await this.page.locator('.tooltip').evaluateAll(els => els.forEach(e => e.remove()));
  }
}

/**
 * プラグインストアインストール確認 / アップデート確認ページ
 * Codeception の PluginStoreInstallPage / PluginStoreUpgradePage を統合
 */
export class PluginStoreConfirmPage {
  constructor(private readonly page: Page) {}

  static async at(page: Page): Promise<PluginStoreConfirmPage> {
    await expect(page.locator('.c-pageTitle')).toContainText('オーナーズストア', { timeout: 30_000 });
    return new PluginStoreConfirmPage(page);
  }

  async インストール(expectedMessage = 'インストールが完了しました。'): Promise<PluginManagePage> {
    // 「インストール」or「アップデート」ボタンでモーダルを開く
    await this.page.locator('#plugin-list button.btn-primary').click();

    const modal = this.page.locator('#installModal');
    await expect(modal.locator('#installBtn')).toBeVisible({ timeout: 60_000 });
    await modal.locator('#installBtn').click();

    // AJAX チェーン完了待ち: 「完了」リンクが表示される
    // composer require/update は CI で数分かかる場合があるため長めに設定
    const completionLink = modal.locator('.modal-footer a');
    await expect(completionLink).toBeVisible({ timeout: 300_000 });
    await expect(modal.locator('.modal-body > p')).toContainText(expectedMessage);
    await completionLink.click();

    return PluginManagePage.at(this.page);
  }

  async アップデート(): Promise<PluginManagePage> {
    return this.インストール('インストールが完了しました。');
  }
}
