import { defineConfig, devices } from '@playwright/test';

/**
 * See https://playwright.dev/docs/test-configuration.
 */
export default defineConfig({
  // Playwrightテスト用ディレクトリ
  testDir: './playwright/acceptance',

  // 並列実行を有効化
  fullyParallel: true,

  // CIではtest.onlyをエラーにする
  forbidOnly: !!process.env.CI,

  // CIでのリトライ回数
  retries: process.env.CI ? 2 : 0,

  // CIでのワーカー数
  workers: process.env.CI ? 1 : undefined,

  // レポータ形式設定
  reporter: 'html',

  // すべてのプロジェクトに共通の設定
  use: {
    // 明示的に設定
    baseURL: 'http://127.0.0.1:8080',

    // テスト失敗時のトレース収集
    trace: 'on-first-retry',
  },

  // 各ブラウザの設定
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    // 以下は必要に応じて有効化
    // {
    //   name: 'Microsoft Edge',
    //   use: { ...devices['Desktop Edge'], channel: 'msedge' },
    // },
    // {
    //   name: 'Google Chrome',
    //   use: { ...devices['Desktop Chrome'], channel: 'chrome' },
    // },
  ],

  // devサーバを起動する場合
  // webServer: {
  //   command: 'npm run start',
  //   url: 'http://127.0.0.1:3000',
  //   reuseExistingServer: !process.env.CI,
  // },
});
