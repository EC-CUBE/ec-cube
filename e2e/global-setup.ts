import { execSync } from 'child_process';
import path from 'path';

/**
 * Playwright globalSetup
 * テスト実行前にEC-CUBEのテストフィクスチャを生成する。
 * Codeception の _bootstrap.php と同等のデータ（会員、商品、受注）を作成。
 */
export default function globalSetup() {
  const projectDir = path.resolve(__dirname, '..');
  const phpBin = process.env.PHP_BIN || 'php';

  console.log('Setting up test fixtures...');
  try {
    const output = execSync(
      `${phpBin} -d memory_limit=512M e2e/setup-fixtures.php`,
      {
        cwd: projectDir,
        timeout: 120_000,
        env: {
          ...process.env,
          APP_ENV: process.env.APP_ENV || 'codeception',
        },
        stdio: ['pipe', 'pipe', 'pipe'],
      }
    );
    console.log(output.toString());
  } catch (error: any) {
    console.error('Fixture setup failed:', error.stderr?.toString() || error.message);
    // フィクスチャ失敗はテスト実行を止めない（基本データは eccube:fixtures:load で入っている）
    console.warn('Continuing without additional fixtures...');
  }
}
