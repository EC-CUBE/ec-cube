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
  | `Sequence` (既定) | ZAP 同梱のポリシー。能動スキャンを行う |
  | `Smoke` | `Sequence` の全ルールを OFF にしたもの。スクリプトの再生だけを確認する。画面の変更でスクリプトが壊れていないかを短時間で確かめるときに使う |

- 前提とするバンドル版プラグインは `plugins.txt` で管理します。4.4 対応版のリリースが無いため、各リポジトリの `4.4` ブランチから導入します。
- EC-CUBE は `APP_ENV=prod` で起動します (`docker-compose.owaspzap.ci.yml`)。

### 結果の判定

`bin/check_results.sh` が ZAP の実行ログから判定します。

- High のアラートがある、または自動化プランのジョブが失敗した場合は失敗です。
- 再生の失敗 (`passed = false` / `Assign: failed`) は、`Smoke` では失敗、`Sequence` では警告です。
  能動スキャンの攻撃が前のステップの状態を変え、以降の再生が失敗することがあるためです。
  ZAP の自動化プランは再生に失敗しても成功扱いになるため、ログで判定しています。
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

- 結果は `zap/local/out/<policy>/<target>/` に出力します (`ZAP-Report-<target>.html`・`zap.log`・`plan.yml`・`alerts.json`)。
  `batch.sh` の一覧は `zap/local/out/<policy>/summary.tsv` です。
- `run.sh` はターゲットごとに DB を準備直後の状態 (`eccubedb_clean`) へ戻します。前のターゲットが作ったデータの影響を受けません。
- レーンの操作は `LANE=<n> zap/local/compose.sh <サブコマンド>` で行えます (例: `logs ec-cube`)。
- Docker を再起動した後は `zap/local/setup.sh -r <レーン番号>...` で作り直してください。
  データとファイルの定期削除 (`delete_data.sh` / `delete_files.sh`) はコンテナ内のバックグラウンド処理のため、再起動で止まります。
- 片付けるときは `LANE=<n> zap/local/compose.sh down -v` を実行します。

## 既知の再生の失敗

`Sequence` で全ターゲットを実行すると、次のターゲットで再生の失敗 (警告) が出ます。いずれもスクリプトの不備ではありません。

- 能動スキャンの攻撃で前のステップの状態が変わるもの: 後続のステップが前提とするデータが変わる、または削除される
- `delete_data.sh` の定期削除と競合するもの: 支払方法 (`dtb_payment`)・テンプレート (`dtb_template`) は作成の 6 秒後に削除されるため、
  スキャンに時間がかかると後続のステップで見つからない
- アプリケーションが 500 を返すもの: 想定外の入力で 500 になる箇所がある (本体は EC-CUBE/ec-cube#7192、プラグインは各リポジトリの issue)

`Smoke` では、Coupon44 の不具合 (クーポン適用後の購入画面が 500) により `plugin_coupon_guest_shopping` だけが失敗します
(EC-CUBE/coupon-plugin#198)。

## シナリオを追加・修正するとき

- ZAP の GUI で記録した Zest スクリプトを `scripts/` に置き、`targets.json` に追加します。
- 画面操作の途中で変わる値 (CSRF トークン・作成したレコードの ID) は、直前のレスポンスから `ZestAssignStringDelimiters` で取り出して `{{変数}}` で参照します。
  - prefix / postfix には `{{変数}}` が展開されません。
  - `ZestActionPrint` は `sequence-activeScan` では出力されません。変数の中身を確かめたいときは、変数を埋めた URL へのリクエストを一時的に足すと、ログの `Response:` 行に出ます。
- 会員登録のように一意であるべき値は、`ZestAssignRandomInteger` で作った乱数を埋めます。
  `before_script` や能動スキャンの再生で同じ値が繰り返し送られ、2 回目以降が失敗するためです。
- `formIndex` はページ内の `<form>` の順番です。購入フローの画面にはヘッダーの検索フォームがありません。
- `mode=confirm` / `mode=complete` のように POST の値だけが違う遷移は、ZAP が同じノードとして扱います。
  `mode2=dummy` のようなダミーのパラメータで区別してください。
- 変更したら `Smoke` で再生を確かめてから、`Sequence` で実行してください。
