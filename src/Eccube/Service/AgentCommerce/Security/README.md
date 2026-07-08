# AgentCommerce Security — 認証と署名

エージェントコマースのインバウンド認証（OAuth2）・scope 照合・メッセージ署名（RFC 9421）・署名鍵の管理を担うサブドメイン。
エージェントはブラウザセッションを持たないため、認証は OAuth2 アクセストークン、応答の真正性は HTTP Message Signatures で担保する。
公開鍵は discovery の `signing_keys[]`（JWK）として広告される。

- 📖 仕様（人間向け）: [README.html](./README.html)
- ⬆ 親: [AgentCommerce README](../README.md)

## 主要ファイル

- `AgentCommerceOAuth2Authenticator.php` — インバウンド OAuth2 検証 + scope×protocol 照合（トークン検証は eccube-api4 依存、未導入時は 503）
- `AgentCommerceScopeRegistry.php` — scope を `<protocol>:<capability>` 形式へ正準化（protocol 越境を禁止）
- `AgentCommerceMessageSignerInterface.php`（`UcpMessageSigner.php`）— メッセージ署名（RFC 9421 / EC P-256 / ES256）
- `KeyStoreInterface.php`（`FilesystemKeyStore.php`）— 署名鍵の永続化（標準は FS、0600/0700）
