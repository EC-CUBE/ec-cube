import { test, expect } from '@playwright/test';
import path from 'path';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
const authFile = path.join(__dirname, '..', '.auth', 'admin.json');

test.describe('Admin System Info (EA08)', () => {
  test.describe.configure({ mode: 'serial' });

  test('systeminfo_system_info_display - EA0801-UC01-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/system`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle__titles')).toContainText('システム情報');

    // Verify system info labels
    await expect(page.locator('#server_info_box__header > div > span')).toContainText('システム情報');
    await expect(page.locator('#server_info_box__body_inner > div:nth-child(1) > div:first-child')).toContainText('EC-CUBE');
    await expect(page.locator('#server_info_box__body_inner > div:nth-child(2) > div:first-child')).toContainText('サーバーOS');
    await expect(page.locator('#server_info_box__body_inner > div:nth-child(3) > div:first-child')).toContainText('DBサーバー');
    await expect(page.locator('#server_info_box__body_inner > div:nth-child(4) > div:first-child')).toContainText('WEBサーバー');
    await expect(page.locator('#server_info_box__body_inner > div:nth-child(5) > div:first-child')).toContainText('PHP');
    await expect(page.locator('#server_info_box__body_inner > div:nth-child(6) > div:first-child')).toContainText('User Agent');
  });

  test('systeminfo_member_list_display - EA0802-UC01-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('メンバー管理');

    await expect(page.locator('#ex-member-new > a')).toContainText('新規登録');
  });

  test('systeminfo_member_create - EA0803-UC01-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('メンバー管理');

    // Click new registration
    await page.locator('#ex-member-new > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#member_form .c-contentsArea__primaryCol .card-header .card-title')).toContainText('メンバー登録');

    // Fill in form
    await page.locator('#admin_member_name').fill('admintest');
    await page.locator('#admin_member_department').fill('admintest department');
    await page.locator('#admin_member_login_id').fill('admintest');
    await page.locator('#admin_member_plain_password_first').fill('password1234');
    await page.locator('#admin_member_plain_password_second').fill('password1234');
    await page.locator('#admin_member_Authority').selectOption({ label: 'システム管理者' });
    await page.locator('#admin_member_Work_1').check();

    // Submit
    await page.locator('#member_form .c-conversionArea__container button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました');

    // Verify on list
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('メンバー管理');
    await expect(page.locator('.card-body tbody tr:nth-child(1) td:nth-child(1)')).toContainText('admintest');
  });

  test('systeminfo_member_create_cancel - EA0803-UC01-T02', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.waitForLoadState('load');

    await page.locator('#ex-member-new > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#member_form .c-contentsArea__primaryCol .card-header .card-title')).toContainText('メンバー登録');

    // Fill in form but cancel
    await page.locator('#admin_member_name').fill('admintest2');
    await page.locator('#admin_member_department').fill('admintest department');
    await page.locator('#admin_member_login_id').fill('admintest');
    await page.locator('#admin_member_plain_password_first').fill('password1234');
    await page.locator('#admin_member_plain_password_second').fill('password1234');
    await page.locator('#admin_member_Authority').selectOption({ label: 'システム管理者' });
    await page.locator('#admin_member_Work_1').check();

    // Click cancel link
    await page.locator('#member_form .c-conversionArea__container .c-conversionArea__leftBlockItem a').click();
    await page.waitForLoadState('load');

    await expect(page.locator('.c-pageTitle')).toContainText('メンバー管理');
    // admintest2 should NOT appear
    const firstCell = await page.locator('.card-body tbody tr:nth-child(1) td:nth-child(1)').textContent();
    expect(firstCell).not.toContain('admintest2');
  });

  test('systeminfo_member_create_validation - EA0803-UC01-T03', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.waitForLoadState('load');

    await page.locator('#ex-member-new > a').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#member_form .c-contentsArea__primaryCol .card-header .card-title')).toContainText('メンバー登録');

    // Submit empty form
    await page.locator('#member_form .c-conversionArea__container button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#member_form')).toContainText('入力されていません。');
  });

  test('systeminfo_member_edit - EA0803-UC02-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('メンバー管理');

    // Click edit on first member
    await page.locator('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(6) .action-edit').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#member_form .c-contentsArea__primaryCol .card-header .card-title')).toContainText('メンバー登録');

    await page.locator('#admin_member_name').fill('administrator');
    await page.locator('#member_form .c-conversionArea__container button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました');

    // Verify
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(1)')).toContainText('administrator');
  });

  test('systeminfo_member_edit_cancel - EA0803-UC02-T02', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.waitForLoadState('load');

    await page.locator('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(6) .action-edit').click();
    await page.waitForLoadState('load');

    await page.locator('#admin_member_name').fill('administrator2');
    await page.locator('#member_form .c-conversionArea__container .c-conversionArea__leftBlockItem a').click();
    await page.waitForLoadState('load');

    await expect(page.locator('.c-pageTitle')).toContainText('メンバー管理');
    const firstCell = await page.locator('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(1)').textContent();
    expect(firstCell).not.toContain('administrator2');
  });

  test('systeminfo_member_edit_validation - EA0803-UC02-T03', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.waitForLoadState('load');

    await page.locator('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(6) .action-edit').click();
    await page.waitForLoadState('load');

    await page.locator('#admin_member_name').fill('');
    await page.locator('#member_form .c-conversionArea__container button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#member_form')).toContainText('入力されていません。');
  });

  test('systeminfo_member_move_down - EA0802-UC01-T02', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('メンバー管理');

    await page.locator('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(6) .action-down').click();
    await page.waitForLoadState('load');
    await page.waitForTimeout(1000);

    await expect(page.locator('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(1)')).toContainText('管理者');
  });

  test('systeminfo_member_move_up - EA0802-UC01-T03', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('メンバー管理');

    await page.locator('.c-primaryCol .card-body table tbody tr:nth-child(2) td:nth-child(6) .action-up').click();
    await page.waitForLoadState('load');
    await page.waitForTimeout(1000);

    await expect(page.locator('.c-primaryCol .card-body table tbody tr:nth-child(2) td:nth-child(1)')).toContainText('管理者');
  });

  test('systeminfo_member_delete - EA0802-UC01-T06', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('メンバー管理');
    await expect(page.locator('.card-body tbody tr:nth-child(1) td:nth-child(1)')).toContainText('administrator');

    // Click delete
    await page.locator('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(6) .action-delete').click();
    await page.waitForTimeout(500);

    // Accept modal
    await page.locator('.c-primaryCol .card-body table tbody tr:nth-child(1) .modal .btn-ec-delete').click();
    await page.waitForLoadState('load');

    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('削除しました');
    await expect(page.locator('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(1)')).toContainText('管理者');
  });

  test('systeminfo_security_display - EA0804-UC01-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/security`);
    await page.waitForLoadState('load');
    await expect(page.locator('#page_admin_setting_system_security .c-pageTitle__titles')).toContainText('セキュリティ管理');
    await expect(page.locator('#page_admin_setting_system_security > div.c-container > div.c-contentsArea > form > div > div.c-contentsArea__primaryCol > div > div > div.card-header > div > div.col-8 > span').first()).toContainText('管理画面URL設定');
  });

  test('systeminfo_security_directory_name - EA0804-UC01-T02', async ({ page }) => {
    // Navigate to security settings
    await page.goto(`/${adminRoute}/setting/system/security`);
    await page.waitForLoadState('load');
    await expect(page.locator('#page_admin_setting_system_security .c-pageTitle__titles')).toContainText('セキュリティ管理');

    // Change admin directory name to 'admin2'
    await page.locator('#admin_security_admin_route_dir').fill('admin2');
    await page.locator('#page_admin_setting_system_security form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');

    // After directory change, we are redirected to login on the new URL
    // Login via the new admin URL
    await page.goto('/admin2/');
    await page.waitForLoadState('load');
    await page.locator('#login_id').fill(process.env.ADMIN_USER || 'admin');
    await page.locator('#password').fill(process.env.ADMIN_PASSWORD || 'password');
    await page.getByRole('button', { name: 'ログイン' }).click();
    await expect(page.locator('.c-pageTitle__titles')).toContainText('ホーム', { timeout: 30_000 });

    // Change back to original admin directory
    await page.goto('/admin2/setting/system/security');
    await page.waitForLoadState('load');
    await page.locator('#admin_security_admin_route_dir').fill(adminRoute);
    await page.locator('#page_admin_setting_system_security form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');

    // Login again via the original admin URL and save auth state for subsequent tests
    await page.goto(`/${adminRoute}/`);
    await page.waitForLoadState('load');
    await page.locator('#login_id').fill(process.env.ADMIN_USER || 'admin');
    await page.locator('#password').fill(process.env.ADMIN_PASSWORD || 'password');
    await page.getByRole('button', { name: 'ログイン' }).click();
    await expect(page.locator('.c-pageTitle__titles')).toContainText('ホーム', { timeout: 30_000 });

    // Save the new auth state so subsequent tests can use it
    await page.context().storageState({ path: authFile });
  });

  test('systeminfo_security_admin_deny_list - EA0804-UC01-T05', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/security`);
    await page.waitForLoadState('load');
    await expect(page.locator('#page_admin_setting_system_security .c-pageTitle__titles')).toContainText('セキュリティ管理');

    // Ensure trusted_hosts is filled (required field)
    const trustedHosts = await page.locator('#admin_security_trusted_hosts').inputValue();
    if (!trustedHosts) {
      await page.locator('#admin_security_trusted_hosts').fill('^127\\.0\\.0\\.1$');
    }

    // Set deny hosts to a safe value (not our own IP to avoid lockout)
    await page.locator('#admin_security_admin_deny_hosts').fill('1.1.1.1');
    await page.locator('#page_admin_setting_system_security form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');

    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました', { timeout: 30_000 });

    // Verify the value was saved
    await page.goto(`/${adminRoute}/setting/system/security`);
    await page.waitForLoadState('load');
    await expect(page.locator('#admin_security_admin_deny_hosts')).toHaveValue('1.1.1.1');

    // Clean up - remove the deny list entry
    await page.locator('#admin_security_admin_deny_hosts').fill('');
    await page.locator('#page_admin_setting_system_security form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました', { timeout: 30_000 });
  });

  test('systeminfo_security_front_ip_allow_list - EA0804-UC01-T06', async ({ page }) => {
    // Test 1: Allow list matching our IP - front should be accessible
    await page.goto(`/${adminRoute}/setting/system/security`);
    await page.waitForLoadState('load');

    await page.locator('#admin_security_front_allow_hosts').fill('127.0.0.1');
    await page.locator('#page_admin_setting_system_security form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました', { timeout: 30_000 });

    // Verify front page is accessible (our IP is in the allow list)
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('body')).not.toContainText('アクセスできません');

    // Test 2: Allow list NOT matching our IP - front should be blocked
    await page.goto(`/${adminRoute}/setting/system/security`);
    await page.waitForLoadState('load');

    await page.locator('#admin_security_front_allow_hosts').fill('192.168.100.1');
    await page.locator('#page_admin_setting_system_security form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました', { timeout: 30_000 });

    // Verify front page is blocked (our IP is NOT in the allow list)
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('body')).toContainText('アクセスできません');

    // Clean up - remove the allow list
    await page.goto(`/${adminRoute}/setting/system/security`);
    await page.waitForLoadState('load');
    await page.locator('#admin_security_front_allow_hosts').fill('');
    await page.locator('#page_admin_setting_system_security form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました', { timeout: 30_000 });
  });

  test('systeminfo_security_front_ip_deny_list - EA0804-UC01-T07', async ({ page }) => {
    // Test 1: Deny list matching our IP - front should be blocked
    await page.goto(`/${adminRoute}/setting/system/security`);
    await page.waitForLoadState('load');

    await page.locator('#admin_security_front_deny_hosts').fill('127.0.0.1');
    await page.locator('#page_admin_setting_system_security form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました', { timeout: 30_000 });

    // Verify front page is blocked (our IP is in the deny list)
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('body')).toContainText('アクセスできません');

    // Test 2: Deny list NOT matching our IP - front should be accessible
    await page.goto(`/${adminRoute}/setting/system/security`);
    await page.waitForLoadState('load');

    await page.locator('#admin_security_front_deny_hosts').fill('192.168.100.1');
    await page.locator('#page_admin_setting_system_security form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました', { timeout: 30_000 });

    // Verify front page is accessible (our IP is NOT in the deny list)
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('body')).not.toContainText('アクセスできません');

    // Clean up - remove the deny list
    await page.goto(`/${adminRoute}/setting/system/security`);
    await page.waitForLoadState('load');
    await page.locator('#admin_security_front_deny_hosts').fill('');
    await page.locator('#page_admin_setting_system_security form .c-conversionArea button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました', { timeout: 30_000 });
  });

  test('systeminfo_authority_management - EA0805-UC03-T01/T02 and UC04-T01', async ({ page }) => {
    // Add authority rules
    await page.goto(`/${adminRoute}/setting/system/authority`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('権限管理');

    // Add row
    await page.locator('body > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body > p > button').click();
    await page.waitForTimeout(500);

    // Fill in: system admin, /content
    await page.locator('form #table-authority tbody tr:nth-child(1) td:nth-child(1) select').selectOption({ label: 'システム管理者' });
    await page.locator('form #table-authority tbody tr:nth-child(1) td:nth-child(2) input').fill('/content');

    // Add second row
    await page.locator('body > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body > p > button').click();
    await page.waitForTimeout(500);

    await page.locator('form #table-authority tbody tr:nth-child(2) td:nth-child(1) select').selectOption({ label: 'システム管理者' });
    await page.locator('form #table-authority tbody tr:nth-child(2) td:nth-child(2) input').fill('/store');

    // Save
    await page.locator('form .c-conversionArea button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました');

    // Verify that content management and owners store are hidden in nav
    await expect(page.locator('nav .c-mainNavArea__nav')).not.toContainText('コンテンツ管理');
    await expect(page.locator('nav .c-mainNavArea__nav')).not.toContainText('オーナーズストア');

    // Delete the authority rules
    await page.goto(`/${adminRoute}/setting/system/authority`);
    await page.waitForLoadState('load');

    // Delete rows (delete row 2 first, then row 1)
    await page.locator('form tbody tr:nth-child(2) td:nth-child(3) button').click();
    await page.locator('form tbody tr:nth-child(1) td:nth-child(3) button').click();

    await page.locator('form .c-conversionArea button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました');

    // Verify content management and owners store are visible again
    await expect(page.locator('nav .c-mainNavArea__nav')).toContainText('コンテンツ管理');
    await expect(page.locator('nav .c-mainNavArea__nav')).toContainText('オーナーズストア');
  });

  test('systeminfo_masterdata_management - EA0807', async ({ page }) => {
    // Helper: navigate to masterdata and select mtb_sex
    async function goToMtbSex() {
      await page.goto(`/${adminRoute}/setting/system/masterdata`);
      await page.waitForLoadState('load');
      await page.locator('#admin_system_masterdata_masterdata').selectOption('mtb_sex');
      await page.locator('#form1 button.btn-primary').click();
      await page.waitForLoadState('load');
    }

    // Clean up: remove any entries beyond the default 2 (男性, 女性)
    await goToMtbSex();
    const rows = await page.locator('#form2 table tbody tr').count();
    if (rows > 3) { // 2 data rows + 1 empty row = 3
      // Clear all rows beyond 男性 and 女性
      for (let i = 3; i <= rows; i++) {
        const idVal = await page.locator(`#form2 table tbody tr:nth-child(${i}) td:nth-child(1) input`).inputValue();
        const nameVal = await page.locator(`#form2 table tbody tr:nth-child(${i}) td:nth-child(2) input`).inputValue();
        if (idVal || nameVal) {
          await page.locator(`#form2 table tbody tr:nth-child(${i}) td:nth-child(1) input`).fill('');
          await page.locator(`#form2 table tbody tr:nth-child(${i}) td:nth-child(2) input`).fill('');
        }
      }
      await page.locator('#form2 .c-conversionArea .ladda-button').click();
      await page.waitForLoadState('load');
    }

    // EA0807-UC01-T01 Register new master data
    await goToMtbSex();

    // Find the empty row (last row should be empty for new input)
    const totalRows = await page.locator('#form2 table tbody tr').count();
    const emptyRow = totalRows; // last row

    await page.locator(`#form2 table tbody tr:nth-child(${emptyRow}) td:nth-child(1) input`).fill('3');
    await page.locator(`#form2 table tbody tr:nth-child(${emptyRow}) td:nth-child(2) input`).fill('無回答');

    await page.locator('#form2 .c-conversionArea .ladda-button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました');

    // EA0807-UC01-T02 Verify on customer new page
    await page.goto(`/${adminRoute}/customer/new`);
    await page.waitForLoadState('load');
    await expect(page.locator('#customer_form #admin_customer_sex')).toContainText('無回答');

    // EA0807-UC02-T01 Edit master data: change "無回答" to "その他"
    await goToMtbSex();
    // Find the row with id=3 (our test entry)
    const editRows = await page.locator('#form2 table tbody tr').count();
    let targetRow = -1;
    for (let i = 1; i <= editRows; i++) {
      const idVal = await page.locator(`#form2 table tbody tr:nth-child(${i}) td:nth-child(1) input`).inputValue();
      if (idVal === '3') { targetRow = i; break; }
    }
    expect(targetRow).toBeGreaterThan(0);

    await page.locator(`#form2 table tbody tr:nth-child(${targetRow}) td:nth-child(2) input`).fill('その他');
    await page.locator('#form2 .c-conversionArea .ladda-button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました');

    await page.goto(`/${adminRoute}/customer/new`);
    await page.waitForLoadState('load');
    await expect(page.locator('#customer_form #admin_customer_sex')).toContainText('その他');

    // EA0807-UC03-T01 Edit validation error (empty name with non-empty id)
    await goToMtbSex();
    const valRows = await page.locator('#form2 table tbody tr').count();
    let valTargetRow = -1;
    for (let i = 1; i <= valRows; i++) {
      const idVal = await page.locator(`#form2 table tbody tr:nth-child(${i}) td:nth-child(1) input`).inputValue();
      if (idVal === '3') { valTargetRow = i; break; }
    }
    expect(valTargetRow).toBeGreaterThan(0);

    await page.locator(`#form2 table tbody tr:nth-child(${valTargetRow}) td:nth-child(2) input`).fill('');
    await page.locator('#form2 .c-conversionArea .ladda-button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.invalid-feedback')).toContainText('入力されていません');

    // EA0807-UC04-T01 Delete master data (clear both id and name)
    await goToMtbSex();
    const delRows = await page.locator('#form2 table tbody tr').count();
    let delTargetRow = -1;
    for (let i = 1; i <= delRows; i++) {
      const idVal = await page.locator(`#form2 table tbody tr:nth-child(${i}) td:nth-child(1) input`).inputValue();
      if (idVal === '3') { delTargetRow = i; break; }
    }
    expect(delTargetRow).toBeGreaterThan(0);

    await page.locator(`#form2 table tbody tr:nth-child(${delTargetRow}) td:nth-child(1) input`).fill('');
    await page.locator(`#form2 table tbody tr:nth-child(${delTargetRow}) td:nth-child(2) input`).fill('');
    await page.locator('#form2 .c-conversionArea .ladda-button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-contentsArea .alert-success')).toContainText('保存しました');
  });

  test('systeminfo_log_display - EA0806-UC02-T01', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/log`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('ログ表示');

    // Verify the log page has the expected form elements
    await expect(page.locator('#admin_system_log_files')).toBeVisible();
    await expect(page.locator('#admin_system_log_line_max')).toBeVisible();
    await expect(page.getByRole('button', { name: '読み込む' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'ダウンロード' })).toBeVisible();

    // Verify a log file is available in the select
    const firstOption = await page.locator('#admin_system_log_files option:nth-child(1)').textContent();
    expect(firstOption!.trim().length).toBeGreaterThan(0);
    expect(firstOption).toMatch(/\.log$/);

    // Verify the log viewer area exists
    await expect(page.locator('.c-contentsArea .log-viewer')).toBeVisible();
  });

  test('systeminfo_ログ表示_異常 - EA0806-UC02-T02/T03/T04', async ({ page }) => {
    // Test with line_max = 0
    await page.goto(`/${adminRoute}/setting/system/log`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('ログ表示');

    await page.locator('#admin_system_log_line_max').fill('0');
    await page.locator('#form1 button.btn-ec-conversion').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#form1 .invalid-feedback')).toContainText('エラー');

    // Test with line_max = -1
    await page.goto(`/${adminRoute}/setting/system/log`);
    await page.waitForLoadState('load');

    await page.locator('#admin_system_log_line_max').fill('-1');
    await page.locator('#form1 button.btn-ec-conversion').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#form1 .invalid-feedback')).toContainText('エラー');

    // Test with line_max = 'a' (non-numeric)
    await page.goto(`/${adminRoute}/setting/system/log`);
    await page.waitForLoadState('load');

    await page.locator('#admin_system_log_line_max').fill('a');
    await page.locator('#form1 button.btn-ec-conversion').click();
    await page.waitForLoadState('load');
    await expect(page.locator('#form1 .invalid-feedback')).toContainText('エラー');
  });

  test('systeminfo_member_self_delete_protection - EA0802-UC01-T07', async ({ page }) => {
    await page.goto(`/${adminRoute}/setting/system/member`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('メンバー管理');

    // The currently logged-in admin should not be able to delete themselves.
    // The delete button for the logged-in user should have an empty href (disabled).
    const rows = await page.locator('.c-primaryCol .card-body table tbody tr').count();
    expect(rows).toBeGreaterThanOrEqual(1);

    // Find the row for the logged-in admin (管理者) - check each row's delete button
    let foundSelfProtection = false;
    for (let i = 1; i <= rows; i++) {
      const deleteBtn = page.locator(`.c-primaryCol .card-body table tbody tr:nth-child(${i}) td:nth-child(6) .action-delete`);
      if (await deleteBtn.count() > 0) {
        const href = await deleteBtn.getAttribute('href');
        if (href === '' || href === null) {
          // This is the self-delete protected row
          foundSelfProtection = true;
          break;
        }
      } else {
        // No delete button at all means it's self-protected
        foundSelfProtection = true;
        break;
      }
    }
    expect(foundSelfProtection).toBe(true);
  });

  test('systeminfo_login_history - EA0808-UC01-T01', async ({ page }) => {
    // Search for 'admin' login history
    await page.goto(`/${adminRoute}/setting/system/login_history`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('ログイン履歴');

    await page.locator('#admin_search_login_history_multi').fill('admin');
    await page.locator('#search_form .c-outsideBlock__contents button').click();
    await page.waitForLoadState('load');

    // Verify first result contains 'admin' and '成功'
    await expect(page.locator('#search_form table tbody tr:nth-child(1) td:nth-child(2)')).toContainText('admin');
    await expect(page.locator('#search_form table tbody tr:nth-child(1) td:nth-child(5) span')).toContainText('成功');
  });
});
