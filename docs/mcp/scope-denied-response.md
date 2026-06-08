# MCP scope 拒否レスポンスの仕様 (補足)

`ISSUE_mcp_server_final_design.md` §4.3 の補足。 設計時に想定した「HTTP 403 + `insufficient_scope`」を採用しなかった経緯と、 実装で採用した代替仕様の根拠をまとめる。

## 結論

scope 不足時の応答は **HTTP 200 + JSON-RPC `result.isError = true` + `content[0].text` に scope 不足メッセージ** を返す。 HTTP 403 化はしない (できない)。

```json
{
  "jsonrpc": "2.0",
  "id": 3,
  "result": {
    "content": [
      {"type": "text", "text": "Insufficient scope: mcp:order:read"}
    ],
    "isError": true
  }
}
```

## なぜ HTTP 403 化を諦めたか

`symfony/mcp-bundle` (内部で `mcp/sdk`) の `CallToolHandler::handle()` が **Tool 呼び出し中の全例外を catch** し、 JSON-RPC レスポンスに変換してから HTTP 層に渡す。 該当ソース:

```php
// vendor/mcp/sdk/src/Server/Handler/Request/CallToolHandler.php
try {
    $result = $this->referenceHandler->handle($reference, $arguments);
    // ... 成功時
    return new Response($request->getId(), $result);
} catch (ToolCallException $e) {                                  // ← (1) 専用パス
    $errorContent = [new TextContent($e->getMessage())];
    return new Response($request->getId(), CallToolResult::error($errorContent));
} catch (\Throwable $e) {                                         // ← (2) フォールバック
    return Error::forInternalError('Error while executing tool', $request->getId());
}
```

つまり Tool が何の例外を投げても **`kernel.exception` には届かず**、 HTTP は常に 200 で返る。 `kernel.exception` listener で 403 化する案は構造上不可能。

回避策の選択肢と却下理由:

| 案 | 内容 | 却下理由 |
|---|---|---|
| A. `kernel.response` listener で書き換え | JSON-RPC ボディを覗いて isError=true を見つけたら 403 に差し替える | レスポンスが SSE (`text/event-stream`) の場合に成立しない。 mcp-bundle の HTTP transport は streamable HTTP で SSE 経由のパスがあり、 single response で完結しない応答も含む |
| B. mcp-bundle を fork | `CallToolHandler` の catch を override | ライブラリのアップグレード追従コストが永続的に乗る。 採用しない |
| C. mcp-bundle の controller を完全自前化 | `/admin/mcp` の controller を自作し、 Tool 呼び出し前に scope を弾く | MCP プロトコル (initialize / notifications / tools/list / tools/call / SSE) を全て自分で実装することになる。 過大 |

選択肢 (1)「**`ToolCallException` を投げる**」は mcp-bundle が用意している正式な拒否経路。 これに乗せるのが筋。

## 実装

`Eccube\Service\Mcp\ScopeChecker::require()` で `Mcp\Exception\ToolCallException` を投げる:

```php
public function require(string $role): void
{
    if (!$this->authorizationChecker->isGranted($role)) {
        throw new ToolCallException(sprintf('Insufficient scope: %s', $this->roleToScope($role)));
    }
}
```

- role → scope 名変換 (`ROLE_OAUTH2_MCP:ORDER:READ` → `mcp:order:read`) を行い、 OAuth2 仕様のキーで LLM 側に伝える。
- `ToolInvoker` は `ToolCallException` を専用 catch し、 監査ログに `AuditResult::ScopeDenied` を記録してから再 throw する。 mcp-bundle の専用 catch (1) に乗る。

## LLM クライアントから見える挙動

- HTTP は 200 OK で返る (LLM クライアントは「ネットワーク的には成功」と認識)。
- ただし `result.isError = true` を見て「Tool は失敗した」と理解できる。
- `content[0].text` に「Insufficient scope: mcp:order:read」とあるため、 どの scope が不足しているかを読める。
- LLM 側は: (a) 別 token (該当 scope を持つもの) で再試行する、 (b) ユーザーに「この scope が必要」と提示する、 のいずれかが可能。

## 設計 AC との差分

| 項目 | 設計 §4.3 当初案 | 実装 |
|---|---|---|
| HTTP ステータス | 403 | 200 |
| ボディの error key | `error: "insufficient_scope"` | `content[0].text: "Insufficient scope: <scope>"` |
| 必要 scope の伝達 | `required_scope: "mcp:order:read"` | テキスト中の `<scope>` 部分 |
| LLM 側の判別容易性 | HTTP 層で判定可 | `result.isError === true` で判定可 |

意味論は等価。 HTTP ステータスでの判別ができない代わりに、 mcp-bundle のプロトコル準拠な拒否経路に乗っている。

## 受入基準テストの形

```bash
# scope を絞った token で scope 外 Tool を叩く
CREDENTIALS_FILE=/tmp/mcp-product-only-creds bash /tmp/mcp-oauth-test.sh search_orders
# 期待:
#   HTTP 200
#   JSON: {"jsonrpc":"2.0","id":3,"result":{"content":[{"type":"text","text":"Insufficient scope: mcp:order:read"}],"isError":true}}
```

PHPUnit 側は `expectException(Mcp\Exception\ToolCallException::class)` で確認する (11 Tool 全てに `testThrowsWhenScopeIsAbsent` 系を配置済み)。

## 認証エラー / Origin / Content-Type との関係

scope 拒否だけは Tool 呼び出し中に発生するため上記の制約に従う。 一方:

- **認証エラー (Bearer 不正 / 期限切れ)**: Api44 OAuth2 リソースサーバが firewall 層で処理 → **HTTP 401**。 mcp-bundle に到達しないので影響なし。
- **Origin 違反 / Content-Type 違反**: `OriginContentTypeListener` が `kernel.request` priority 16 で発火 → mcp-bundle / firewall 到達前に **HTTP 403 / 415** を返す。

つまり、 「HTTP 層で弾けるもの」 は HTTP 層で弾き、 「Tool 呼び出し中にしか判定できない scope 拒否」 だけが JSON-RPC level の応答になる。 結果として、 「Tool が呼ばれた = 認証も Origin も Content-Type も通った」という不変条件は壊れていない。
