# /admin/mcp の認可境界と scope 検査の規約

MCP の「閲覧専用・領域限定」は、認証(firewall)だけでなくツール層の領域 scope 検査で担保する。この二層の前提を崩さないための規約。

## 認可の二層

1. firewall: `/admin/mcp` は OAuth2 Bearer を Member として認証する。通過条件は `ROLE_ADMIN`。
2. ツール層: 各ツール呼び出しは `ScopeEnforcingReferenceHandler` を必ず通り、`McpToolScopeMap` の領域 scope(例 `mcp:order:read`)を `ScopeChecker` で検査する。

領域・read の限定はツール層(2)でのみ効く。firewall(1)は `ROLE_ADMIN` しか見ない。

## 規約

- `/admin/mcp` 配下は MCP エンドポイント専用にする。通常の管理コントローラ(`ROLE_ADMIN` だけで通す URL)を置かない。
  - 理由: MCP トークンは Member(`ROLE_ADMIN`)として認証されるため、scope 非検査の URL を `/admin/mcp` 配下に置くと、read 専用トークン(例 `mcp:product:read` のみ)でも到達できてしまう。盗難トークンでも同様。
  - どうしても配下に足す場合は、そのコントローラでも領域 scope を検査する(`ROLE_ADMIN` だけに依存しない)。
- 新規ツールは必ず `McpToolScopeMap` に登録する。未登録ツールは `ScopeEnforcingReferenceHandler` が実行時 deny する(fail-closed)。

## 回帰ガード

クロス scope 拒否(ある領域の scope しか持たない token が他領域ツールを呼べない)は `McpScopeEnforcementIntegrationTest` で product / order / customer / plugin を実カーネル経由で縛る。領域ツールを増やしたら、この拒否テストにも 1 領域追加する。

## 補足(構造保証・運用)

- firewall 側で「最低 1 つの mcp scope を要求する」access_control を足すと、mcp scope を一切持たない Member トークンの到達を構造的に塞げる(領域・read の区別は引き続きツール層)。firewall 定義は Api44 側にあるため、そちらのタスクとして扱う。
- 盗難トークン対策は scope 封じ込め(本規約)に加え、最小権限のトークン発行・短い有効期限・失効・監査ログ(`McpAuditLogger`)で運用側が担う。
