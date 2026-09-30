---
name: eccube-permission-lanes
description: EC-CUBE 4.4 で Web サーバーと CLI の書き込み権限を分離した構成（レーン W / レーン S）を扱うときの規約。「権限を分離した環境で動かして」「www-data で書き込めない / Permission denied を直して」「eccube:doctor:permissions の結果を読んで」「どのユーザーで bin/console を実行すべきか」「デプロイ手順を書いて」「実行時にファイルへ書き込む処理を追加して」などと言われたとき、または src/Eccube/Service/Permission・src/Eccube/Command/Content・src/Eccube/Util/CacheUtil.php・src/Eccube/Kernel.php のディレクトリ定義・docker-compose.permission-lanes.yml・dockerbuild/docker-php-entrypoint を作成・編集するときに使用する。
---

# 権限レーン規約 — Web サーバーと CLI の書き込み分離（EC-CUBE 4.4）

**対象**: 実行時にファイルへ書き込むコード全般、`src/Eccube/Service/Permission/**`、`src/Eccube/Command/Content/**`、
`src/Eccube/Util/CacheUtil.php`、`src/Eccube/Kernel.php`、`docker-compose.permission-lanes.yml`、`dockerbuild/docker-php-entrypoint`
**前提**: Symfony 7.4 / PHP 8.2+。**分離は任意で、既定は分離しない構成**（Web サーバーと CLI が同じユーザー、または全体に書き込み権限を与える従来どおりの運用）。

> 目的: Web サーバーに最小限の書き込み権限しか与えずに運用できる状態を**壊さない**こと。
> 書き込み先の分類（レーン）を守り、レーン S へ書く操作は CLI に置き、Web サーバーのユーザー名を固定しない。
> 運用手順（本番のパーミッション設定・デプロイ・4.3 からの移行）の正本は https://doc4.ec-cube.net/permission 。本 Skill は開発時の判断基準に絞る。

## レーンの判断基準

判定は 1 つだけ: **リクエスト処理中に書き込みが発生するか**。発生するならレーン W、CLI へ移せるならレーン S。

| レーン | 所有者 | 対象（`PermissionRequirementProvider` の定義） |
|---|---|---|
| **W**（Web サーバー所有） | Web サーバー。CLI からは書かない | `var/runtime/{env}`（`eccube_runtime_dir`）・`var/log`・`var/sessions/{env}`・`html/upload/save_image`・`html/upload/temp_image`・`html/upload/refund_request/{save,temp}`・メンテナンスファイルの生成先（`ECCUBE_MAINTENANCE_FILE_PATH`） |
| **S**（CLI ユーザー所有） | CLI（SSH ログインユーザー）。Web サーバーは**読み取りのみ** | `var`（自体）・`var/build/{env}`・`var/cache/{env}`・`app/template`・`html/user_data`・`app/Plugin`・`app/PluginData`・`app/proxy`・`html/plugin`・`vendor`・`composer.json`・`composer.lock`・`app/keystore`・`.env` |

- **レーン定義の唯一の置き場所は `src/Eccube/Service/Permission/PermissionRequirementProvider.php`**。書き込み先を増やす・移すときは必ずここへ登録する。登録しないパスは `eccube:doctor:permissions` が診断しない。
- パスは設定パラメータ（`eccube_runtime_dir` / `kernel.logs_dir` / `kernel.build_dir` / `kernel.cache_dir` / `eccube_theme_app_dir` / `eccube_save_image_dir` 等）から解決する。`var/...` のリテラルをコードに書かない。
- 注意が要るもの: `app/PluginData` はプラグインが実行時に書く場合だけ Web サーバーの権限が要る（`optional`）。`app/proxy` は `ReloadSafeAttributeDriver` がプラグイン操作時に一時プロキシを書くため、プラグイン操作を CLI に寄せることが読み取り専用化の前提。`app/keystore` は秘密鍵がデプロイ成果物で、Web サーバーから書けると署名鍵の差し替えを許すためレーン S。ただし Web サーバーは**署名のために読む**必要があるので鍵は `0755` / `0644` で作る（`chgrp` できない環境があるため。所有者専用にするなら `ECCUBE_KEYSTORE_STRICT_PERMISSIONS=1`）。鍵は `eccube:keystore:generate` で事前配置し、実行時の自動生成は CLI を使えない環境向けのフォールバックとして残す。
- 共有グループ（CLI ユーザーと Web サーバーを同じグループに入れて `g+w` で共有する）を前提にしない。セキュリティポリシーでグループを作れない環境があるため、再現環境も作らない。

## 実装規約 — 実行時にレーン S へ書き込まない

- **リクエスト処理中に作るファイルは `eccube_runtime_dir` 配下**に置く（例: CSV の一時領域 `eccube_csv_temp_realdir` = `%eccube_runtime_dir%/eccube`）。`kernel.cache_dir` / `kernel.build_dir` は CLI が生成するビルド生成物の置き場で、リクエスト処理中は読み取りのみ。
- **レーン S を書き換える機能は CLI コマンドとして実装する**（Skill `eccube-command`）。管理画面側は `ECCUBE_RESTRICT_FILE_UPLOAD=1` の読み取り専用モードで代替コマンドを案内する（`eccube.yaml` の `eccube_restrict_file_upload_urls` にルート名 → コマンドを登録。レーン S へ書く管理画面ルートの全数は `tests/Eccube/Tests/EventListener/RestrictFileUploadListenerTest.php` が固定しているので、ルートを足したらそちらも更新する）。
- DB レコードとファイルを対で扱う処理は Service に置き、管理画面と CLI が同じ経路を通す（Skill `eccube-service`）。
- **`is_writable()` で「Web サーバーから書けるか」を判定しない**。返るのは実行ユーザー（CLI）から見た可否だけ。可否の推定は `PathOwnership` / `PermissionDiagnostic` に集約されている（所有者 uid・gid・パーミッションビットから owner / group / other の一致する 1 クラスだけを見る）。
- **`mkdir()` のモードは `0755`**。`0777` を書かない。`umask()` を呼ばない（`ECCUBE_UMASK` で運用側が決め、`apply_umask()` は `bin/console` / `index.php` が呼ぶ）。
- **書き込み失敗を握りつぶさない**。`Symfony\Component\Filesystem` の `IOException` は `ContentWriteException::forWrite()` / `forRemove()` に包んで投げ、コマンド側は `reportWriteFailure()` で `eccube:doctor:permissions` を案内して `1` を返す。
- キャッシュの削除は `Eccube\Util\CacheUtil` を通す。`clearCache()` は `kernel.terminate` で実行され、build ディレクトリへ書けない場合は実行時キャッシュだけ削除して `eccube:cache:build` を案内する（`clearRuntimeCache()`）。`clearTwigCache()` は build 側の twig も削除する（prod では build 側が読み取り専用キャッシュとして優先されるため）。リクエスト処理中のコードから `cache:clear` を直接実行しない。
- 子プロセスで `bin/console` を実行するときは cwd に `kernel.project_dir` を渡し `setTimeout(null)` にする（`PluginCommandTrait::clearCache()` / `EnvSetCommand` の形）。
- **Web サーバーのユーザー名（`www-data` 等）をコード・設定に固定しない**。環境ごとに異なるため、`WebServerUserResolver` が Web でしか生成されないファイル（`var/sessions/{env}` → `html/upload/temp_image` 等 → `var/log`）の所有者から実測する。実行ユーザーは `posix_geteuid()`（`getmyuid()` はスクリプトファイルの所有者を返すため不可）。

## CLI の実行ユーザーの選び方

**操作対象のレーンで決める。** 分離した構成では 1 つのユーザーですべてを実行することはできない。

| 操作 | 実行ユーザー | 例 |
|---|---|---|
| レーン S を書く（ビルド生成・コンテンツ・プラグイン・`.env`・鍵） | CLI ユーザー | `bin/console eccube:cache:build` / `eccube:page:apply` / `eccube:plugin:install` / `eccube:env:set` / `eccube:keystore:generate` |
| レーン W を書く（実行時キャッシュの削除） | Web サーバーのユーザー | `sudo -u www-data bin/console cache:pool:clear --all` |

- Docker の再現環境では `docker compose exec -u eccube`（レーン S）/ `-u www-data`（レーン W）で使い分ける（起動手順は AGENTS.md「権限を分離した環境」）。
- **root で実行して乗り切らない。** 診断は uid 0 を常に可とみなすので通ってしまうが、root 所有で作られたファイルは以後どちらのユーザーからも書けなくなる。
- **`cache:clear --no-warmup` を使わない。** `cache:clear` 自体は build と cache の双方がレーン S なので CLI ユーザーなら成功するが、`--no-warmup` ではコンパイル済みコンテナが再生成されず、次のリクエストで Web サーバーが `Unable to write in the "cache" directory` で 500 になる。復旧は `eccube:cache:build` のみ。
- **`eccube:plugin:*` の後は `eccube:cache:build` を別途実行する。** `PluginCommandTrait::clearCache()` は `cache:clear --no-warmup` を子プロセスで実行するだけで、実行中のプロセスは自身のコンパイル済みコンテナを作り直せない（作り直すと親のコンテナディレクトリが消え `console.terminate` で異常終了する）。
- 分離した構成は **`APP_ENV=prod` 固定**。dev はリクエスト処理中にコンテナを再生成するため `Kernel::buildContainer()` が `var/cache` と `var/build`（レーン S）への書き込みを要求して 500 になる。
- `var/log` はレーン W なので CLI からログファイルへ書けない。ログ出力の失敗で本来のエラーが隠れるため、分離した構成では `ECCUBE_CLI_LOG_TO_FILE=0` を設定して CLI のログをコンソールへ寄せる。
- Web サーバーのユーザーで `bin/console` を実行しても、レーン S を書く操作は `ContentWriteException` で失敗する（終了コード `1`）。エラーメッセージが案内するとおり、実行ユーザーを変える。

## `eccube:doctor:permissions` の読み方

3 レーンの期待値と実際の所有者・パーミッションを突き合わせる。判定は **uid / gid / パーミッションビットからの推定**で、補助グループ・ACL・SELinux は考慮しない。

- 終了コード: `0` = NG なし / `1` = NG あり / `2` = オプション不正。**WARN は終了コードに影響しない。**
- 機械可読は `--format=json`（`web_server` / `cli` / `summary.{ok,warn,ng}` / `findings[]`）。CI で「分離できている」を固定するときは NG 0 件に加えて **`.web_server != null` と `.cli.uid != .web_server.uid`** まで見る。セッションファイルが無いと Web サーバーの uid を特定できず全件 WARN・終了コード `0` で見かけ上成功する。
- 「Web サーバーの実行ユーザーを特定できませんでした」は、Web でしか生成されないファイルがまだ無い状態。サイトへ一度アクセス（`curl -s -o /dev/null http://.../`）してから再実行する。
- 「Web サーバーと診断の実行ユーザーが同じ uid」の note は、共有ホスティング（suexec 等）では分離不可を意味する。別ユーザーで運用しているはずの環境では、判定に使ったファイルが CLI で生成された疑い。

| 判定 | 意味 | 対処 |
|---|---|---|
| NG `Web サーバーから書き込み可能です (想定: 読み取りのみ)` | レーン S を Web サーバーが書ける | 所有者を CLI ユーザーへ変え、group / other の `w` を落とす |
| NG `任意のローカルユーザーから書き込めます` | レーン S が world-writable。Web サーバーの uid が不明でも NG | `ECCUBE_UMASK` を空にし、`chmod o-w` |
| NG `Web サーバーから書き込めません` | レーン W を Web サーバーが書けない | 所有者を Web サーバーのユーザーへ |
| NG `Web サーバーから到達できません` | 祖先ディレクトリに `x` が無い（`/home/{user}` が `0700` 等）。ヒントに塞いでいる祖先のパスと権限が出る | 祖先へ `x` を付与するか所有者を変える |
| NG `存在しません` | 必須パスが無い | 作成する（`optional` なパスは「未作成」で OK） |
| WARN `Web サーバーは読み取りのみですが, 診断の実行ユーザーからも書き込めません` | レーン S の所有者が診断の実行ユーザーと違う | そのパスを書くコマンドは所有者のユーザーで実行する |
| WARN `事前コンパイルされていないテンプレートがリクエスト処理中にコンパイルされています` | `var/runtime/{env}/twig` に生成物がある = `eccube:cache:build` の warmup 漏れ（prod のみ） | `eccube:cache:build` を実行する |
| WARN `Web サーバーから書き込めますが, 任意のローカルユーザーからも書き込めます` | レーン W が world-writable | 権限を分離できる環境では `ECCUBE_UMASK` を空にする |
| WARN `祖先ディレクトリの権限を確認できないため到達可否を判定できません` | `open_basedir` 等で上位を参照できない | 到達不能とは断定していない。手動で確認する |

## キャッシュの 3 分割とテンプレート更新後の操作

| ディレクトリ | レーン | 内容 | 生成 |
|---|---|---|---|
| `var/build/{env}`（`kernel.build_dir`） | S | コンパイル済みコンテナ・ルーティング・メタデータ・prod の twig | `eccube:cache:build` |
| `var/cache/{env}`（`kernel.cache_dir`） | S | 翻訳カタログ・HTMLPurifier | 同上 |
| `var/runtime/{env}`（`eccube_runtime_dir`） | W | cache pool・twig のフォールバック・CSV 一時領域・MCP セッション・プロファイラ | リクエスト処理中 |

- prod の twig は `[build/twig（読み取り専用）, runtime/twig]` の 2 層。build に無いテンプレートだけが実行時に runtime へコンパイルされる。dev は `auto_reload` が有効で従来どおり単層。
- テンプレートを更新したら prod では `eccube:cache:build`。`eccube:page:apply` 等のコンテンツ操作は自動で削除を試み、build へ書けなければ本処理を完了させたうえで終了コード `3` と `eccube:cache:build` の案内を返す。
- 実行時キャッシュ（cache pool）の削除は `cache:pool:clear --all` を Web サーバーのユーザーで。`var/runtime/{env}/twig` は `cache:pool:clear` の対象外なので、消すなら Web サーバーのユーザーで `rm -rf` するか管理画面のキャッシュ管理から。

## よくある間違い

- ❌ リクエスト処理中に `app/template` / `html/user_data` / `.env` / `var/build` へ書く → ✅ CLI コマンド化し、管理画面は読み取り専用モードで代替コマンドを案内する
- ❌ 実行時の一時ファイルを `kernel.cache_dir` や `var/` 直下に作る → ✅ `eccube_runtime_dir` 配下（レーン W）に置く
- ❌ `cache:clear --no-warmup` を分離した構成で実行する → ✅ `eccube:cache:build`。コンテナが再生成されず次のリクエストで 500 になる
- ❌ `eccube:plugin:*` の終了コード `0` で完了扱いにする → ✅ 続けて `eccube:cache:build`。`3` は「完了したが手動操作が必要」
- ❌ `is_writable()` で Web サーバーから書けるかを判定する → ✅ 実行ユーザーの可否しか分からない。`PermissionDiagnostic` に任せる
- ❌ `mkdir($dir, 0777)` / `umask(0000)` を書く → ✅ `0755`。umask は `ECCUBE_UMASK` で運用側が決める
- ❌ 書き込み先を増やしたのに `PermissionRequirementProvider` へ登録しない → ✅ 登録しないパスは診断されない
- ❌ `www-data` をコード・設定・テストに固定する → ✅ 環境ごとに異なる。所有者から実測し、テストは所有者 +1 等の相対値で書く
- ❌ WARN だけの診断結果を「分離できている」と読む → ✅ `.web_server != null` と uid の不一致まで確認する
- ❌ root で `bin/console` を実行して権限エラーを回避する → ✅ 対象レーンの所有者で実行する。root 所有のファイルは双方から書けなくなる

## 実行・確認方法

QA ツール（PHPUnit / PHPStan / PHP-CS-Fixer）の実行方法は AGENTS.md「開発コマンド」を参照。

```bash
# 分離した構成の再現 (--build 必須, DB サーバー必須。詳細は AGENTS.md「権限を分離した環境」)
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.pgsql.yml \
  -f docker-compose.permission-lanes.yml up -d --build --wait
curl -s -o /dev/null http://127.0.0.1:8080/
docker compose exec -u eccube ec-cube bin/console eccube:doctor:permissions
docker compose exec -u eccube ec-cube bin/console eccube:doctor:permissions --format=json \
  | jq -e '.summary.ng == 0 and .web_server != null and .cli.uid != .web_server.uid'

# 判定ロジックの単体テスト
vendor/bin/phpunit tests/Eccube/Tests/Service/Permission tests/Eccube/Tests/Command/DoctorPermissionsCommandTest.php
```

- 分離した構成の結合検証は CI ジョブ `permission-lanes-test`（`.github/workflows/permission-lanes-test.yml`）が行う。レーンの境界を両方向から、診断の判定、読み取り専用モードの応答、CLI からのプラグイン導入までを固定する。Web サーバーと CLI が別 uid で Apache（mod_php）経由でしか再現しない挙動（`PassEnv` に無い環境変数が Web 側にだけ届かない等）は単体テストでは検出できないため、書き込み先やレーン定義を変えたときはこのジョブを通す。
- 既定モードと分離モードを切り替えるときは `docker compose ... down -v` でレーン W のボリュームを作り直す。切り替え前の `www-data` の uid で作られたディレクトリが残り、切り替え後の Web サーバーから書けなくなる。`html/upload/**` はホスト側でも `www-data` の uid 所有になるため、戻すときは `sudo chown -R $(id -u):$(id -g) html/upload`。

---

実装・改修後は、Skill `eccube-command`（終了コードと入出力）・`eccube-service`（DB とファイルの対）・`eccube-review-responsibility` で点検すること。
