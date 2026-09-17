import { type Page, expect } from '@playwright/test';

/**
 * インストーラの入力値
 */
export type InstallSiteConfig = {
  shopName: string;
  email: string;
  loginId: string;
  /** 15 文字以上. Step3Type の NotCompromisedPassword を踏まないよう推測されにくい値にする */
  loginPass: string;
  /** 'admin' は Step3Type の NotEqualTo 制約で拒否される */
  adminDir: string;
};

export type InstallDatabaseConfig = {
  /** Step4Type の ChoiceType の value. pdo_pgsql / pdo_mysql / pdo_sqlite */
  driver: 'pdo_pgsql' | 'pdo_mysql' | 'pdo_sqlite';
  host?: string;
  port?: string;
  name?: string;
  user?: string;
  password?: string;
};

/**
 * インストーラ (APP_ENV=install, /install/step1 〜 /install/complete)
 *
 * Codeception の Page\Install\InstallPage に相当するが、旧実装は step1〜step2 の
 * 権限チェックのみだったため、step3 以降は新規.
 *
 * 注意: #main の class は step 番号とズレている (step3 の #main は class="step4")
 * ため、到達判定には h1 と URL を使う.
 */
export class InstallPage {
  constructor(private readonly page: Page) {}

  static async go(page: Page): Promise<InstallPage> {
    await page.goto('/install/step1');
    await expect(page.locator('h1')).toContainText('ようこそ', { timeout: 30_000 });
    return new InstallPage(page);
  }

  private async 次へ進む(): Promise<void> {
    await this.page.locator('#form1 button[type="submit"]').click();
  }

  /**
   * step1: ようこそ
   *
   * 同意チェックボックス (#install_step1_agree) はチェックしない.
   * チェックすると InstallController::sendAppData() が ec-cube.net へ
   * 外部 POST するため、E2E では避ける.
   */
  async step1_次へ(): Promise<void> {
    await expect(this.page.locator('h1')).toContainText('ようこそ');
    await this.次へ進む();
  }

  /**
   * step2: 権限チェック
   *
   * vendor 配下まで is_writable で走査するため遅い.
   */
  async step2_権限チェックが正常なこと(timeout = 120_000): Promise<void> {
    await expect(this.page.locator('h1')).toContainText('権限チェック', { timeout });
    await expect(this.page.locator('textarea[name="disp_area"]')).toContainText(
      'アクセス権限は正常です',
      { timeout }
    );
  }

  /** step2 の「次へ進む」は form ではなくリンク */
  async step2_次へ(): Promise<void> {
    await this.page.getByRole('link', { name: '次へ進む' }).click();
  }

  /**
   * step3: サイトの設定
   *
   * admin_force_ssl は https 接続時のみ有効 (HTTP では disabled) なので触らない.
   */
  async step3_サイトの設定を入力(config: InstallSiteConfig): Promise<void> {
    await expect(this.page.locator('h1')).toContainText('サイトの設定');
    await this.page.locator('#install_step3_shop_name').fill(config.shopName);
    await this.page.locator('#install_step3_email').fill(config.email);
    await this.page.locator('#install_step3_login_id').fill(config.loginId);
    await this.page.locator('#install_step3_login_pass').fill(config.loginPass);
    await this.page.locator('#install_step3_admin_dir').fill(config.adminDir);
    await this.次へ進む();
  }

  /**
   * step4: データベースの設定
   *
   * pdo_sqlite を選ぶと接続情報の入力欄は JS で disabled になるため、
   * 値が指定された項目だけを入力する.
   */
  async step4_データベースの設定を入力(config: InstallDatabaseConfig): Promise<void> {
    await expect(this.page.locator('h1')).toContainText('データベースの設定');
    await this.page.locator('#install_step4_database').selectOption(config.driver);

    const fields: [string, string | undefined][] = [
      ['#install_step4_database_host', config.host],
      ['#install_step4_database_port', config.port],
      ['#install_step4_database_name', config.name],
      ['#install_step4_database_user', config.user],
      ['#install_step4_database_password', config.password],
    ];
    for (const [selector, value] of fields) {
      if (value === undefined) {
        continue;
      }
      await this.page.locator(selector).fill(value);
    }

    await this.次へ進む();
  }

  /**
   * step5: データベースの初期化
   *
   * no_update はチェックしない (drop -> create -> CSV import -> insert のフルパス).
   * この POST が最も重いため timeout を長めに取る.
   */
  async step5_データベースを初期化(timeout = 300_000): Promise<void> {
    await expect(this.page.locator('h1')).toContainText('データベースの初期化');
    await this.次へ進む();
    await this.page.waitForURL(/\/install\/complete$/, { timeout });
  }

  /** インストール完了画面に到達していること */
  async complete_完了していること(): Promise<void> {
    await expect(this.page.locator('h1')).toContainText('インストールが完了しました');
  }

  /**
   * 完了画面の JS が完走し「管理画面を表示」ボタンが有効化されるのを待つ.
   *
   * 完了画面は data-bs-backdrop="static" のモーダルが全面を覆っており、
   * プラグイン一覧の取得が終わるまでクリックできない.
   */
  async complete_管理画面ボタンの有効化を待つ(timeout = 120_000): Promise<void> {
    await expect(this.page.locator('#go_to_admin_page')).not.toHaveClass(/disabled/, { timeout });
    await expect(this.page.locator('#PluginProgressModal')).not.toBeVisible({ timeout });
  }

  /** 「管理画面を表示」をクリックする */
  async complete_管理画面を表示(): Promise<void> {
    await this.page.locator('#go_to_admin_page').click();
  }
}
