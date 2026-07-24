import { test, expect, Page } from '@playwright/test';
import fs from 'fs';
import path from 'path';

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
const COOKIE_NAME = 'eccube_cookie_consent';

/**
 * 同意 Cookie の値を取得する（未設定なら null）。
 */
async function getConsentCookie(page: Page): Promise<string | null> {
  const cookies = await page.context().cookies();
  const found = cookies.find((c) => c.name === COOKIE_NAME);
  return found ? found.value : null;
}

/**
 * 管理画面の認証済みコンテキストを生成する（front-tests は admin 認証を持たないため）。
 */
async function withAdmin(page: Page, fn: (adminPage: Page) => Promise<void>): Promise<void> {
  const authFile = path.join(__dirname, '..', '.auth', 'admin.json');
  const storageState = JSON.parse(fs.readFileSync(authFile, 'utf-8'));
  const browser = page.context().browser()!;
  const adminContext = await browser.newContext({ storageState });
  const adminPage = await adminContext.newPage();
  try {
    await fn(adminPage);
  } finally {
    await adminContext.close();
  }
}

/**
 * ToggleSwitchType（Bootstrap スイッチ）の ON/OFF を切り替える。
 * input は視覚的に非表示のため check()/uncheck() は "not visible" で失敗する。
 * 既存の管理画面 E2E と同様に label クリックで切り替える。
 */
async function setToggleSwitch(page: Page, inputId: string, checked: boolean): Promise<void> {
  const input = page.locator(`#${inputId}`);
  if ((await input.isChecked()) !== checked) {
    await page.locator(`label[for="${inputId}"]`).click();
  }
}

/**
 * 管理画面「店舗基本情報」でクッキーポリシー同意機能の ON/OFF を切り替える。
 */
async function setCookieConsentFeature(page: Page, enabled: boolean): Promise<void> {
  await withAdmin(page, async (adminPage) => {
    await adminPage.goto(`/${adminRoute}/setting/shop`);
    await adminPage.waitForLoadState('load');

    await setToggleSwitch(adminPage, 'shop_master_option_cookie_consent', enabled);

    await adminPage.locator('button.ladda-button[type="submit"]').click();
    await adminPage.waitForLoadState('load');
  });
}

/**
 * 管理画面「店舗基本情報」で GA トラッキングID と同意機能 ON/OFF を一度に設定する。
 */
async function configureShop(page: Page, opts: { gaId: string; cookieConsent: boolean }): Promise<void> {
  await withAdmin(page, async (adminPage) => {
    await adminPage.goto(`/${adminRoute}/setting/shop`);
    await adminPage.waitForLoadState('load');

    await adminPage.locator('#shop_master_ga_id').fill(opts.gaId);
    await setToggleSwitch(adminPage, 'shop_master_option_cookie_consent', opts.cookieConsent);

    await adminPage.locator('button.ladda-button[type="submit"]').click();
    await adminPage.waitForLoadState('load');
  });
}

/**
 * 現在ページの GA 読み込み状態を取得する。
 * gtag / スクリプトタグは同期的に定義・注入されるため、外部ネットワークに依存せず判定できる。
 */
async function gaState(page: Page): Promise<{ gtag: boolean; script: boolean; banner: boolean }> {
  return await page.evaluate(() => ({
    gtag: typeof (window as any).gtag !== 'undefined',
    script: !!document.querySelector('script[src*="googletagmanager.com/gtag/js"]'),
    banner: !!document.querySelector('#cookie-consent-banner'),
  }));
}

const TEST_GA_ID = 'G-E2ECOOKIE01';

test.describe('Front Cookie Consent', () => {
  test.describe.configure({ mode: 'serial' });

  test.beforeAll(async ({ browser }) => {
    // 機能はオプトイン（既定 OFF）のため、バナー・設定ページを検証する本ブロックでは ON にする
    const context = await browser.newContext();
    const page = await context.newPage();
    try {
      await setCookieConsentFeature(page, true);
    } finally {
      await context.close();
    }
  });

  test.afterAll(async ({ browser }) => {
    // テスト後は既定（OFF）へ戻す
    const context = await browser.newContext();
    const page = await context.newPage();
    try {
      await setCookieConsentFeature(page, false);
    } finally {
      await context.close();
    }
  });

  test.beforeEach(async ({ context }) => {
    // 各テストは未同意状態から開始する
    await context.clearCookies();
  });

  test('未同意のときフロントに同意バナーが表示される', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('load');

    await expect(page.locator('#cookie-consent-banner')).toBeVisible();
  });

  test('「同意する」で Cookie=accepted・バナーがリロードなしで非表示になる', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('load');

    await page.locator('#cookie-consent-accept').click();

    await expect(page.locator('#cookie-consent-banner')).toBeHidden();
    expect(await getConsentCookie(page)).toBe('accepted');
  });

  test('「拒否する」で Cookie=rejected になる', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('load');

    await page.locator('#cookie-consent-reject').click();

    await expect(page.locator('#cookie-consent-banner')).toBeHidden();
    expect(await getConsentCookie(page)).toBe('rejected');
  });

  test('「閉じる(×)」では Cookie が設定されず、次回アクセスで再表示される', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('load');

    await page.locator('#cookie-consent-close').click();
    await expect(page.locator('#cookie-consent-banner')).toBeHidden();
    expect(await getConsentCookie(page)).toBeNull();

    // 再訪で再表示
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('#cookie-consent-banner')).toBeVisible();
  });

  test('クッキー設定ページ（ゲスト）で状態確認・変更できる', async ({ page }) => {
    await page.goto('/cookie-consent');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-pageHeader h1')).toContainText('クッキー設定');

    // 同意を選択して保存
    await page.locator('#consent_accepted').check();
    await page.locator('#cookie-consent-save').click();

    // 保存 API（Ajax）の Set-Cookie 反映を待つ（成功後の自動リロードに依存しない）
    await expect.poll(async () => await getConsentCookie(page)).toBe('accepted');
  });

  test('クッキーポリシーページが表示される', async ({ page }) => {
    await page.goto('/help/cookie-policy');
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-pageHeader h1')).toContainText('クッキーポリシー');
  });

  test('フッターのクッキー設定リンクから設定ページへ遷移できる（同意後の再設定導線）', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('load');

    const link = page.locator('.ec-footerNavi__link a[href*="/cookie-consent"]');
    await expect(link).toBeVisible();
    await link.click();
    await page.waitForLoadState('load');

    expect(new URL(page.url()).pathname).toBe('/cookie-consent');
    await expect(page.locator('.ec-pageHeader h1')).toContainText('クッキー設定');
  });

  test('同意更新APIはCSRFトークン不正時に403で拒否する', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('load');

    // 不正な _token を載せて更新 API を直接叩く（正しい CSRF ヘッダは付与しない）
    const status = await page.evaluate(async () => {
      const res = await fetch('/cookie-consent/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'consent_status=accepted&source=popup&_token=invalid-token',
      });
      return res.status;
    });

    expect(status).toBe(403);
    // 拒否されたため Cookie は設定されない
    expect(await getConsentCookie(page)).toBeNull();
  });
});

test.describe('Front Cookie Consent - 管理画面 ON/OFF', () => {
  test.describe.configure({ mode: 'serial' });

  test.afterAll(async ({ browser }) => {
    // テスト後は既定（OFF）へ戻す
    const context = await browser.newContext();
    const page = await context.newPage();
    try {
      await setCookieConsentFeature(page, false);
    } finally {
      await context.close();
    }
  });

  test('OFF にするとバナー非表示・設定ページはトップへリダイレクトする', async ({ page }) => {
    await setCookieConsentFeature(page, false);

    await page.context().clearCookies();
    await page.goto('/');
    await page.waitForLoadState('load');
    // バナー要素自体がレンダリングされない
    await expect(page.locator('#cookie-consent-banner')).toHaveCount(0);

    // 設定ページはトップへリダイレクト
    await page.goto('/cookie-consent');
    await page.waitForLoadState('load');
    expect(new URL(page.url()).pathname).toBe('/');
  });

  test('ON に戻すとバナーが再表示される', async ({ page }) => {
    await setCookieConsentFeature(page, true);

    await page.context().clearCookies();
    await page.goto('/');
    await page.waitForLoadState('load');
    await expect(page.locator('#cookie-consent-banner')).toBeVisible();
  });
});

test.describe('Front Cookie Consent - Google Analytics 連動', () => {
  test.describe.configure({ mode: 'serial' });

  test.beforeEach(async ({ context }) => {
    await context.clearCookies();
  });

  test.afterAll(async ({ browser }) => {
    // テスト後は ga_id を空・機能を既定（OFF）へ戻す
    const context = await browser.newContext();
    const page = await context.newPage();
    try {
      await configureShop(page, { gaId: '', cookieConsent: false });
    } finally {
      await context.close();
    }
  });

  test('機能ON+ga_id: 未同意ではGA未読込、同意でリロードなし読込', async ({ page }) => {
    await configureShop(page, { gaId: TEST_GA_ID, cookieConsent: true });

    await page.goto('/');
    await page.waitForLoadState('load');

    // 未同意: gtag もスクリプトも無い（ローダー関数のみ定義済み）
    let st = await gaState(page);
    expect(st.gtag).toBe(false);
    expect(st.script).toBe(false);
    expect(st.banner).toBe(true);

    // 同意 → リロードなしで GA が読み込まれる（公開イベントフック経由）
    await page.locator('#cookie-consent-accept').click();
    await expect(page.locator('#cookie-consent-banner')).toBeHidden();

    await expect.poll(async () => (await gaState(page)).gtag).toBe(true);
    st = await gaState(page);
    expect(st.script).toBe(true);
    expect(await getConsentCookie(page)).toBe('accepted');
  });

  test('機能ON+ga_id: 拒否ではGAは読み込まれない', async ({ page }) => {
    await configureShop(page, { gaId: TEST_GA_ID, cookieConsent: true });

    await page.goto('/');
    await page.waitForLoadState('load');

    await page.locator('#cookie-consent-reject').click();
    await expect(page.locator('#cookie-consent-banner')).toBeHidden();

    const st = await gaState(page);
    expect(st.gtag).toBe(false);
    expect(st.script).toBe(false);
    expect(await getConsentCookie(page)).toBe('rejected');
  });

  test('機能OFF+ga_id: 同意なしでもGAが無条件に読み込まれる（後方互換）', async ({ page }) => {
    await configureShop(page, { gaId: TEST_GA_ID, cookieConsent: false });

    await page.goto('/');
    await page.waitForLoadState('load');

    const st = await gaState(page);
    expect(st.gtag).toBe(true);
    expect(st.script).toBe(true);
    // 機能 OFF なのでバナーは出ない
    expect(st.banner).toBe(false);
  });

  test('ga_id未設定: 機能ONでもGA出力なし・バナーは動作する', async ({ page }) => {
    await configureShop(page, { gaId: '', cookieConsent: true });

    await page.goto('/');
    await page.waitForLoadState('load');

    const st = await gaState(page);
    expect(st.gtag).toBe(false);
    expect(st.script).toBe(false);
    // GA が無くても同意バナーは表示・動作する
    expect(st.banner).toBe(true);
    await expect(page.locator('#cookie-consent-banner')).toBeVisible();
  });
});
