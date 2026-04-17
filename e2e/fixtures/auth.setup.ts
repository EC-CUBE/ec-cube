import { test as setup, expect } from '@playwright/test';
import path from 'path';
import fs from 'fs';

const authFile = path.join(__dirname, '..', '.auth', 'admin.json');

setup('admin login', async ({ page }) => {
  // .auth ディレクトリを作成
  const authDir = path.dirname(authFile);
  if (!fs.existsSync(authDir)) {
    fs.mkdirSync(authDir, { recursive: true });
  }

  const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
  await page.goto(`/${adminRoute}/`);
  await page.locator('#login_id').fill(process.env.ADMIN_USER || 'admin');
  await page.locator('#password').fill(process.env.ADMIN_PASSWORD || 'password');
  await page.getByRole('button', { name: 'ログイン' }).click();
  await expect(page.locator('.c-pageTitle__titles')).toContainText('ホーム', { timeout: 30_000 });

  await page.context().storageState({ path: authFile });
});
