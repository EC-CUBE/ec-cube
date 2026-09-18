import { test, expect } from '@playwright/test';

/**
 * MCP サーバの End-to-End。 管理画面でトークンを発行し、 その Bearer で HTTP transport
 * (`/{admin}/mcp`) に JSON-RPC を投げて、 一覧ツールがサマリ射影で返ることを確認する。
 *
 * PHPUnit の `mcp` グループ (契約テスト) が触らない「トークン発行 UI → 実 HTTP でのツール呼び出し」
 * の経路を、 Api44 導入済み環境で 1 本通す。 OAuth の同意フロー (DCR/PKCE) は HTTPS 必須のため
 * ここでは踏まず、 管理画面のトークン発行 UI 経由で Bearer を得る。
 */

const adminRoute = process.env.ECCUBE_ADMIN_ROUTE || 'admin';
const mcpEndpoint = `/${adminRoute}/mcp`;

/** MCP へ JSON-RPC を POST する。 token / session id を渡すと該当ヘッダを付与。 */
async function mcpPost(
  request: import('@playwright/test').APIRequestContext,
  body: unknown,
  opts: { token?: string; sessionId?: string } = {},
) {
  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
    // MCP の HTTP transport は SSE も選択肢に含む Accept を要求する
    Accept: 'application/json, text/event-stream',
  };
  if (opts.token) {
    headers['Authorization'] = `Bearer ${opts.token}`;
  }
  if (opts.sessionId) {
    headers['Mcp-Session-Id'] = opts.sessionId;
  }

  return request.post(mcpEndpoint, { headers, data: body });
}

test.describe('MCP サーバ', () => {
  test('トークン発行 UI で発行した Bearer で search_products がサマリ射影を返す', async ({ page }) => {
    // 1. トークン発行 UI でラベル・scope・有効期限を入力して発行
    await page.goto(`/${adminRoute}/api/oauth/mcp/new`);
    await page.waitForLoadState('load');
    await expect(page.locator('#mcp_token_form')).toBeVisible();

    await page.locator('#mcp_token_label').fill('e2e-token');
    // scope: mcp:product:read (expanded=true の複数チェックボックス。 value=mcp:product:read は index 0)
    await page.locator('#mcp_token_scopes_0').check();
    // submit ボタンは dev の Symfony Web Debug Toolbar に覆われ得るため、 form を直接 submit する
    await page.locator('#mcp_token_form').evaluate((f: HTMLFormElement) => f.requestSubmit());
    await page.waitForLoadState('load');

    // 2. 発行画面の textarea から JWT を取得 (発行直後のみ表示される)
    const token = await page.locator('#mcp_token_value').inputValue();
    expect(token, '発行トークンが空').not.toBe('');

    // 3. initialize で handshake が成立する (200 + JSON-RPC result)。 session id はヘッダで返る
    const initRes = await mcpPost(page.request, {
      jsonrpc: '2.0',
      id: 1,
      method: 'initialize',
      params: {
        protocolVersion: '2025-03-26',
        clientInfo: { name: 'e2e', version: '1' },
        capabilities: {},
      },
    }, { token });
    expect(initRes.status(), 'initialize が 200 でない (Bearer 認証を確認)').toBe(200);

    const sessionId = initRes.headers()['mcp-session-id'];
    expect(sessionId, 'initialize が Mcp-Session-Id を返さない').toBeTruthy();

    // 非 initialize リクエストには session id が必須。 まず initialized 通知を送る
    const notifyRes = await mcpPost(page.request, {
      jsonrpc: '2.0',
      method: 'notifications/initialized',
    }, { token, sessionId });
    expect(notifyRes.status()).toBe(202);

    // 4. tools/call で search_products を呼ぶ → items がサマリ射影で返る
    const callRes = await mcpPost(page.request, {
      jsonrpc: '2.0',
      id: 2,
      method: 'tools/call',
      params: { name: 'search_products', arguments: { limit: 5 } },
    }, { token, sessionId });
    expect(callRes.status()).toBe(200);

    // MCP の tool 結果は content[].text に JSON 文字列で入る
    const payload = await callRes.json();
    const text = payload?.result?.content?.[0]?.text;
    expect(text, 'tool 結果に content text が無い').toBeTruthy();
    const data = JSON.parse(text);

    expect(Array.isArray(data.items)).toBe(true);
    expect(data.items.length).toBeGreaterThan(0);

    for (const item of data.items) {
      // サマリ射影のキーだけ: 重量フィールド (description_detail 等) は含まれない
      expect(item).toHaveProperty('id');
      expect(item).toHaveProperty('price');
      expect(item.price).toHaveProperty('min');
      expect(item.price).toHaveProperty('max');
      expect(item).toHaveProperty('stock');
      expect(item).not.toHaveProperty('description_detail');
      expect(item).not.toHaveProperty('ProductClasses');
    }
  });

  test('Bearer なしの /admin/mcp は 401 と resource_metadata を返す', async ({ page }) => {
    const res = await mcpPost(page.request, {
      jsonrpc: '2.0',
      id: 1,
      method: 'initialize',
      params: { protocolVersion: '2025-03-26', clientInfo: { name: 'e2e', version: '1' }, capabilities: {} },
    });

    expect(res.status()).toBe(401);
    expect(res.headers()['www-authenticate'] ?? '').toContain('resource_metadata=');
  });
});
