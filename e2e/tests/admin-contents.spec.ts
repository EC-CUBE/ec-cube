import { test, expect } from '@playwright/test';
import path from 'path';
import fs from 'fs';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';

// ---------------------------------------------------------------------------
// News management
// ---------------------------------------------------------------------------
test.describe.serial('News management', () => {
  let createdNewsTitle: string;

  test('create news', async ({ page }) => {
    // Go to news list and click new
    await page.goto(`/${adminRoute}/content/news`);
    await page.waitForLoadState('load');
    await page.locator('#addNew').click();
    await page.waitForLoadState('load');

    // Fill in date (datetime-local requires YYYY-MM-DDTHH:mm format)
    const today = new Date().toISOString().slice(0, 10); // YYYY-MM-DD
    await page.locator('#admin_news_publish_date').fill(`${today}T00:00`);

    // Fill in title and body
    await page.locator('#admin_news_title').fill('news_title1');
    await page.locator('#admin_news_description').fill('newsnewsnewsnewsnews');

    // Submit - form redirects to news list
    await Promise.all([
      page.waitForURL(/\/content\/news(\/|$)/),
      page.locator('.c-contentsArea .c-contentsArea__cols .c-conversionArea .btn-ec-conversion').click(),
    ]);
    await expect(page.locator('.c-container .c-contentsArea .alert-success')).toContainText('保存しました');
  });

  test('edit news', async ({ page }) => {
    // Go to news list
    await page.goto(`/${adminRoute}/content/news`);
    await page.waitForLoadState('load');

    // Click edit on the 2nd item (nth-child(2) = first data row, since header is nth-child(1);
    // but we need the 2nd data row = nth-child(3) since we created a new one)
    // Actually, the Codeception test edits row 2 (1-indexed) which is the second <li> = first data row
    await page.locator('.c-contentsArea .list-group > li:nth-child(2) a[aria-label="編集"]').click();
    await page.waitForLoadState('load');

    createdNewsTitle = 'news_title_' + Date.now();
    await page.locator('#admin_news_title').fill(createdNewsTitle);
    await Promise.all([
      page.waitForURL(/\/content\/news(\/|$)/),
      page.locator('.c-contentsArea .c-contentsArea__cols .c-conversionArea .btn-ec-conversion').click(),
    ]);
    await expect(page.locator('.c-container .c-contentsArea .alert-success')).toContainText('保存しました');

    // Verify title was updated
    await page.goto(`/${adminRoute}/content/news`);
    await page.waitForLoadState('load');
    const titleText = await page.locator('.c-contentsArea .list-group > li:nth-child(2) .col.d-flex a:first-child').textContent();
    expect(titleText?.trim()).toBe(createdNewsTitle);
  });

  test('delete news', async ({ page }) => {
    // Go to news list
    await page.goto(`/${adminRoute}/content/news`);
    await page.waitForLoadState('load');

    // Click delete on the 2nd item (first data row) - target only the modal trigger
    await page.locator('.c-contentsArea .list-group > li:nth-child(2) [data-bs-original-title="削除"] a[data-bs-toggle="modal"]').click();

    // Accept delete modal
    await page.locator('.modal.show .btn-ec-delete').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-container .c-contentsArea .alert-success')).toContainText('削除しました');

    // Verify title is no longer at row 2
    if (createdNewsTitle) {
      const newTitle = await page.locator('.c-contentsArea .list-group > li:nth-child(2) .col.d-flex a:first-child').textContent();
      expect(newTitle?.trim()).not.toBe(createdNewsTitle);
    }
  });
});

// ---------------------------------------------------------------------------
// File management
// ---------------------------------------------------------------------------
test.describe.serial('File management', () => {
  test('upload, view and delete file', async ({ page }) => {
    // Prepare a test file to upload
    const fixtureDir = path.join(__dirname, '..', 'fixtures');
    const uploadFilePath = path.join(fixtureDir, 'upload.txt');
    if (!fs.existsSync(uploadFilePath)) {
      fs.writeFileSync(uploadFilePath, 'This is uploaded file.');
    }

    await page.goto(`/${adminRoute}/content/file_manager`);
    await page.waitForLoadState('load');

    // Upload file
    await page.locator('#form_file').setInputFiles(uploadFilePath);
    await page.locator('#upload_box__file a.action-upload').click();
    await page.waitForLoadState('load');

    // Verify file appears in list
    await expect(page.locator('#fileList table > tbody')).toContainText('upload.txt');

    // Find the row with upload.txt and click the view link
    const uploadRow = page.locator('#fileList table > tbody > tr', { hasText: 'upload.txt' });
    const viewLink = uploadRow.locator('a.action-view');
    const href = await viewLink.getAttribute('href');
    expect(href).toBeTruthy();

    // Verify file content via view
    const [newPage] = await Promise.all([
      page.context().waitForEvent('page'),
      viewLink.click(),
    ]);
    await newPage.waitForLoadState('load');
    await expect(newPage.locator('body')).toContainText('This is uploaded file.');
    await newPage.close();

    // Delete the uploaded file
    await uploadRow.locator('a.action-delete').click();
    // Accept the delete modal
    await page.locator('.modal.show .btn-ec-delete, .modal.show a.btn-ec-delete').first().click();
    await page.waitForLoadState('load');

    // Verify file is gone
    await expect(page.locator('#fileList table > tbody')).not.toContainText('upload.txt');
  });

  test('create and delete folder', async ({ page }) => {
    await page.goto(`/${adminRoute}/content/file_manager`);
    await page.waitForLoadState('load');

    // Create folder
    await page.locator('#form_create_file').fill('testfolder1');
    await page.locator('#form1 a.action-create').click();
    await page.waitForLoadState('load');

    // Verify folder appears
    await expect(page.locator('#fileList table > tbody')).toContainText('testfolder1');

    // Click into folder
    const folderRow = page.locator('#fileList table > tbody > tr', { hasText: 'testfolder1' });
    await folderRow.locator('td:nth-child(2) a').click();
    await page.waitForLoadState('load');

    // Verify breadcrumb shows folder name
    await expect(page.locator('#bread')).toContainText('testfolder1');

    // Go back to root
    await page.goto(`/${adminRoute}/content/file_manager`);
    await page.waitForLoadState('load');

    // Delete folder
    const folderRow2 = page.locator('#fileList table > tbody > tr', { hasText: 'testfolder1' });
    await folderRow2.locator('a.action-delete').click();
    await page.locator('.modal.show .btn-ec-delete, .modal.show a.btn-ec-delete').first().click();
    await page.waitForLoadState('load');

    // Verify folder is gone
    await expect(page.locator('#fileList table > tbody')).not.toContainText('testfolder1');
  });

  test('reject php file upload', async ({ page }) => {
    // Create a temporary php file
    const fixtureDir = path.join(__dirname, '..', 'fixtures');
    const phpFilePath = path.join(fixtureDir, 'upload.php');
    if (!fs.existsSync(phpFilePath)) {
      fs.writeFileSync(phpFilePath, '<?php echo "test"; ?>');
    }

    await page.goto(`/${adminRoute}/content/file_manager`);
    await page.waitForLoadState('load');

    await page.locator('#form_file').setInputFiles(phpFilePath);
    await page.locator('#upload_box__file a.action-upload').click();

    // Expect error message
    await expect(page.locator('#form1')).toContainText('アップロードできないファイル拡張子です。');
  });
});

// ---------------------------------------------------------------------------
// Page management
// ---------------------------------------------------------------------------
test.describe.serial('Page management', () => {
  const pageName = 'testpage_' + Date.now();

  test('create page', async ({ page }) => {
    await page.goto(`/${adminRoute}/content/page/new`);
    await page.waitForLoadState('load');

    // Verify default template content via ace editor
    await page.waitForFunction(() => !!(window as any).ace?.edit);
    const defaultContent = await page.evaluate(() => {
      return (window as any).ace.edit('editor').getValue();
    });
    expect(defaultContent).toContain("{% extends 'default_frame.twig' %}");
    expect(defaultContent).toContain('{% block main %}');

    // Fill in fields
    await page.locator('#main_edit_name').fill(pageName);
    await page.locator('#main_edit_file_name').fill(pageName);
    await page.locator('#main_edit_url').fill(pageName);

    // Set content via ace editor
    await page.evaluate((name) => {
      (window as any).ace.edit('editor').setValue(name);
    }, pageName);

    // Select layout
    await page.locator('#main_edit_PcLayout').selectOption({ label: '下層ページ用レイアウト' });

    // Submit
    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Verify page is accessible on front
    await page.goto(`/user_data/${pageName}`);
    await page.waitForLoadState('load');
    await expect(page.locator('body')).toContainText(pageName);
  });

  test('edit page', async ({ page }) => {
    // Navigate to page management and find the page
    await page.goto(`/${adminRoute}/content/page`);
    await page.waitForLoadState('load');

    // Click the page name link
    await page.locator(`a:has-text("${pageName}")`).first().click();
    await page.waitForLoadState('load');

    // Update content
    await page.waitForFunction(() => !!(window as any).ace?.edit);
    await page.evaluate(() => {
      (window as any).ace.edit('editor').setValue("{% extends 'default_frame.twig' %}");
    });

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Verify front page shows the layout footer
    await page.goto(`/user_data/${pageName}`);
    await page.waitForLoadState('load');
    await expect(page.locator('footer.ec-layoutRole__footer')).toBeVisible();
  });

  test('delete page', async ({ page }) => {
    await page.goto(`/${adminRoute}/content/page`);
    await page.waitForLoadState('load');

    // Find the row containing our page and click delete
    const row = page.locator(`.table.table-sm tbody tr`, { hasText: pageName });
    await row.locator('a[data-bs-toggle="modal"]').click();

    // Accept delete modal (modal may be rendered at page level, not inside the row)
    await page.locator('.modal.show a.btn-ec-delete').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('削除しました');

    // Verify page is 404
    await page.goto(`/user_data/${pageName}`);
    await page.waitForLoadState('load');
    await expect(page).toHaveTitle(/ページがみつかりません/);
  });
});

// ---------------------------------------------------------------------------
// Block management
// ---------------------------------------------------------------------------
test.describe.serial('Block management', () => {
  const blockName = 'testblock_' + Date.now();

  test('create block', async ({ page }) => {
    await page.goto(`/${adminRoute}/content/block/new`);
    await page.waitForLoadState('load');

    await page.locator('#block_name').fill(blockName);
    await page.locator('#block_file_name').fill(blockName);

    // Wait for ace editor to be ready, then set content
    await page.waitForFunction(() => !!(window as any).ace?.edit);
    await page.evaluate((name) => {
      (window as any).ace.edit('editor').setValue('<div id="' + name + '">block_content_1</div>');
    }, blockName);

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('edit block', async ({ page }) => {
    // Go to block list and find our block
    await page.goto(`/${adminRoute}/content/block`);
    await page.waitForLoadState('load');

    // Click the edit link for our block (find the list item containing block name)
    const blockItem = page.locator('.c-contentsArea .list-group > li', { hasText: blockName });
    await blockItem.locator('a[aria-label="編集"]').click();
    await page.waitForLoadState('load');

    // Update content
    await page.waitForFunction(() => !!(window as any).ace?.edit);
    await page.evaluate((name) => {
      (window as any).ace.edit('editor').setValue('<div id="' + name + '">block_content_updated</div>');
    }, blockName);

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');
  });

  test('delete block', async ({ page }) => {
    await page.goto(`/${adminRoute}/content/block`);
    await page.waitForLoadState('load');

    // Find the block and click delete
    const blockItem = page.locator('.c-contentsArea .list-group > li', { hasText: blockName });
    await blockItem.locator('[data-bs-original-title="削除"] a').click();

    // Accept modal
    await page.locator('.modal.show .btn-ec-delete').click();
    await page.waitForLoadState('load');

    // Verify block is no longer listed
    await expect(page.locator('.c-contentsArea .list-group')).not.toContainText(blockName);
  });
});

// ---------------------------------------------------------------------------
// CSS management
// ---------------------------------------------------------------------------
test.describe.serial('CSS management', () => {
  test('apply and revert CSS', async ({ page }) => {
    // Apply CSS that hides headerNavi
    await page.goto(`/${adminRoute}/content/css`);
    await page.waitForLoadState('load');

    await page.waitForFunction(() => !!(window as any).ace?.edit);
    await page.evaluate(() => {
      (window as any).ace.edit('editor').setValue('.ec-headerNaviRole { display: none; }');
    });
    await page.locator('#save-button').click();
    await page.waitForLoadState('load');

    // Verify on front page that headerNavi is hidden
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-headerNaviRole')).toBeHidden();

    // Revert CSS
    await page.goto(`/${adminRoute}/content/css`);
    await page.waitForLoadState('load');

    await page.waitForFunction(() => !!(window as any).ace?.edit);
    await page.evaluate(() => {
      (window as any).ace.edit('editor').setValue('.ec-headerNaviRole { }');
    });
    await page.locator('#save-button').click();
    await page.waitForLoadState('load');

    // Verify on front page that headerNavi is visible again
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-headerNaviRole')).toBeVisible();
  });
});

// ---------------------------------------------------------------------------
// JavaScript management
// ---------------------------------------------------------------------------
test.describe.serial('JavaScript management', () => {
  test('apply and revert JavaScript', async ({ page }) => {
    const testText = 'JSテストテキスト_' + Date.now();

    // Apply JS that appends text
    await page.goto(`/${adminRoute}/content/js`);
    await page.waitForLoadState('load');

    await page.waitForFunction(() => !!(window as any).ace?.edit);
    await page.evaluate((txt) => {
      (window as any).ace.edit('editor').setValue("$('.ec-headerNaviRole').append('" + txt + "');");
    }, testText);
    await page.locator('#save-button').click();
    await page.waitForLoadState('load');

    // Verify on front page
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-headerNaviRole')).toContainText(testText);

    // Revert JS
    await page.goto(`/${adminRoute}/content/js`);
    await page.waitForLoadState('load');

    await page.waitForFunction(() => !!(window as any).ace?.edit);
    await page.evaluate(() => {
      (window as any).ace.edit('editor').setValue('/* */');
    });
    await page.locator('#save-button').click();
    await page.waitForLoadState('load');

    // Verify text is gone
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-headerNaviRole')).not.toContainText(testText);
  });
});

// ---------------------------------------------------------------------------
// Layout management (create, drag block, verify, cleanup)
// ---------------------------------------------------------------------------
test.describe.serial('Layout management', () => {
  const layoutName = 'layout_test_' + Date.now();
  const pageName = 'page_test_' + Date.now();

  test('create layout with drag-and-drop block - EA0605-UC01-T01/T02/T03', async ({ page }) => {
    test.setTimeout(180_000);

    /**
     * Helper: Move a block by name to a target position area.
     * Uses DOM manipulation + updateUpDown() to update hidden form inputs,
     * matching the approach of layout_design.js's sortable update handler.
     */
    async function moveBlockToPosition(blockName: string, targetPositionId: string) {
      await page.evaluate(({ blockName, targetPositionId }) => {
        // Find the block element containing the specified block name
        const blocks = document.querySelectorAll('[id^="detail_box__layout_item"]');
        let blockEl: Element | null = null;
        blocks.forEach(el => {
          if (el.querySelector('span')?.textContent?.trim() === blockName) {
            blockEl = el;
          }
        });
        if (!blockEl) throw new Error(`Block "${blockName}" not found`);

        const sourceParent = (blockEl as Element).parentElement;
        const target = document.getElementById(targetPositionId);
        if (!target) throw new Error(`Target "${targetPositionId}" not found`);

        // Remove placeholder from target if present
        const placeholder = target.querySelector('.target-placeholder');
        if (placeholder) placeholder.remove();

        // Move the DOM element to the target
        target.appendChild(blockEl);

        // Add placeholder back to source if it has no more blocks
        if (sourceParent && sourceParent.querySelectorAll('.block').length === 0) {
          const tplEl = document.getElementById('target-placeholder');
          if (tplEl) {
            sourceParent.insertAdjacentHTML('beforeend', tplEl.innerHTML);
          }
        }

        // Update the hidden form inputs via the global updateUpDown function
        const updateUpDown = (window as any).updateUpDown;
        if (updateUpDown) {
          updateUpDown(target);
          if (sourceParent) {
            updateUpDown(sourceParent);
          }
        }
      }, { blockName, targetPositionId });
    }

    // --- CREATE LAYOUT ---
    await page.goto(`/${adminRoute}/content/layout/new`);
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('レイアウト管理');

    // Fill in layout name and device type
    await page.locator('#admin_layout_name').fill(layoutName);
    await page.locator('#admin_layout_DeviceType').selectOption({ label: 'PC' });

    // Move block "新着情報" to header area (#position_3)
    await moveBlockToPosition('新着情報', 'position_3');
    await page.waitForTimeout(500);

    // Save layout
    await page.locator('#form1 > div > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // --- CREATE PAGE with this layout ---
    await page.goto(`/${adminRoute}/content/page/new`);
    await page.waitForLoadState('load');

    await page.locator('#main_edit_name').fill(pageName);
    await page.locator('#main_edit_file_name').fill(pageName);
    await page.locator('#main_edit_url').fill(pageName);

    // Select our new layout
    await page.locator('#main_edit_PcLayout').selectOption({ label: layoutName });

    await page.locator('button.ladda-button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // --- VERIFY block appears on front page ---
    await page.goto(`/user_data/${pageName}`);
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-layoutRole__header .ec-newsRole')).toBeVisible();

    // --- EDIT: Move block to footer (#position_10) ---
    await page.goto(`/${adminRoute}/content/layout`);
    await page.waitForLoadState('load');
    await page.locator(`a:has-text("${layoutName}")`).click();
    await page.waitForLoadState('load');

    await moveBlockToPosition('新着情報', 'position_10');
    await page.waitForTimeout(500);

    await page.locator('#form1 > div > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('保存しました');

    // Verify block is in footer now
    await page.goto(`/user_data/${pageName}`);
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-layoutRole__footer .ec-newsRole')).toBeVisible();

    // --- ATTEMPT DELETE LAYOUT (should fail because page uses it) ---
    await page.goto(`/${adminRoute}/content/layout`);
    await page.waitForLoadState('load');

    // Find the card that contains our layout name and click its delete button
    const layoutCard = page.locator(`.card`, { hasText: layoutName });
    await layoutCard.locator('button[data-bs-toggle="modal"][data-bs-target="#DeleteModal"]').click();
    await page.locator('#DeleteModal').waitFor({ state: 'visible' });
    await page.locator('#DeleteModal a.btn-ec-delete').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert')).toContainText('削除できませんでした');

    // --- DELETE PAGE ---
    await page.goto(`/${adminRoute}/content/page`);
    await page.waitForLoadState('load');

    const pageRow = page.locator(`.table.table-sm tbody tr`, { hasText: pageName });
    await pageRow.locator('a[data-bs-toggle="modal"]').click();
    await page.locator('.modal.show a.btn-ec-delete').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('削除しました');

    // --- DELETE LAYOUT ---
    await page.goto(`/${adminRoute}/content/layout`);
    await page.waitForLoadState('load');

    const layoutCard2 = page.locator(`.card`, { hasText: layoutName });
    await layoutCard2.locator('button[data-bs-toggle="modal"][data-bs-target="#DeleteModal"]').click();
    await page.locator('#DeleteModal').waitFor({ state: 'visible' });
    await page.locator('#DeleteModal a.btn-ec-delete').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('削除しました');

    // Verify layout is gone
    await expect(page.locator('.c-contentsArea')).not.toContainText(layoutName);
  });
});

// ---------------------------------------------------------------------------
// Cache management
// ---------------------------------------------------------------------------
test.describe('Cache management', () => {
  test('clear cache', async ({ page }) => {
    // Verify front page works before
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('h1')).toBeVisible();

    // Clear cache
    await page.goto(`/${adminRoute}/content/cache`);
    await page.waitForLoadState('load');
    await page.locator('.c-contentsArea .btn-ec-conversion').click();
    await expect(page.locator('.alert')).toContainText('削除しました');

    // Verify front page still works after cache clear
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('h1')).toBeVisible();
  });
});

// ---------------------------------------------------------------------------
// Maintenance management
// ---------------------------------------------------------------------------
test.describe.serial('Maintenance management', () => {
  test('enable and disable maintenance mode', async ({ page }) => {
    // First, ensure maintenance mode is disabled (in case a previous run left it enabled)
    await page.goto(`/${adminRoute}/content/maintenance`);
    await page.waitForLoadState('load');
    const buttonText = await page.locator('#page_admin_content_maintenance button[type="submit"]').textContent();
    if (buttonText?.trim() === '無効にする') {
      // Maintenance is already enabled, disable it first
      await page.locator('#page_admin_content_maintenance button[type="submit"]').click();
      await page.waitForLoadState('load');
      await expect(page.locator('.alert-success')).toContainText('メンテナンスモードを無効にしました');
    }

    // Enable maintenance mode
    await page.goto(`/${adminRoute}/content/maintenance`);
    await page.waitForLoadState('load');
    await page.locator('#page_admin_content_maintenance button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('メンテナンスモードを有効にしました');

    // As logged-in admin, front page should show maintenance alert but still accessible
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-maintenanceAlert')).toContainText('メンテナンスモードが有効になっています');

    // Disable maintenance mode (go back to admin)
    await page.goto(`/${adminRoute}/content/maintenance`);
    await page.waitForLoadState('load');
    await page.locator('#page_admin_content_maintenance button[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.alert-success')).toContainText('メンテナンスモードを無効にしました');

    // Verify front page no longer shows maintenance alert
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-maintenanceAlert')).not.toBeVisible();
  });
});

// ---------------------------------------------------------------------------
// Layout editor - unused block search
// ---------------------------------------------------------------------------
test.describe('Layout editor - unused block search', () => {
  test('contentsmanagement_検索未使用ブロック - EA0605-UC01-T04', async ({ page }) => {
    // Navigate to layout management and edit the 下層ページ用レイアウト layout
    await page.goto(`/${adminRoute}/content/layout`);
    await page.waitForLoadState('load');

    // Click on the layout named 下層ページ用レイアウト
    await page.locator('a:has-text("下層ページ用レイアウト")').first().click();
    await page.waitForLoadState('load');
    await expect(page.locator('.c-pageTitle')).toContainText('レイアウト管理');

    // Count all unused block items before search
    const initialCount = await page.locator('#unused-block div.sort').count();
    expect(initialCount).toBeGreaterThan(0);

    // Search for 'トピック' in unused block search box
    await page.locator('#search-block').fill('トピック');
    await page.waitForTimeout(500);

    // Only matching blocks should be visible
    const visibleBlocks = page.locator('#unused-block div.sort:visible');
    const filteredCount = await visibleBlocks.count();
    expect(filteredCount).toBe(1);

    // Clear the search to restore all blocks
    await page.locator('#search-block').fill('');
    await page.waitForTimeout(500);

    const restoredCount = await page.locator('#unused-block div.sort:visible').count();
    expect(restoredCount).toBe(initialCount);
  });
});
