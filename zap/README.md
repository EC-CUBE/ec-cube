# EC-CUBE Penetration Testing with OWASP ZAP

このツールは、サイトを実際に攻撃し、脆弱性が無いかを確認するツールです。
必ずローカル環境の Docker でのみ使用し、稼動中のサイトには決して使用しないでください。
意図せずデータが更新されたり、削除される場合があります。
テストは自己責任で実施し、株式会社イーシーキューブ及び、関連する開発コミュニティは一切の責任を負いかねますのであらかじめご了承ください。

GUI で手動探索する手順は [ドキュメント](https://doc4.ec-cube.net/penetration-testing/quick_start) を参照してください。
ここでは、Zest スクリプト (`scripts/*.zst`) を自動化フレームワークで実行する手順を説明します。

## 仕組み

- 1 つのターゲットは、画面操作を記録した Zest スクリプト `scripts/<target>.zst` です。
  ZAP の `sequence-activeScan` がこのスクリプトを再生し、各リクエストへ能動スキャンをかけます。
- ターゲットの一覧と個別の設定は `targets.json` で管理します。

  | 項目 | 内容 |
  |---|---|
  | `before_script` | スキャンの前に実行する Zest スクリプト (会員の作成など)。管理画面のターゲットで省略すると `admin_login.zst` |
  | `context` | 省略時は名前から決める。管理画面は `admin`、`mypage_*` は会員でログインした `front_login`、それ以外は `default` |
  | `thread_per_host` | 能動スキャンの並列数。状態が壊れやすいターゲットは 1 |

- 自動化プランは `automation/template.yml` から `generate_automation_config.sh` で生成します。
- スキャンポリシーは 2 種類です。

  | ポリシー | 内容 |
  |---|---|
  | `Sequence` (既定) | 能動スキャンを行う。ZAP 同梱の `Sequence` ポリシーから一部のルールを OFF にした `EC-CUBE` ポリシーを使う (下の「OFF にしているルール」) |
  | `Smoke` | `Sequence` の全ルールを OFF にしたもの。スクリプトの再生だけを確認する。画面の変更でスクリプトが壊れていないかを短時間で確かめるときに使う |

  どちらも `bin/prepare_zap.sh` が作ります。同梱のポリシーは読み取り専用のため、書き換えずに別のファイルとして作っています。

- 前提とするバンドル版プラグインは `plugins.txt` で管理します。4.4 対応版のリリースが無いため、各リポジトリの `4.4` ブランチから導入します。
- EC-CUBE は `APP_ENV=prod` で起動します (`docker-compose.owaspzap.ci.yml`)。

### OFF にしているルール

`EC-CUBE` ポリシーでは、次のルールを OFF にしています。

- 40026 Cross Site Scripting (DOM Based)
  - ZAP がブラウザ (headless の Firefox) でページを開き、URL の `#` 以降とクエリ文字列に入れた値で `alert()` が実行されるかを見るルールです。
    POST の本文は攻撃せず、シナリオのステップの Cookie や状態も引き継ぎません。
    EC-CUBE の JavaScript は `location.hash` / `location.search` を読んでおらず、検出の見込みがほぼありません。
  - ページ内のすべての `div` をクリックしては開き直すため、1 ステップで ZAP の待ち時間の上限 (600 秒。設定では変えられない) に
    達することがあります。上限に達してもルールの処理は止まらず、次のルール (Server Side Code Injection) の所要時間も延びていました。
  - 全ターゲットのスキャンで 1 件も検出しておらず、ログが残っていた 98 ステップだけで合計約 3 時間かかっていました。
    OFF にすると、`plugin_mailmagazine_send` は 3 時間 3 分から 18 分に、`admin_payment` は 11 分から 2 分 20 秒になりました (攻撃の件数はほぼ同じ)。

### 結果の判定

`bin/check_results.sh` が ZAP の実行ログから判定します。

- High のアラートがある、または自動化プランのジョブが失敗した場合は失敗です。
- 再生の失敗 (`passed = false` / `Assign: failed`) は、`Smoke` では失敗、`Sequence` では警告です。
  能動スキャンの攻撃が前のステップの状態を変え、以降の再生が失敗することがあるためです。
  ZAP の自動化プランは再生に失敗しても成功扱いになるため、ログで判定しています。
- `Smoke` では、再生で入力エラーの画面が返った POST を `Form error: <URL> <最初のエラーメッセージ>` としてログに出し
  (`scripts/report_form_errors.js`)、再生の失敗として数えます。入力エラーの画面は 200 で返るため ZAP は成功とみなしますが、
  確認画面などの後続の画面に攻撃が届きません。再生と攻撃は ZAP の中で区別できないため、攻撃を送らない `Smoke` に限っています。
- Path Traversal (ルール 6) のうち根拠 (evidence) が空のものは、High の件数から除外します (レポートには残ります)。
  URL 末尾の文字列をファイル名として送るチェックが、比較用の長いファイル名で入力欄の文字数上限に
  かかるだけで High を出すためです。ファイルを読み取れた本来の検知は根拠を持ちます。

## GitHub Actions で実行する

`.github/workflows/zaproxy.yml` を手動で実行します (Actions タブ → OWASP ZAP → Run workflow)。

| 入力 | 内容 |
|---|---|
| `policy` | `Sequence` または `Smoke` |
| `targets` | 実行するターゲットの正規表現 (target 名全体に一致させる)。例: `admin_tax\|mypage_order` |

ターゲットごとのレポート (`zap-<target>-report`: HTML レポート・実行ログ・生成したプラン) と、
全ターゲットのアラートをまとめた `all_alerts` をアーティファクトとして保存します。

## ローカルで実行する

レーン (`LANE=<n>`) ごとに EC-CUBE・PostgreSQL・ZAP を独立して起動するため、複数のターゲットを並列に実行できます。
1 レーンあたりメモリを 0.7GB 程度使います (10 レーンのスキャン中に計測)。レーンはホストへポートを公開しません。

必要なもの: Docker (Compose v2。override の `!reset` を使います)、bash、jq、git、flock

```bash
# EC-CUBE のイメージをビルドする (作業ツリーのコードで実行するため)
docker compose build ec-cube

# レーン 1〜4 を準備する (初回は 5 分程度。プラグインは zap/local/repos に clone する)
zap/local/setup.sh 1 2 3 4

# 1 つだけ実行する
LANE=1 zap/local/run.sh -p Smoke admin_tax
LANE=1 zap/local/run.sh admin_tax

# 全ターゲットを 4 レーンで実行する (正規表現で絞り込める)
zap/local/batch.sh -p Smoke -l "1 2 3 4"
zap/local/batch.sh -l "1 2 3 4" 'admin_product_.*'
```

- 結果は `zap/local/out/<policy>/<target>/` に出力します (`ZAP-Report-<target>.html`・`zap.log`・`plan.yml`・`alerts.json`・`access.log`・`attacks.tsv`)。
  CI では成果物 `zap-<target>-report` に同じものが入ります。
- `attacks.tsv` は、シナリオのリクエストごとに EC-CUBE が受けたリクエストの件数と応答の内訳です (`zap/bin/attack_counts.sh`)。
  `Sequence` では、パラメータがあるのに正常な応答 (2xx/3xx) が数件しか無いリクエストを「攻撃が届いていない可能性」として通知します。
  `batch.sh` の一覧は `zap/local/out/<policy>/summary.tsv` です。
  条件を変えて同じターゲットを実行するときは、`ZAP_OUT=<出力先>` で結果の上書きを避けられます。
- `run.sh` はターゲットごとに DB を準備直後の状態 (`eccubedb_clean`) へ戻します。前のターゲットが作ったデータの影響を受けません。
- レーンの操作は `LANE=<n> zap/local/compose.sh <サブコマンド>` で行えます (例: `logs ec-cube`)。
- Docker を再起動した後は `zap/local/setup.sh -r <レーン番号>...` で作り直してください。
  データとファイルの定期削除 (`delete_data.sh` / `delete_files.sh`) はコンテナ内のバックグラウンド処理のため、再起動で止まります。
- 片付けるときは `LANE=<n> zap/local/compose.sh down -v` を実行します。

## 既知の再生の失敗

`Sequence` で全ターゲットを実行すると、次のターゲットで再生の失敗 (警告) が出ます。いずれもスクリプトの不備ではありません。

- 能動スキャンの攻撃で前のステップの状態が変わるもの: 後続のステップが前提とするデータが変わる、または削除される
- `delete_data.sh` の定期削除と競合するもの: 支払方法 (`dtb_payment`)・テンプレート (`dtb_template`) は作成の 1 分後に削除されるため、
  能動スキャンに時間がかかると後続のステップで見つからない。タグ (`dtb_tag`) は作成日時を持たないため、10 秒ほど経ったものから削除する
- アプリケーションが 500 を返すもの: 想定外の入力で 500 になる箇所がある (本体は EC-CUBE/ec-cube#7192、プラグインは各リポジトリの issue)

再生に失敗したステップは、能動スキャンの攻撃が画面に届いていない可能性があります (例: `entry` のパスワード再設定は、
再設定用の URL が攻撃の前の再生で既に 404 になるため、再設定フォームは実質スキャンされていない)。

再生に成功していても、削除のリクエストは攻撃が処理まで届いていません (`attacks.tsv` で確認できます)。
パラメータが `_token` と `_method` だけで、`_method` への攻撃は Symfony が 400 を返すためです。

## シナリオが通らない画面を調べる

`zap/bin/coverage.sh` は、EC-CUBE のルートのうち、どのシナリオのリクエストも一致しないものを一覧にします。
シナリオを追加したときや、本体にルートが増えたときに確認してください。

```bash
LANE=1 zap/local/compose.sh exec -T ec-cube bin/console debug:router --format=json | zap/bin/coverage.sh -
```

- 「通る」はリクエストの URL とメソッドが一致したというだけで、能動スキャンの攻撃が効いたことは意味しません。
  パラメータの無いリクエストは攻撃されず、再生に失敗したステップも攻撃が届いていない可能性があります。
- 対象外にするルートは、理由と一緒に `zap/coverage_exclude.txt` に書きます (インストーラ、ストアのプラグイン管理)。

## スキャン用のパッチ (補助モード)

能動スキャンの攻撃で状態が消費されると (登録済み、削除済み、使用済みの URL、ログアウトなど)、後続の攻撃は
画面の処理まで届かずに弾かれます。`zap/patches/*.patch` は、こうした保存や削除の処理を無効にするパッチです
([doc4 の「テストが止まらないようにするための設定」](https://doc4.ec-cube.net/penetration-testing/testing/apply_patch) を 4.4 向けに作り直したもの)。

スキャンする対象がリリースするコードと変わり、保存の時点で起きるエラー (制約違反など) は検出できなくなるため、
既定のスキャンでは当てません。必要なときだけ選んで当てます。

| パッチ | 内容 |
|---|---|
| `mypage-delivery-keep` | お届け先の登録上限と削除を無効にする。`mypage_delivery`・`mypage_shopping_shipping` で、お届け先の新規登録への攻撃が上限到達の 4xx で弾かれなくなる |

doc4 のパッチのうち次のものは、当てても `attacks.tsv` の正常な応答が増えなかったため収録していません。

- 削除の処理を無効にするもの: 削除のリクエストのパラメータは `_token` と `_method` だけで、`_method` への攻撃は
  Symfony が 400 を返す。パスの ID は攻撃されないため、削除済みかどうかに関わらず攻撃は処理まで届かない
- カートを残すもの: `/shopping/checkout` のパラメータは CSRF トークンだけで、攻撃されない
- 会員登録で会員を保存しないもの: `entry` の有効化とパスワード再設定の再生が壊れる
- パスワード再設定のキーを使用済みにしないもの: 再設定フォームは攻撃の前の再生で既に 404 になる。
  直前の `POST /forgot` への攻撃がキーを作り直すためと推測している (未検証)

```bash
LANE=7 zap/local/patch.sh                            # すべて当てる
LANE=7 zap/local/patch.sh -R mypage-delivery-keep    # 1 つだけ外す
```

GitHub Actions では、`workflow_dispatch` の `patches` にパッチ名 (空白区切り) か `all` を指定します。
パッチは `restore.sh` では戻りません。外すか、`setup.sh -r` でレーンを作り直してください。

## シナリオを追加・修正するとき

- ZAP の GUI で記録した Zest スクリプトを `scripts/` に置き、`targets.json` に追加します。
- 画面操作の途中で変わる値 (CSRF トークン・作成したレコードの ID) は、直前のレスポンスから `ZestAssignStringDelimiters` で取り出して `{{変数}}` で参照します。
  - prefix / postfix には `{{変数}}` が展開されません。前後の値に依存する場合は `ZestAssignRegexDelimiters` を使い、
    `/shopping/shipping_edit/\d+/` や `refund_request/(?=\d+/edit)` のように正規表現で位置を絞ります。
  - 取り出しに失敗すると、URL やパラメータに `{{変数}}` がそのまま残って送られます。
    検索フォームのように CSRF トークンを持たないフォームへ未定義の変数を送ると、余分な項目として検証エラーになります。
  - `ZestActionPrint` は `sequence-activeScan` では出力されません。変数の中身を確かめたいときは、変数を埋めた URL へのリクエストを一時的に足すと、ログの `Response:` 行に出ます。
- 会員登録のように一意であるべき値は、`ZestAssignRandomInteger` で作った乱数を埋めます。
  `before_script` や能動スキャンの再生で同じ値が繰り返し送られ、2 回目以降が失敗するためです。
- `formIndex` はページ内の `<form>` の順番です。購入フローやマイページの一部の画面にはヘッダーの検索フォームがありません。
  ずれていても取り出しの失敗としてログに出ないことがあるため、実際の HTML で順番を確かめてください。
- 手で書く `ZestRequest` に `response` を含めないでください。`elementType` の無い `response` があると読み込みに失敗し、
  ログには `認識できないシーケンス: target` とだけ出ます。
- admin コンテキストのターゲットの `before_script` で会員としてログインすると、セッション ID が変わって以降の管理画面の
  リクエストが未ログイン扱いになります。会員の操作の後に管理画面を操作する場合は、管理画面にログインし直してください
  (`admin_create_refund_request.zst`)。
- `multipart/form-data` のリクエストは、boundary を小文字だけにし、boundary の行の前の改行を CRLF にしてください。
  ZAP は Content-Type を小文字にしてから boundary を取り出すため、ブラウザが付ける `----WebKitFormBoundary...` のように
  大文字を含むと本文を分割できず、能動スキャンの攻撃が 1 件も送られません (ZAP のログ `/home/zap/.ZAP/zap.log` に
  `VariantMultipartFormParameters.setParameter` の `IndexOutOfBoundsException` が出ます)。
  また ZAP は改行を CRLF として本文を分割するため、手で編集して LF だけになった箇所があると値の範囲がずれます。
  `zap/bin/lint_zst.sh` で確かめられます (CI でも実行します)。
- POST の値と送り先は、現在の画面がブラウザから送るものに揃えてください。古い版で記録したシナリオは、次のような食い違いで
  入力エラーになり、確認画面などに攻撃が届かないことがあります。`Smoke` の `Form error:` で見つけられます。
  - 後から増えた項目が無い: プラグインが足す必須項目 (会員登録の `entry[mailmaga_flg]`) や、hidden の項目
    (商品登録の `admin_product[faqs_rendered]`。無いと入力エラーの画面の再表示が 500 になる。EC-CUBE/ec-cube#7192)
  - 項目の形が変わった: 日付が年・月・日の 3 項目から 1 項目 (`single_text`) になった (受注登録のお届け日)
  - 記録した環境にしか無い ID を送っている: テンプレートなどはシナリオの中で作り、ID は応答から取り出す
  - JavaScript が送り先を変えている: メルマガのテンプレート選択は `/select/{id}` へ送る
- 商品画像の一時ファイル (`html/upload/temp_image`) は、`delete_files.sh` が 60 分残します。他の `html/` 配下の新しいファイルは
  十数秒以内に消すため、画像のアップロードを能動スキャンしている間に、次の商品登録のステップが使う一時ファイルが消えていました。
  なお一時ファイルは最初に登録が成功した攻撃で移動するため、商品登録のステップへの以降の攻撃は画像の入力エラーになります。
  登録の処理まで届く攻撃は、続く編集のステップで行われます。
- `mode=confirm` / `mode=complete` のように POST の値だけが違う遷移は、ZAP が同じノードとして扱います。
  `mode2=dummy` のようなダミーのパラメータで区別してください。
- 変更したら `Smoke` で再生を確かめてから、`Sequence` で実行してください。
