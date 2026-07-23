import { test, expect } from '@playwright/test';
import { ADMIN_ROUTE } from '../config/default.config';

const adminRoute = ADMIN_ROUTE;

test.describe('Admin Store (EA10)', () => {
  test.describe.configure({ mode: 'serial' });

  // 認証キー新規発行時の注意喚起モーダル（オーナーズストアでの二重発行防止）
  // @see https://github.com/EC-CUBE/ec-cube/issues/6208
  test('store_authkey_confirm_modal_transition - EA1001-UC01-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/store/plugin/authentication_setting`);
    await expect(page.locator('.c-pageTitle__titles')).toContainText('認証キー設定');

    // 「認証キー新規発行」ボタンは CAPTCHA を直接開かず、注意喚起モーダルを開く
    const trigger = page.locator('button[data-bs-target="#authentication_key_confirm"]');
    await expect(trigger).toBeVisible();
    await expect(page.locator('#authentication_key_confirm')).toBeHidden();

    await trigger.click();

    // 注意喚起モーダルが表示され、CAPTCHA モーダルはまだ非表示
    const confirmModal = page.locator('#authentication_key_confirm');
    await expect(confirmModal).toBeVisible();
    await expect(confirmModal).toContainText('オーナーズストア');
    await expect(page.locator('#captcha')).toBeHidden();

    // 「発行手続きへ進む」で注意喚起モーダルが閉じ、既存の CAPTCHA モーダルへ遷移する
    await page.locator('#proceed_to_captcha').click();
    await expect(confirmModal).toBeHidden();
    await expect(page.locator('#captcha')).toBeVisible();
    // モーダル連鎖(hide→show)でバックドロップが二重化・残留しないこと
    await expect(page.locator('.modal-backdrop')).toHaveCount(1);
  });

  test('store_authkey_confirm_modal_cancel - EA1001-UC01-T02', async ({ page }) => {
    await page.goto(`/${adminRoute}/store/plugin/authentication_setting`);

    await page.locator('button[data-bs-target="#authentication_key_confirm"]').click();
    const confirmModal = page.locator('#authentication_key_confirm');
    await expect(confirmModal).toBeVisible();

    // 「キャンセル」で注意喚起モーダルを閉じても CAPTCHA モーダルは開かない
    await confirmModal.getByRole('button', { name: 'キャンセル' }).click();
    await expect(confirmModal).toBeHidden();
    await expect(page.locator('#captcha')).toBeHidden();
    // 閉じた後にバックドロップが残留しないこと
    await expect(page.locator('.modal-backdrop')).toHaveCount(0);
  });
});
