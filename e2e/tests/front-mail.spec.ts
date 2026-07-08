import { test, expect, type APIRequestContext } from '@playwright/test';

/**
 * mailpit の REST API(既定 8025) からメールを読む。
 * SMTP(1025) で受信したメールを HTTP API で参照でき、Codeception の
 * MailCatcher モジュール(seeInLastEmail / grabFromLastEmail) 相当を Playwright で行う。
 */
const MAILPIT_URL = process.env.MAILPIT_URL || 'http://127.0.0.1:8025';

async function clearMailbox(request: APIRequestContext) {
  await request.delete(`${MAILPIT_URL}/api/v1/messages`);
}

/** 宛先(To)と件名の部分一致でメッセージ ID を探す。無ければ null。 */
async function findMessageId(request: APIRequestContext, to: string, subjectPart: string): Promise<string | null> {
  const res = await request.get(`${MAILPIT_URL}/api/v1/messages`);
  if (!res.ok()) return null;
  const data = await res.json();
  const msg = (data.messages ?? []).find(
    (m: { To?: { Address: string }[]; Subject?: string }) =>
      (m.To ?? []).some((t) => t.Address === to) && String(m.Subject ?? '').includes(subjectPart),
  );
  return msg ? msg.ID : null;
}

/** メッセージ本文(text/plain)を取得する。 */
async function getMessageText(request: APIRequestContext, id: string): Promise<string> {
  const res = await request.get(`${MAILPIT_URL}/api/v1/message/${id}`);
  const data = await res.json();
  return String(data.Text ?? '');
}

test.describe('会員登録メール (EF04)', () => {
  test('EF0401-UC01-T20 会員登録 アクティベートメールで本会員化', async ({ page, request }) => {
    await clearMailbox(request);

    const newEmail = `mailpit_${Date.now()}@example.com`;

    // 会員登録フォーム入力
    await page.goto('/entry');
    await page.waitForLoadState('load');
    await page.locator('#entry_name_name01').fill('姓');
    await page.locator('#entry_name_name02').fill('名');
    await page.locator('#entry_kana_kana01').fill('セイ');
    await page.locator('#entry_kana_kana02').fill('メイ');
    await page.locator('#entry_postal_code').fill('530-0001');
    await page.locator('#entry_address_pref').selectOption({ value: '27' });
    // 郵便番号→住所の自動入力(yubinbango, 外部JS)完了を待つ
    await page.waitForTimeout(1000);
    await page.locator('#entry_address_addr01').fill('大阪市北区');
    await page.locator('#entry_address_addr02').fill('梅田2-4-9');
    await page.locator('#entry_phone_number').fill('111-111-111');
    await page.locator('#entry_email_first').fill(newEmail);
    await page.locator('#entry_email_second').fill(newEmail);
    await page.locator('#entry_plain_password_first').fill('password1234');
    await page.locator('#entry_plain_password_second').fill('password1234');
    await page.locator('#entry_job').selectOption({ value: '1' });
    await page.locator('#entry_user_policy_check').check();

    // 確認画面へ → 「会員登録をする」
    await page.locator('button.ec-blockBtn--action[type="submit"]').click();
    await page.waitForLoadState('load');
    await expect(page.locator('.ec-registerRole')).toContainText(newEmail);
    await page.locator('.ec-registerRole form button.ec-blockBtn--action').click();
    await page.waitForLoadState('load');

    // 仮会員登録の確認メールが mailpit に届くのを待つ
    await expect
      .poll(() => findMessageId(request, newEmail, '会員登録のご確認'), {
        timeout: 30_000,
        message: '仮会員登録の確認メールが mailpit に届かない',
      })
      .not.toBeNull();

    const confirmId = (await findMessageId(request, newEmail, '会員登録のご確認'))!;
    const confirmText = await getMessageText(request, confirmId);
    expect(confirmText).toContain('姓 名 様');
    expect(confirmText).toContain('この度は会員登録依頼をいただきまして、有り難うございます。');

    // 本文からアクティベート URL を抽出(Codeception の grabFromLastEmail 相当)
    const matched = confirmText.match(/\/entry\/activate\/[0-9a-zA-Z]+/);
    expect(matched, 'アクティベート URL がメール本文に無い').not.toBeNull();

    // アクティベート URL を踏んで本会員登録を完了する
    await page.goto(matched![0]);
    await page.waitForLoadState('load');
    await expect(page.locator('div.ec-pageHeader h1')).toContainText('新規会員登録(完了)');

    // 本会員登録の完了メールも届く
    await expect
      .poll(() => findMessageId(request, newEmail, '会員登録が完了しました'), {
        timeout: 30_000,
        message: '本会員登録の完了メールが mailpit に届かない',
      })
      .not.toBeNull();

    const completeId = (await findMessageId(request, newEmail, '会員登録が完了しました'))!;
    const completeText = await getMessageText(request, completeId);
    expect(completeText).toContain('姓 名 様');
    expect(completeText).toContain('本会員登録が完了いたしました。');
  });
});
