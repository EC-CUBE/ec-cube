# Security — 認証・認可

EC-CUBE の認証（誰か）・認可（何を許すか）の中核部品。アクセス制御は
「ファイアウォール＋ロール＋Voter」の 3 層で、パスのプレフィックスで面的に守るのが基本。
UserProvider（ユーザー解決）・PasswordHasher（照合）・AuthorityVoter（URL 単位の権限）・
ログイン/ログアウトの各ハンドラを収める。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`security/SKILL.md`](../../../.claude/skills/security/SKILL.md)

## 主要ファイル

- `Core/User/CustomerProvider.php` / `Core/User/MemberProvider.php` — ログイン ID からユーザーを解決する UserProvider。ログイン成功時にパスワードを現行アルゴリズムへ自動再ハッシュ
- `PasswordHasher/PasswordHasher.php` — パスワードのハッシュ・照合（旧 2.11 未満からの移行照合を内包）
- `Voter/AuthorityVoter.php` — 管理画面内の URL 単位の権限制御（`AuthorityRole.deny_url` に一致で拒否）
- `Http/Authentication/EccubeAuthenticationSuccessHandler.php` — ログイン後リダイレクトを絶対 URL からパスへ正規化（オープンリダイレクト対策）。失敗・ログアウト用ハンドラも同ディレクトリ

> access_control（どのパスにどのロールを要求するか）は security.yaml 直書きではなく
> `src/Eccube/DependencyInjection/EccubeExtension.php` が動的生成する点に注意。
