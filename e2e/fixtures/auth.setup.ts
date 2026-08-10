import { test as setup, expect } from '@playwright/test';
import { ADMIN_PASSWORD, ADMIN_ROUTE, ADMIN_USER } from '../config/default.config';
import path from 'path';
import fs from 'fs';

const authFile = path.join(__dirname, '..', '.auth', 'admin.json');

setup('admin login', async ({ page }) => {
  // .auth ディレクトリを作成
  const authDir = path.dirname(authFile);
  if (!fs.existsSync(authDir)) {
    fs.mkdirSync(authDir, { recursive: true });
  }

  const adminRoute = ADMIN_ROUTE;
  await page.goto(`/${adminRoute}/`);
  await page.locator('#login_id').fill(ADMIN_USER);
  await page.locator('#password').fill(ADMIN_PASSWORD);
  await page.getByRole('button', { name: 'ログイン' }).click();
  await expect(page.locator('.c-pageTitle__titles')).toContainText('ホーム', { timeout: 30_000 });

  await page.context().storageState({ path: authFile });
});
