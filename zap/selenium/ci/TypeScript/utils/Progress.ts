import { Page } from '@playwright/test';
export const intervalRepeater = async (callback: any, interval: number, page: Page) => {
  let progress = await callback();

  console.log('\n::group::Active Scan progress...');
  while (progress < 100) {
    progress = await callback();
    console.log(`${progress}%`);
    await page.waitForTimeout(interval);
  }
  console.log('::endgroup::');
}
